@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#125047] py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-white">Profil Saya</h1>
            <p class="text-green-200 text-lg">Kelola informasi akun dan password Anda.</p>
        </div>

        {{-- Success/Error Messages --}}
        @if (session('status') === 'profile-updated')
            <div id="successMessage" class="mb-6 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg text-center">
                Profil berhasil diperbarui.
            </div>
        @endif

        @if (session('status') === 'password-updated')
            <div id="successMessage" class="mb-6 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg text-center">
                Password berhasil diperbarui.
            </div>
        @endif

        {{-- Profile Information Card --}}
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
            <div class="flex items-center mb-6">
                <div class="h-16 w-16 rounded-full bg-[#125047] flex items-center justify-center mr-6">
                    <svg class="h-8 w-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $user->name }}</h3>
                    <p class="text-gray-600">{{ $user->email }}</p>
                    @if($user->hasRole('admin_dinas'))
                        <p class="text-sm text-green-600 font-semibold">Admin Dinas Pendidikan</p>
                    @elseif($user->hasRole('admin_sekolah'))
                        <p class="text-sm text-green-600 font-semibold">Admin {{ $user->school->name ?? 'Sekolah' }}</p>
                    @endif
                </div>
            </div>

            {{-- Profile Update Form --}}
            <div class="border-t pt-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">Informasi Profile</h4>
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        {{-- Password Update Card --}}
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Ganti Password</h4>
            @include('profile.partials.update-password-form')
        </div>

        {{-- Account Actions Card --}}
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Tindakan Akun</h4>
            @include('profile.partials.delete-user-form')
        </div>

        {{-- Link to Teacher Profile if Available --}}
        @if($user->teacher_id && $user->teacher)
            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 mt-8">
                <div class="flex items-center">
                    <svg class="h-6 w-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h5 class="text-sm font-semibold text-blue-800">Profil Guru Tersedia</h5>
                        <p class="text-sm text-blue-600">Akun ini terhubung dengan data guru: {{ $user->teacher->full_name }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('guru.profile.show') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Lihat Profil Guru Lengkap
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
// Auto hide success messages
setTimeout(function() {
    const successMsg = document.getElementById('successMessage');
    if (successMsg) successMsg.style.display = 'none';
}, 5000);
</script>
@endsection
