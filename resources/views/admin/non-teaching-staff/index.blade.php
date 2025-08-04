@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-[#125047] py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumb & Session Message --}}
            <nav class="flex items-center space-x-2 text-white mb-6">
                <a href="{{ route('home') }}" class="hover:text-green-300">Dashboard</a>
                <span class="text-gray-300">&gt;</span>
                <span class="border-b-2 border-white">Data Tenaga Kependidikan</span>
            </nav>

            {{-- Pesan sukses/error import --}}
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
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Header Card --}}
            <div class="bg-[#136e67] rounded-2xl shadow-lg px-8 py-5 mb-8 border-b-4 border-white flex items-center">
                <h2 class="text-3xl font-bold text-white mx-auto">Manajemen Data Tenaga Pendidik Non Guru</h2>
            </div>

            {{-- Filter Section --}}
            <div class="bg-[#0d524a] rounded-xl p-6 mb-8">
                <h2 class="text-2xl font-bold text-white mb-6">Filter Tenaga Kependidikan</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Sekolah</label>
                        <div class="relative">
                            <select name="sekolah" class="block w-full bg-white rounded-lg border-0 py-2.5 pl-4 pr-10 focus:ring-2 focus:ring-green-400">
                                <option value="">Semua Sekolah</option>
                                @foreach($schools ?? [] as $school)
                                    <option value="{{ $school->id }}">{{ $school->name }}</option>
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
                        <label class="block text-sm font-medium text-gray-300 mb-2">Jabatan</label>
                        <div class="relative">
                            <select name="position" class="block w-full bg-white rounded-lg border-0 py-2.5 pl-4 pr-10 focus:ring-2 focus:ring-green-400">
                                <option value="">Semua Jabatan</option>
                                <option value="Tata Usaha">Tata Usaha</option>
                                <option value="Pustakawan">Pustakawan</option>
                                <option value="Laboran">Laboran</option>
                                <option value="Keamanan">Keamanan</option>
                                <option value="Kebersihan">Kebersihan</option>
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
                            <select name="status" class="block w-full bg-white rounded-lg border-0 py-2.5 pl-4 pr-10 focus:ring-2 focus:ring-green-400">
                                <option value="">Semua Status</option>
                                <option value="PNS">PNS</option>
                                <option value="PPPK">PPPK</option>
                                <option value="GTT/PTT">GTT/PTT</option>
                                <option value="Honorer">Honorer</option>
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
                            <input type="text" name="search" placeholder="Cari nama atau NIP..." class="block w-full bg-white rounded-lg border-0 py-2.5 pl-4 pr-10 focus:ring-2 focus:ring-green-400">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <button type="submit" class="text-gray-700 hover:text-gray-900">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Daftar Staff Section --}}
            <div class="bg-[#0d524a] rounded-xl p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-white">Daftar Tenaga Pendidik Non Guru</h2>
                    <div class="flex space-x-3">
                        <button onclick="document.getElementById('import-modal').classList.remove('hidden')"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 rounded-lg font-semibold text-white hover:bg-blue-700 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                            </svg>
                            Import Excel
                        </button>
                        <a href="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.non-teaching-staff.create') : route('dinas.non-teaching-staff.create') }}" class="inline-flex items-center px-4 py-2 bg-white rounded-lg font-semibold text-[#0d524a] hover:bg-green-50 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Tenaga Pendidik Non Guru
                        </a>
                    </div>
                </div>

                {{-- Staff Table --}}
                @if($staff->isEmpty())
                    <div class="bg-[#09443c] rounded-xl shadow-lg p-8 text-center">
                        <div class="mb-4">
                            <svg class="w-16 h-16 mx-auto text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">Tidak ada data tenaga pendidik non guru</h3>
                        <p class="text-white/70">Silahkan tambah data tenaga pendidik non guru atau ubah filter pencarian</p>
                            </div>
                @else
                    @php
                        $rows = $staff->map(function($person){
                            return [
                                $person->full_name,
                                $person->nip_nik ?? '-',
                                $person->school->name ?? '-',
                                $person->position ?? '-',
                                // $person->tmt ? ($person->tmt instanceof \Carbon\Carbon ? $person->tmt->translatedFormat('d-m-Y') : $person->tmt) : '-',
                                '<a href="'.(auth()->user()->hasRole('admin_sekolah') ? route('sekolah.non-teaching-staff.show', $person) : route('dinas.non-teaching-staff.show', $person)).'" class="text-green-300 hover:underline">Lihat detail</a>'
                            ];
                        });
                    @endphp
                    <div class="bg-[#09443c] rounded-xl shadow-lg px-0 py-8">
                        <x-public.data-table :headers="['Nama','NIP/NIK','Asal Sekolah','Jabatan','Aksi']" :rows="$rows" />
                    </div>
                @endif

                {{-- Pagination --}}
                @if(isset($staff) && method_exists($staff, 'hasPages') && $staff->hasPages())
                <div class="mt-6">
                    {{ $staff->links() }}
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
                <form action="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.non-teaching-staff.import') : route('dinas.non-teaching-staff.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Import Data Tenaga Pendidik Non Guru</h3>
                            <div class="mt-2">
                                <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <p class="text-xs text-gray-500 mt-1">Format yang didukung: .xlsx, .xls, .csv</p>
                                <div class="mt-3">
                                    <a href="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.non-teaching-staff.template') : route('dinas.non-teaching-staff.template') }}" class="text-sm text-blue-600 hover:underline">
                                        Download template Excel
                                    </a>
                                </div>
                                <div class="mt-3 text-xs text-gray-500">
                                    <p class="font-semibold">Petunjuk Import:</p>
                                    <ul class="list-disc pl-5 space-y-1 mt-1">
                                        <li>Kolom AKSI: CREATE, UPDATE, atau DELETE</li>
                                        <li>Kolom NIP_NIK: Wajib dan unik</li>
                                        <li>Kolom NAMA_LENGKAP: Wajib diisi</li>
                                        <li>Kolom JENIS_KELAMIN: Laki-laki atau Perempuan</li>
                                        <li>Kolom AGAMA: Wajib diisi dengan agama yang valid</li>
                                        <li>Kolom JABATAN: Wajib diisi</li>
                                        <li>Kolom STATUS_KE_PEGAWAIAN: PNS, PPPK, GTY, PTY</li>
                                        <li>Kolom STATUS: Aktif atau Tidak Aktif</li>
                                        <li>Kolom NPSN_SEKOLAH: Wajib untuk admin dinas</li>
                                    </ul>
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
@endsection

