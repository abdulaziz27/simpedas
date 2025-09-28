<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = User::with(['roles', 'school']);

        // Filter berdasarkan role user yang login
        if ($user->hasRole('admin_sekolah')) {
            // Admin sekolah hanya bisa lihat guru di sekolahnya
            $query->where('school_id', $user->school_id);
            $query->whereHas('roles', function ($q) {
                $q->where('name', 'guru');
            });
        }

        // Filter berdasarkan role
        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        // Filter roles berdasarkan user yang login
        if ($user->hasRole('admin_sekolah')) {
            $roles = Role::where('name', 'guru')->get();
        } else {
            $roles = Role::all();
        }

        $schools = School::all();

        return view('admin.user-management.index', compact('users', 'roles', 'schools'));
    }

    public function create()
    {
        $user = Auth::user();
        $roles = Role::all();
        $schools = School::all();

        // Filter role berdasarkan user yang login
        if ($user->hasRole('admin_sekolah')) {
            $roles = $roles->where('name', 'guru');
        }

        return view('admin.user-management.create', compact('roles', 'schools'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Log untuk debugging
        \Log::info('Creating user with data:', $request->all());

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'school_id' => [
                'required_if:role,admin_sekolah,guru',
                'nullable',
                'exists:schools,id'
            ],
        ]);

        if ($validator->fails()) {
            \Log::error('Validation failed:', $validator->errors()->toArray());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validasi role berdasarkan user yang login
        if ($user->hasRole('admin_sekolah')) {
            if ($request->role !== 'guru') {
                return redirect()->back()
                    ->withErrors(['role' => 'Admin sekolah hanya dapat membuat user dengan role guru'])
                    ->withInput();
            }
            $request->merge(['school_id' => $user->school_id]);
        }

        // Set school_id berdasarkan role
        if ($request->role === 'admin_dinas') {
            $request->merge(['school_id' => null]); // Admin dinas tidak terikat sekolah
        }

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'school_id' => $request->school_id,
        ];

        \Log::info('User data to be created:', $userData);

        try {
            $newUser = User::create($userData);
            $newUser->assignRole($request->role);

            $routeName = $user->hasRole('admin_sekolah') ? 'sekolah.user-management.index' : 'dinas.user-management.index';

            return redirect()->route($routeName)
                ->with('success', 'User berhasil ditambahkan.');
        } catch (\Exception $e) {
            \Log::error('Error creating user: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat membuat user: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(User $user)
    {
        $this->authorizeAccess($user);
        return view('admin.user-management.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authorizeAccess($user);

        // Cek apakah user adalah admin dinas pertama
        if ($this->isFirstAdminDinas($user)) {
            return redirect()->back()
                ->with('error', 'Admin dinas pertama tidak dapat diedit.');
        }

        $roles = Role::all();
        $schools = School::all();

        $currentUser = Auth::user();
        if ($currentUser->hasRole('admin_sekolah')) {
            $roles = $roles->where('name', 'guru');
        }

        return view('admin.user-management.edit', compact('user', 'roles', 'schools'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeAccess($user);

        // Cek apakah user adalah admin dinas pertama
        if ($this->isFirstAdminDinas($user)) {
            return redirect()->back()
                ->with('error', 'Admin dinas pertama tidak dapat diubah.');
        }

        $currentUser = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'role' => 'required|exists:roles,name',
            'school_id' => [
                'required_if:role,admin_sekolah,guru',
                'nullable',
                'exists:schools,id'
            ],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validasi role berdasarkan user yang login
        if ($currentUser->hasRole('admin_sekolah')) {
            if ($request->role !== 'guru') {
                return redirect()->back()
                    ->withErrors(['role' => 'Admin sekolah hanya dapat mengubah user menjadi role guru'])
                    ->withInput();
            }
            $request->merge(['school_id' => $currentUser->school_id]);
        }

        // Set school_id berdasarkan role
        if ($request->role === 'admin_dinas') {
            $request->merge(['school_id' => null]); // Admin dinas tidak terikat sekolah
        }

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'school_id' => $request->school_id,
        ];

        // Update password jika diisi
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        // Update role
        $user->syncRoles([$request->role]);

        $routeName = $currentUser->hasRole('admin_sekolah') ? 'sekolah.user-management.index' : 'dinas.user-management.index';

        return redirect()->route($routeName)
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $this->authorizeAccess($user);

        // Cek apakah user adalah admin dinas pertama
        if ($this->isFirstAdminDinas($user)) {
            return redirect()->back()
                ->with('error', 'Admin dinas pertama tidak dapat dihapus.');
        }

        // Cek apakah user mencoba menghapus dirinya sendiri
        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        try {
            // Hapus roles terlebih dahulu
            $user->syncRoles([]);

            // Hapus user
            $deleted = $user->delete();

            if (!$deleted) {
                return redirect()->back()
                    ->with('error', 'Gagal menghapus user. Silakan coba lagi.');
            }

            $routeName = Auth::user()->hasRole('admin_sekolah') ? 'sekolah.user-management.index' : 'dinas.user-management.index';

            return redirect()->route($routeName)
                ->with('success', 'User berhasil dihapus.');
        } catch (\Exception $e) {
            \Log::error('Error deleting user: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus user. Silakan coba lagi.');
        }
    }

    private function authorizeAccess(User $targetUser)
    {
        $currentUser = Auth::user();

        if ($currentUser->hasRole('admin_sekolah')) {
            // Admin sekolah hanya bisa akses guru di sekolahnya
            if (!$targetUser->hasRole('guru') || $targetUser->school_id !== $currentUser->school_id) {
                abort(403, 'Anda tidak memiliki akses ke user ini.');
            }
        }
    }

    private function isFirstAdminDinas(User $user)
    {
        if (!$user->hasRole('admin_dinas')) {
            return false;
        }

        // Cek apakah user ini adalah admin dinas pertama (berdasarkan ID terkecil)
        $firstAdminDinas = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin_dinas');
        })->orderBy('id')->first();

        return $firstAdminDinas && $firstAdminDinas->id === $user->id;
    }
}
