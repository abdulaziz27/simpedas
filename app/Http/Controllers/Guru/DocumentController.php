<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\TeacherDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;
        $documents = $teacher ? $teacher->documents()->orderBy('uploaded_at', 'desc')->get() : collect();
        return view('guru.documents', compact('documents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB Max
            'document_name' => 'required|string|max:255'
        ]);

        $teacher = Auth::user()->teacher;
        if (!$teacher) {
            return back()->with('error', 'Profil guru tidak ditemukan.');
        }

        $file = $request->file('document');
        $originalName = $request->input('document_name') . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('teacher_documents/' . $teacher->id, $originalName, 'public');

        $teacher->documents()->create([
            'document_name' => $request->input('document_name'),
            'document_type' => 'personal',
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'uploaded_at' => now(),
        ]);

        return back()->with('success', 'Dokumen berhasil diunggah.');
    }

    public function download(TeacherDocument $document)
    {
        // Authorization check
        if ($document->teacher_id !== Auth::user()->teacher->id) {
            abort(403);
        }

        return Storage::disk('public')->download($document->file_path, $document->document_name . '.' . pathinfo($document->file_path, PATHINFO_EXTENSION));
    }

    public function destroy(TeacherDocument $document)
    {
        // Authorization check
        if ($document->teacher_id !== Auth::user()->teacher->id) {
            abort(403);
        }

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }
}
