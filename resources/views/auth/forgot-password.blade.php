@extends('layouts.guest')

@section('title', 'Lupa Password - SIMPEDAS')

@section('content')
<div class="min-h-screen bg-white relative overflow-hidden">
    <!-- Background Split -->
    <div class="absolute inset-0 flex">
        <div class="w-1/2 bg-[#17695a]"></div>
        <div class="w-1/2 bg-white"></div>
    </div>

    <!-- Logo -->
    <div class="absolute top-0 left-0 p-8 z-20">
        <a href="/">
            <img src="{{ asset('images/logo-siantar-cerdas.svg') }}" alt="SIMPEDAS" class="h-10">
        </a>
        </div>

    <!-- Left Character -->
    <div class="absolute bottom-0 left-0 hidden md:block z-10">
        <img src="{{ asset('images/avatar-saly-riding.svg') }}" alt="Riding illustration" class="w-80 lg:w-96">
        </div>

    <!-- Right Character -->
    <div class="absolute top-1/2 right-10 transform -translate-y-1/2 hidden md:block z-10">
         <img src="{{ asset('images/avatar-saly-phone.svg') }}" alt="Phone illustration" class="w-64 lg:w-72">
        </div>

    <!-- Form Container -->
    <div class="min-h-screen flex justify-center items-center relative z-20">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-2xl p-8">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <div class="text-sm text-gray-500 mb-1">Reset your password</div>
                        <h2 class="text-4xl font-bold text-gray-900">Forgot Password</h2>
                    </div>
                    <div class="text-right text-sm">
                        <a href="{{ route('login') }}" class="text-[#17695a] font-semibold hover:underline">Back to Login</a>
                    </div>
                </div>

                {{-- Success status after sending link --}}
                @if (session('status'))
                    <div class="text-green-700 bg-green-100 rounded-md p-3 text-sm mb-4">
                        {{ __('Link reset password sudah dikirim ke email Anda.') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Alamat Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@sekolah.sch.id" class="w-full border border-[#17695a] rounded-lg px-4 py-2 focus:ring-2 focus:ring-[#17695a] focus:outline-none">
                        @error('email')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="w-full bg-[#17695a] text-white font-semibold rounded-lg py-2 mt-2 shadow hover:bg-[#09443c] transition">Kirim Link Reset Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
