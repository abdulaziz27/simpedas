@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#125047] py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="flex items-center space-x-2 text-white mb-6">
            <a href="{{ route('home') }}" class="hover:text-green-300">Dashboard</a>
            <span class="text-gray-300">&gt;</span>
            <a href="{{ route('dinas.articles.index') }}" class="hover:text-green-300">Manajemen Artikel</a>
            <span class="text-gray-300">&gt;</span>
            <span class="border-b-2 border-white">Edit Artikel</span>
        </nav>

        {{-- Form Card --}}
        <div class="bg-white rounded-xl p-8 shadow-lg">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Artikel</h1>

            <form action="{{ route('dinas.articles.update', $article) }}" method="POST" enctype="multipart/form-data" id="article-form">
                @csrf
                @method('PUT')
                
                {{-- Title --}}
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Judul Artikel <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title', $article->title) }}" required
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                        placeholder="Masukkan judul artikel">
                    @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                {{-- Slug --}}
                <div class="mb-6">
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Slug (URL)</label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug', $article->slug) }}"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                        placeholder="Akan otomatis dibuat dari judul jika kosong">
                    <p class="mt-1 text-sm text-gray-500">Slug akan digunakan di URL artikel. Kosongkan untuk auto-generate.</p>
                    @error('slug') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                {{-- Excerpt --}}
                <div class="mb-6">
                    <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">Ringkasan Artikel</label>
                    <textarea name="excerpt" id="excerpt" rows="3"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                        placeholder="Ringkasan singkat artikel (opsional, max 500 karakter)">{{ old('excerpt', $article->excerpt) }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">Maksimal 500 karakter. Akan ditampilkan di preview artikel.</p>
                    @error('excerpt') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                {{-- Content (Quill Editor) --}}
                <div class="mb-6">
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Konten Artikel <span class="text-red-500">*</span></label>
                    <div id="editor-container" style="height: 400px;" class="border border-gray-300 rounded-md"></div>
                    <textarea name="content" id="content" style="display: none;">{{ old('content', $article->content) }}</textarea>
                    <input type="hidden" name="content_required" value="1">
                    @error('content') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                {{-- Featured Image --}}
                <div class="mb-6">
                    <label for="featured_image" class="block text-sm font-medium text-gray-700 mb-2">Gambar Featured</label>
                    @if($article->featured_image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $article->featured_image) }}" alt="Featured Image" class="h-32 w-auto rounded-md">
                            <p class="text-sm text-gray-500 mt-1">Gambar saat ini</p>
                        </div>
                    @endif
                    <input type="file" name="featured_image" id="featured_image" accept="image/jpeg,image/png,image/jpg"
                        class="block w-full text-sm text-gray-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-md file:border-0
                            file:text-sm file:font-semibold
                            file:bg-green-50 file:text-green-700
                            hover:file:bg-green-100">
                    <p class="mt-1 text-sm text-gray-500">Format: JPG, JPEG, PNG (Max. 2MB). Kosongkan jika tidak ingin mengubah.</p>
                    @error('featured_image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    {{-- Status --}}
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                        <select name="status" id="status" required
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="draft" {{ old('status', $article->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status', $article->status) == 'published' ? 'selected' : '' }}>Published</option>
                        </select>
                        @error('status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Published At --}}
                    <div>
                        <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Publikasi</label>
                        <input type="datetime-local" name="published_at" id="published_at" 
                            value="{{ old('published_at', $article->published_at ? $article->published_at->format('Y-m-d\TH:i') : '') }}"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <p class="mt-1 text-sm text-gray-500">Kosongkan untuk menggunakan waktu sekarang</p>
                        @error('published_at') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- SEO Meta --}}
                <div class="mb-6 border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">SEO (Opsional)</h3>
                    <div class="mb-4">
                        <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                        <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $article->meta_title) }}"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                            placeholder="Judul untuk SEO (jika kosong akan menggunakan judul artikel)">
                        @error('meta_title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                        <textarea name="meta_description" id="meta_description" rows="2"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                            placeholder="Deskripsi untuk SEO (jika kosong akan menggunakan excerpt)">{{ old('meta_description', $article->meta_description) }}</textarea>
                        @error('meta_description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Submit Buttons --}}
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('dinas.articles.index') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2 bg-[#125047] text-white rounded-md hover:bg-[#0E453F] transition">
                        Update Artikel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Quill.js CSS --}}
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

{{-- Quill.js JS --}}
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Quill editor
    var quill = new Quill('#editor-container', {
        theme: 'snow',
        modules: {
            toolbar: {
                container: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'color': [] }, { 'background': [] }],
                    ['link', 'image'],
                    ['clean']
                ],
                handlers: {
                    image: function() {
                        // Create file input
                        var input = document.createElement('input');
                        input.setAttribute('type', 'file');
                        input.setAttribute('accept', 'image/*');
                        input.click();

                        input.onchange = function() {
                            var file = input.files[0];
                            if (file) {
                                // Show loading
                                var range = quill.getSelection();
                                if (!range) {
                                    range = { index: quill.getLength() };
                                }
                                quill.insertText(range.index, 'Uploading...', 'user');
                                quill.setSelection(range.index + 13);

                                // Create FormData
                                var formData = new FormData();
                                formData.append('image', file);

                                // Upload image
                                fetch('{{ route("dinas.articles.upload-image") }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: formData
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        // Remove "Uploading..." text
                                        quill.deleteText(range.index, 13);
                                        // Insert image
                                        quill.insertEmbed(range.index, 'image', data.url);
                                        quill.setSelection(range.index + 1);
                                    } else {
                                        alert('Gagal mengupload gambar: ' + (data.message || 'Unknown error'));
                                        quill.deleteText(range.index, 13);
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('Gagal mengupload gambar. Silakan coba lagi.');
                                    quill.deleteText(range.index, 13);
                                });
                            }
                        };
                    }
                }
            }
        },
        placeholder: 'Tulis konten artikel Anda di sini...'
    });

    // Set content from article
    var content = {!! json_encode(old('content', $article->content)) !!};
    quill.root.innerHTML = content;

    // Always update content before form submit (fallback)
    function updateContentToTextarea() {
        try {
            var content = quill.root.innerHTML;
            var contentTextarea = document.getElementById('content');
            if (contentTextarea) {
                contentTextarea.value = content;
                console.log('Content synced to textarea');
            }
        } catch (error) {
            console.error('Error syncing content:', error);
        }
    }

    // Sync content periodically and before submit
    setInterval(updateContentToTextarea, 2000); // Sync every 2 seconds

    // Update hidden textarea before form submit
    var form = document.getElementById('article-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Form submit triggered');
            
            // Always update content first
            updateContentToTextarea();
            
            try {
                // Get content from Quill editor
                var content = quill.root.innerHTML;
                var textContent = quill.getText().trim();
                
                console.log('Content length:', textContent.length);
                console.log('HTML content preview:', content.substring(0, 100));
                
                // Basic validation - check if content is not empty
                if (!content || content === '<p><br></p>' || content === '<p></p>' || textContent.length < 10) {
                    e.preventDefault();
                    alert('Konten artikel terlalu pendek. Minimal 10 karakter teks.');
                    console.log('Validation failed - content too short');
                    return false;
                }
                
                console.log('Validation passed - form will submit');
                // If validation passes, form will submit normally
            } catch (error) {
                console.error('Error in form submit validation:', error);
                // Don't prevent submit if there's an error, let server handle it
                // Content already updated above
            }
        });
    } else {
        console.error('Form not found!');
    }

    // Auto-generate slug from title
    document.getElementById('title').addEventListener('input', function() {
        var slugInput = document.getElementById('slug');
        if (!slugInput.value || slugInput.dataset.autoGenerated === 'true') {
            var title = this.value;
            var slug = title.toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim();
            slugInput.value = slug;
            slugInput.dataset.autoGenerated = 'true';
        }
    });

    // Reset auto-generated flag when user manually edits slug
    document.getElementById('slug').addEventListener('input', function() {
        this.dataset.autoGenerated = 'false';
    });
});
</script>
@endsection

