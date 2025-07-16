<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NonTeachingStaff;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $query = NonTeachingStaff::query();

        // Apply filters
        if ($request->filled('sekolah')) {
            $query->where('school_id', $request->sekolah);
        }

        if ($request->filled('jabatan')) {
            $query->where('position', $request->jabatan);
        }

        if ($request->filled('status')) {
            $query->where('employment_status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('nip', 'like', '%' . $request->search . '%');
        }

        $staff = $query->paginate(9);

        return view('admin.staff.index', compact('staff'));
    }

    public function create()
    {
        return view('admin.staff.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'required|string|unique:non_teaching_staff',
            'school_id' => 'required|exists:schools,id',
            'position' => 'required|string',
            'employment_status' => 'required|string',
            'birth_place' => 'required|string',
            'birth_date' => 'required|date',
            'gender' => 'required|in:L,P',
            'address' => 'required|string',
            'email' => 'required|email|unique:non_teaching_staff',
        ]);

        NonTeachingStaff::create($validated);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Data pegawai berhasil ditambahkan');
    }

    public function show(NonTeachingStaff $staff)
    {
        return view('admin.staff.show', compact('staff'));
    }

    public function edit(NonTeachingStaff $staff)
    {
        return view('admin.staff.edit', compact('staff'));
    }

    public function update(Request $request, NonTeachingStaff $staff)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'required|string|unique:non_teaching_staff,nip,' . $staff->id,
            'school_id' => 'required|exists:schools,id',
            'position' => 'required|string',
            'employment_status' => 'required|string',
            'birth_place' => 'required|string',
            'birth_date' => 'required|date',
            'gender' => 'required|in:L,P',
            'address' => 'required|string',
            'email' => 'required|email|unique:non_teaching_staff,email,' . $staff->id,
        ]);

        $staff->update($validated);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Data pegawai berhasil diperbarui');
    }

    public function destroy(NonTeachingStaff $staff)
    {
        $staff->delete();

        return redirect()->route('admin.staff.index')
            ->with('success', 'Data pegawai berhasil dihapus');
    }
} 