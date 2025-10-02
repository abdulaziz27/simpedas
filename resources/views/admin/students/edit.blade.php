@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#125047] py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="flex items-center space-x-2 text-white mb-6">
            <a href="{{ route('home') }}" class="hover:text-green-300">Dashboard</a>
            <span class="text-gray-300">&gt;</span>
            <a href="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.students.index') : route('dinas.students.index') }}" class="hover:text-green-300">Data Siswa</a>
            <span class="text-gray-300">&gt;</span>
            <span class="border-b-2 border-white">Edit Data Siswa</span>
        </nav>

        <div class="bg-white rounded-xl p-8 shadow-lg">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Data Siswa - {{ $student->nama_lengkap }}</h1>

            <form action="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.students.update', $student) : route('dinas.students.update', $student) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Data Wajib --}}
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Data Wajib</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

                        @if(Auth::user()->hasRole('admin_dinas'))
                        <div class="md:col-span-2">
                            <label for="sekolah_id" class="block text-sm font-medium text-gray-700">Sekolah</label>
                            <select name="sekolah_id" id="sekolah_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                @foreach($schools as $school)
                                    <option value="{{ $school->id }}" {{ old('sekolah_id', $student->sekolah_id) == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                                @endforeach
                            </select>
                            @error('sekolah_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        @endif

                        <div>
                            <label for="nisn" class="block text-sm font-medium text-gray-700">NISN <span class="text-red-500">*</span></label>
                            <input type="text" name="nisn" id="nisn" value="{{ old('nisn', $student->nisn) }}" required maxlength="20" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            @error('nisn') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="nipd" class="block text-sm font-medium text-gray-700">NIPD</label>
                            <input type="text" name="nipd" id="nipd" value="{{ old('nipd', $student->nipd) }}" maxlength="20" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            @error('nipd') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="nama_lengkap" class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap', $student->nama_lengkap) }}" required maxlength="150" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            @error('nama_lengkap') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <select name="jenis_kelamin" id="jenis_kelamin" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                <option value="">Pilih</option>
                                <option value="L" {{ old('jenis_kelamin', $student->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $student->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('jenis_kelamin') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="tempat_lahir" class="block text-sm font-medium text-gray-700">Tempat Lahir <span class="text-red-500">*</span></label>
                            <input type="text" name="tempat_lahir" id="tempat_lahir" value="{{ old('tempat_lahir', $student->tempat_lahir) }}" required maxlength="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            @error('tempat_lahir') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700">Tanggal Lahir <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir', $student->tanggal_lahir?->format('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            @error('tanggal_lahir') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="agama" class="block text-sm font-medium text-gray-700">Agama <span class="text-red-500">*</span></label>
                            <select name="agama" id="agama" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                <option value="">Pilih Agama</option>
                                <option value="Islam" {{ old('agama', $student->agama) == 'Islam' ? 'selected' : '' }}>Islam</option>
                                <option value="Kristen" {{ old('agama', $student->agama) == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                <option value="Katolik" {{ old('agama', $student->agama) == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                <option value="Hindu" {{ old('agama', $student->agama) == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                <option value="Buddha" {{ old('agama', $student->agama) == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                <option value="Konghucu" {{ old('agama', $student->agama) == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                            </select>
                            @error('agama') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="rombel" class="block text-sm font-medium text-gray-700">Rombel <span class="text-red-500">*</span></label>
                            <input type="text" name="rombel" id="rombel" value="{{ old('rombel', $student->rombel) }}" required maxlength="50" placeholder="Contoh: 6A, 9B, 12 IPA 1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            @error('rombel') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="status_siswa" class="block text-sm font-medium text-gray-700">Status Siswa</label>
                            <select name="status_siswa" id="status_siswa" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                <option value="aktif" {{ old('status_siswa', $student->status_siswa) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="tamat" {{ old('status_siswa', $student->status_siswa) == 'tamat' ? 'selected' : '' }}>Tamat</option>
                                <option value="pindah" {{ old('status_siswa', $student->status_siswa) == 'pindah' ? 'selected' : '' }}>Pindah</option>
                            </select>
                            @error('status_siswa') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Data Domisili --}}
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Data Domisili</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <div class="md:col-span-2">
                            <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                            <textarea name="alamat" id="alamat" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('alamat', $student->alamat) }}</textarea>
                            @error('alamat') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="kelurahan" class="block text-sm font-medium text-gray-700">Kelurahan</label>
                            <input type="text" name="kelurahan" id="kelurahan" value="{{ old('kelurahan', $student->kelurahan) }}" maxlength="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            @error('kelurahan') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="kecamatan" class="block text-sm font-medium text-gray-700">Kecamatan</label>
                            <input type="text" name="kecamatan" id="kecamatan" value="{{ old('kecamatan', $student->kecamatan) }}" maxlength="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            @error('kecamatan') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="kode_pos" class="block text-sm font-medium text-gray-700">Kode Pos</label>
                            <input type="text" name="kode_pos" id="kode_pos" value="{{ old('kode_pos', $student->kode_pos) }}" maxlength="10" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            @error('kode_pos') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Data Keluarga --}}
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Data Keluarga</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <div>
                            <label for="nama_ayah" class="block text-sm font-medium text-gray-700">Nama Ayah</label>
                            <input type="text" name="nama_ayah" id="nama_ayah" value="{{ old('nama_ayah', $student->nama_ayah) }}" maxlength="150" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            @error('nama_ayah') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="pekerjaan_ayah" class="block text-sm font-medium text-gray-700">Pekerjaan Ayah</label>
                            <input type="text" name="pekerjaan_ayah" id="pekerjaan_ayah" value="{{ old('pekerjaan_ayah', $student->pekerjaan_ayah) }}" maxlength="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            @error('pekerjaan_ayah') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="nama_ibu" class="block text-sm font-medium text-gray-700">Nama Ibu</label>
                            <input type="text" name="nama_ibu" id="nama_ibu" value="{{ old('nama_ibu', $student->nama_ibu) }}" maxlength="150" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            @error('nama_ibu') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="pekerjaan_ibu" class="block text-sm font-medium text-gray-700">Pekerjaan Ibu</label>
                            <input type="text" name="pekerjaan_ibu" id="pekerjaan_ibu" value="{{ old('pekerjaan_ibu', $student->pekerjaan_ibu) }}" maxlength="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            @error('pekerjaan_ibu') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="anak_ke" class="block text-sm font-medium text-gray-700">Anak Ke-</label>
                            <input type="number" name="anak_ke" id="anak_ke" value="{{ old('anak_ke', $student->anak_ke) }}" min="1" max="20" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            @error('anak_ke') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="jumlah_saudara" class="block text-sm font-medium text-gray-700">Jumlah Saudara</label>
                            <input type="number" name="jumlah_saudara" id="jumlah_saudara" value="{{ old('jumlah_saudara', $student->jumlah_saudara) }}" min="0" max="20" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            @error('jumlah_saudara') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Data Kontak & Sosial Ekonomi --}}
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Kontak & Sosial Ekonomi</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <div>
                            <label for="no_hp" class="block text-sm font-medium text-gray-700">No. HP</label>
                            <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp', $student->no_hp) }}" maxlength="20" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            @error('no_hp') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="kip" class="block text-sm font-medium text-gray-700">KIP</label>
                            <select name="kip" id="kip" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                <option value="">Pilih</option>
                                <option value="1" {{ old('kip', $student->kip ? '1' : '0') == '1' ? 'selected' : '' }}>Ya</option>
                                <option value="0" {{ old('kip', $student->kip ? '1' : '0') == '0' ? 'selected' : '' }}>Tidak</option>
                            </select>
                            @error('kip') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="transportasi" class="block text-sm font-medium text-gray-700">Transportasi</label>
                            <input type="text" name="transportasi" id="transportasi" value="{{ old('transportasi', $student->transportasi) }}" maxlength="50" placeholder="Contoh: Sepeda, Motor, Jalan Kaki" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            @error('transportasi') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="jarak_rumah_sekolah" class="block text-sm font-medium text-gray-700">Jarak Rumah ke Sekolah (km)</label>
                            <input type="number" name="jarak_rumah_sekolah" id="jarak_rumah_sekolah" value="{{ old('jarak_rumah_sekolah', $student->jarak_rumah_sekolah) }}" step="0.01" min="0" max="999.99" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            @error('jarak_rumah_sekolah') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Data Kesehatan --}}
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Data Kesehatan</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <div>
                            <label for="tinggi_badan" class="block text-sm font-medium text-gray-700">Tinggi Badan (cm)</label>
                            <input type="number" name="tinggi_badan" id="tinggi_badan" value="{{ old('tinggi_badan', $student->tinggi_badan) }}" min="50" max="250" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            @error('tinggi_badan') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="berat_badan" class="block text-sm font-medium text-gray-700">Berat Badan (kg)</label>
                            <input type="number" name="berat_badan" id="berat_badan" value="{{ old('berat_badan', $student->berat_badan) }}" min="10" max="200" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            @error('berat_badan') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="flex justify-end space-x-4">
                    <a href="{{ auth()->user()->hasRole('admin_sekolah') ? route('sekolah.students.show', $student) : route('dinas.students.show', $student) }}" class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        Update Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
