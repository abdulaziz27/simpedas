@extends('layouts.guest')

@section('content')
<div class="min-h-screen bg-white relative overflow-hidden">
    <div class="absolute inset-0 flex">
        <div class="w-1/2 bg-[#17695a]"></div>
        <div class="w-1/2 bg-white"></div>
    </div>

    <div class="absolute top-0 left-0 p-8 z-20">
        <a href="/">
            <img src="{{ asset('images/logo-simpedas.jpeg') }}" alt="SIMPEDAS" class="h-10">
        </a>
        </div>

    <div class="absolute bottom-0 left-0 hidden md:block z-10">
        <img src="{{ asset('images/avatar-saly-riding.svg') }}" alt="Riding illustration" class="w-80 lg:w-96">
        </div>

    <div class="absolute top-1/2 right-10 transform -translate-y-1/2 hidden md:block z-10">
         <img src="{{ asset('images/avatar-saly-phone.svg') }}" alt="Phone illustration" class="w-64 lg:w-72">
        </div>

    <div class="min-h-screen flex justify-center items-center relative z-20">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-2xl p-8">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <div class="text-sm text-gray-500 mb-1">Set your new password</div>
                        <h2 class="text-4xl font-bold text-gray-900">Reset Password</h2>
                    </div>
                    <div class="text-right text-sm">
                        <a href="{{ route('login') }}" class="text-[#17695a] font-semibold hover:underline">Back to Login</a>
                    </div>
                </div>

                <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">
                    <input type="hidden" name="email" value="{{ old('email', $request->email) }}">

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Masukkan password baru" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-[#17695a] focus:outline-none">
                        @error('password')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi password baru" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-[#17695a] focus:outline-none">
                        @error('password_confirmation')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="w-full bg-[#17695a] text-white font-semibold rounded-lg py-2 mt-2 shadow hover:bg-[#09443c] transition">Simpan Password Baru</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
