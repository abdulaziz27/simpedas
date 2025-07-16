<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $teacher = Auth::user()->teacher;
        if (!$teacher) abort(404);
        return view('guru.profile', compact('teacher'));
    }

    public function edit()
    {
        $teacher = Auth::user()->teacher;
        if (!$teacher) abort(404);
        return view('guru.edit', compact('teacher'));
    }

    public function update(Request $request)
    {
        $teacher = Auth::user()->teacher;
        if (!$teacher) abort(404);

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'nuptk' => 'nullable|string|max:20',
            'nip' => 'nullable|string|max:20',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'gender' => 'required|string|in:Laki-laki,Perempuan',
            'religion' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'education_level' => 'nullable|string|max:50',
            'education_major' => 'nullable|string|max:100',
            'subjects' => 'nullable|string',
            'employment_status' => 'nullable|string',
            'rank' => 'nullable|string',
            'position' => 'nullable|string',
            'education_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB Max
        ]);

        // Handle education document upload
        if ($request->hasFile('education_document')) {
            $file = $request->file('education_document');
            $filename = 'education_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('teacher_documents/' . $teacher->id, $filename, 'public');

            // Save to teacher_documents table
            $teacher->documents()->create([
                'document_name' => 'Dokumen Pendidikan - ' . ($validated['education_level'] ?? 'Tidak diketahui'),
                'document_type' => 'education',
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'uploaded_at' => now(),
            ]);
        }

        $teacher->update($validated);

        return redirect()->route('guru.profile.show')->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $teacher = Auth::user()->teacher;
        if (!$teacher) abort(404);

        // Delete old photo if exists
        if ($teacher->photo) {
            Storage::disk('public')->delete($teacher->photo);
        }

        $path = $request->file('photo')->store('teacher_photos', 'public');
        $teacher->update(['photo' => $path]);

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }

    public function print()
    {
        $teacher = Auth::user()->teacher;
        if (!$teacher) abort(404);
        return view('guru.print', compact('teacher'));
    }

    public function destroy()
    {
        $teacher = Auth::user()->teacher;
        if (!$teacher) abort(404);

        // Delete associated documents and files
        foreach ($teacher->documents as $document) {
            Storage::disk('public')->delete($document->file_path);
            $document->delete();
        }

        // Delete profile photo
        if ($teacher->photo) {
            Storage::disk('public')->delete($teacher->photo);
        }

        // Delete the teacher record
        $teacher->delete();

        // Logout the user since their teacher profile is deleted
        Auth::logout();

        return redirect()->route('login')->with('success', 'Profil berhasil dihapus.');
    }
}
