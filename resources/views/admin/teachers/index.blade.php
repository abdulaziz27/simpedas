@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-[#125047] py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <nav class="flex items-center space-x-2 text-white mb-6">
                <a href="{{ route('home') }}" class="hover:text-green-300">Dashboard</a>
                <span class="text-gray-300">&gt;</span>
                <span class="border-b-2 border-white">Data Guru</span>
            </nav>

            {{-- Header Card --}}
            <div class="bg-[#136e67] rounded-2xl shadow-lg px-8 py-5 mb-8 border-b-4 border-white flex items-center">
                <h2 class="text-3xl font-bold text-white mx-auto">Manajemen Data Guru</h2>
            </div>

            {{-- Filter Section --}}
            <div class="bg-[#0d524a] rounded-xl p-6 mb-8">
                <h2 class="text-2xl font-bold text-white mb-6">Filter Guru</h2>
                <form id="filtersForm" action="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.teachers.index') : route('dinas.teachers.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Sekolah</label>
                        <div class="relative">
                            <select name="school_id" class="block w-full bg-white rounded-lg border-0 py-2.5 pl-4 pr-10 focus:ring-2 focus:ring-green-400">
                                <option value="">Semua Sekolah</option>
                                @foreach($schools ?? [] as $school)
                                    <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Jenis PTK</label>
                        <div class="relative">
                            <select name="jenis_ptk" class="block w-full bg-white rounded-lg border-0 py-2.5 pl-4 pr-10 focus:ring-2 focus:ring-green-400">
                                <option value="">Semua Jenis PTK</option>
                                <option value="Guru" {{ request('jenis_ptk') == 'Guru' ? 'selected' : '' }}>Guru</option>
                                <option value="Kepala Sekolah" {{ request('jenis_ptk') == 'Kepala Sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                                <option value="Wakil Kepala Sekolah" {{ request('jenis_ptk') == 'Wakil Kepala Sekolah' ? 'selected' : '' }}>Wakil Kepala Sekolah</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                        <div class="relative">
                            <select name="employment_status" class="block w-full bg-white rounded-lg border-0 py-2.5 pl-4 pr-10 focus:ring-2 focus:ring-green-400">
                                <option value="">Semua Status</option>
                                <option value="PNS" {{ request('employment_status') == 'PNS' ? 'selected' : '' }}>PNS</option>
                                <option value="PPPK" {{ request('employment_status') == 'PPPK' ? 'selected' : '' }}>PPPK</option>
                                <option value="GTY" {{ request('employment_status') == 'GTY' ? 'selected' : '' }}>GTY</option>
                                <option value="PTY" {{ request('employment_status') == 'PTY' ? 'selected' : '' }}>PTY</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">&nbsp;</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIP..." class="block w-full bg-white rounded-lg border-0 py-2.5 pl-4 pr-10 focus:ring-2 focus:ring-green-400">
                        </div>
                    </div>
                </form>
            </div>

            {{-- Pesan sukses/error import (hanya di sini, bukan di modal) --}}
            @if(session('success'))
                @php
                    $successMsg = session('success');
                    $successLines = preg_split('/\n+/', $successMsg);
                    $mainMsg = array_shift($successLines);
                @endphp
                <div class="mb-6">
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-2" role="alert">
                        <strong>{{ $mainMsg }}</strong>
                    </div>
                    @if(session('import_errors') && count(session('import_errors')))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-2" role="alert">
                            <strong>Error:</strong>
                            <ul class="list-disc pl-5">
                                @foreach(session('import_errors') as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if(session('import_warnings') && count(session('import_warnings')))
                        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-2" role="alert">
                            <strong>Warning:</strong>
                            <ul class="list-disc pl-5">
                                @foreach(session('import_warnings') as $warn)
                                    <li>{{ $warn }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Error umum --}}
            @if(session('error'))
                <div class="mb-6">
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <strong>Error:</strong> {{ session('error') }}
                    </div>
                </div>
            @endif

            {{-- Daftar Guru Section --}}
            <div class="bg-[#0d524a] rounded-xl p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-white">Daftar Guru</h2>
                    <div class="flex space-x-3">
                        <button onclick="document.getElementById('import-modal').classList.remove('hidden')"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 rounded-lg font-semibold text-white hover:bg-blue-700 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                            </svg>
                            Import Excel
                        </button>
                        <a href="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.teachers.create') : route('dinas.teachers.create') }}" class="inline-flex items-center px-4 py-2 bg-white rounded-lg font-semibold text-[#0d524a] hover:bg-green-50 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Guru
                        </a>
                    </div>
                </div>

                {{-- Teacher Table --}}
                @if($teachers->isEmpty())
                    <div class="bg-[#09443c] rounded-xl shadow-lg p-8 text-center">
                        <div class="mb-4">
                            <svg class="w-16 h-16 mx-auto text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">Tidak ada data guru</h3>
                        <p class="text-white/70">Silahkan tambah data guru atau ubah filter pencarian</p>
                            </div>
                @else
                    @php
                        $rows = $teachers->map(function($teacher){
                            return [
                                $teacher->full_name,
                                $teacher->nuptk ?? '-',
                                ($teacher->gender == 'L' ? 'Laki-laki' : ($teacher->gender == 'P' ? 'Perempuan' : '-')),
                                $teacher->jenis_ptk ?? '-',
                                $teacher->employment_status ?? '-',
                                $teacher->school->name ?? '-',
                                $teacher->mengajar ?? $teacher->subjects ?? '-',
                                '<a href="'.(auth()->user()->hasRole('admin_sekolah') ? route('sekolah.teachers.show', $teacher->id) : route('dinas.teachers.show', $teacher->id)).'" class="text-green-300 hover:underline">Detail</a>'
                            ];
                        });
                    @endphp
                    <div class="bg-[#09443c] rounded-xl shadow-lg px-0 py-8">
                        <x-public.data-table :headers="['Nama','NUPTK','JK','Jenis PTK','Status Kepegawaian','Sekolah','Mengajar','Aksi']" :rows="$rows" />
                    </div>
                @endif

                {{-- Pagination --}}
                @if(isset($teachers) && method_exists($teachers, 'hasPages') && $teachers->hasPages())
                <div class="mt-6">
                    {{ $teachers->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Import Modal --}}
    <div id="import-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('import-modal').classList.add('hidden')"></div>

            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <form id="import-form" action="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.teachers.import') : route('dinas.teachers.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Import Data Guru</h3>
                            <div class="mt-2">
                                <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <p class="text-xs text-gray-500 mt-1">Format yang didukung: .xlsx, .xls, .csv</p>
                                <div class="mt-3">
                                    <a href="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.teachers.template') : route('dinas.teachers.template') }}" class="text-sm text-blue-600 hover:underline">
                                        Download template Excel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Import
                        </button>
                        <button type="button" onclick="document.getElementById('import-modal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function(){
            // Filter form handling
            const form = document.getElementById('filtersForm');
            if (form) {
                const selects = form.querySelectorAll('select');
                const inputs = form.querySelectorAll('input[type="text"]');
                let t;
                const debounce = (fn, delay) => {
                    clearTimeout(t);
                    t = setTimeout(fn, delay);
                };
                selects.forEach(el => el.addEventListener('change', () => form.submit()));
                inputs.forEach(el => el.addEventListener('input', () => debounce(() => form.submit(), 400)));
            }

            // Import form handling
            const importForm = document.getElementById('import-form');
            if (importForm) {
                importForm.addEventListener('submit', function(e) {
                    const fileInput = this.querySelector('input[type="file"]');
                    const submitButton = this.querySelector('button[type="submit"]');

                    if (!fileInput.files.length) {
                        e.preventDefault();
                        alert('Silakan pilih file Excel terlebih dahulu!');
                        return;
                    }

                    // Show loading state
                    submitButton.disabled = true;
                    submitButton.innerHTML = 'Mengimport...';
                    submitButton.classList.add('opacity-50', 'cursor-not-allowed');

                    // Add loading indicator
                    const loadingDiv = document.createElement('div');
                    loadingDiv.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
                    loadingDiv.innerHTML = `
                        <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
                            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                            <span class="text-gray-700">Sedang mengimport data...</span>
                        </div>
                    `;
                    document.body.appendChild(loadingDiv);
                });
            }
        })();
    </script>
@endsection


