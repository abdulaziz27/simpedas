<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NonTeachingStaff;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NonTeachingStaffController extends Controller
{
    private function getStaffQuery()
    {
        $user = Auth::user();
        $query = NonTeachingStaff::query();

        if ($user->hasRole('admin_sekolah')) {
            $query->where('school_id', $user->school_id);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $query = $this->getStaffQuery();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('full_name', 'like', "%{$search}%");
        }

        $staff = $query->with('school')->latest()->paginate(10);
        return view('admin.non-teaching-staff.index', compact('staff'));
    }

    public function create()
    {
        $schools = School::all();
        return view('admin.non-teaching-staff.create', compact('schools'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'nip_nik' => 'nullable|string|max:20',
            'nuptk' => 'nullable|string|max:20',
            'birth_place' => 'required|string|max:100',
            'birth_date' => 'required|date',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'religion' => 'required|string|max:50',
            'address' => 'required|string',
            'position' => 'required|string|max:100',
            'education_level' => 'required|string|max:100',
            'employment_status' => 'required|string|max:20',
            'rank' => 'nullable|string|max:50',
            'tmt' => 'nullable|date',
            'status' => 'required|in:Aktif,Tidak Aktif',
            'school_id' => 'required_if:auth()->user()->hasRole("admin_dinas"),exists:schools,id',
        ]);
        if ($user->hasRole('admin_sekolah')) {
            $data['school_id'] = $user->school_id;
        }
        NonTeachingStaff::create($data);
        return redirect()->route(Auth::user()->hasRole('admin_sekolah') ? 'sekolah.non-teaching-staff.index' : 'admin.non-teaching-staff.index')
            ->with('success', 'Data staf berhasil ditambahkan.');
    }

    public function show(NonTeachingStaff $nonTeachingStaff)
    {
        $this->authorizeAccess($nonTeachingStaff);
        return view('admin.non-teaching-staff.show', compact('nonTeachingStaff'));
    }

    public function edit(NonTeachingStaff $nonTeachingStaff)
    {
        $this->authorizeAccess($nonTeachingStaff);
        $schools = School::all();
        return view('admin.non-teaching-staff.edit', compact('nonTeachingStaff', 'schools'));
    }

    public function update(Request $request, NonTeachingStaff $nonTeachingStaff)
    {
        $this->authorizeAccess($nonTeachingStaff);

        $data = $request->validate([
            'full_name' => 'sometimes|nullable|string|max:255',
            'nip_nik' => 'nullable|string|max:20',
            'nuptk' => 'nullable|string|max:20',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'religion' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'position' => 'nullable|string|max:100',
            'education_level' => 'nullable|string|max:100',
            'employment_status' => 'nullable|string|max:20',
            'rank' => 'nullable|string|max:50',
            'tmt' => 'nullable|date',
            'status' => 'nullable|in:Aktif,Tidak Aktif',
            'school_id' => 'nullable|exists:schools,id',
        ]);

        if (Auth::user()->hasRole('admin_sekolah')) {
            $data['school_id'] = Auth::user()->school_id;
        }

        $nonTeachingStaff->update($data);

        return redirect()->route(Auth::user()->hasRole('admin_sekolah') ? 'sekolah.non-teaching-staff.show' : 'admin.non-teaching-staff.show', $nonTeachingStaff->id)
            ->with('success', 'Data staf berhasil diperbarui.');
    }

    public function destroy(NonTeachingStaff $nonTeachingStaff)
    {
        $this->authorizeAccess($nonTeachingStaff);
        $nonTeachingStaff->delete();
        return redirect()->route(Auth::user()->hasRole('admin_sekolah') ? 'sekolah.non-teaching-staff.index' : 'admin.non-teaching-staff.index')
            ->with('success', 'Data staf berhasil dihapus.');
    }

    /**
     * Print non-teaching staff data
     */
    public function print(NonTeachingStaff $nonTeachingStaff)
    {
        $this->authorizeAccess($nonTeachingStaff);
        return view('admin.non-teaching-staff.print', compact('nonTeachingStaff'));
    }

    private function authorizeAccess(NonTeachingStaff $staff)
    {
        $user = Auth::user();
        if ($user->hasRole('admin_sekolah') && $staff->school_id !== $user->school_id) {
            abort(404);
        }
    }
}
