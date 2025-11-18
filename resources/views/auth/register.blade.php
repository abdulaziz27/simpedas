@extends('layouts.guest')

@section('title', 'Daftar - SIMPEDAS')

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
                        <div class="text-sm text-gray-500 mb-1">Welcome to SIMPEDAS</div>
                        <h2 class="text-4xl font-bold text-gray-900">Sign up</h2>
                    </div>
                    <div class="text-right text-sm">
                        <span class="text-gray-500">Have an Account ?</span>
                        <a href="{{ route('login') }}" class="text-[#17695a] font-semibold hover:underline ml-1">Sign in</a>
                    </div>
                </div>
                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Enter your username or email address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="Username or email address" class="w-full border border-[#17695a] rounded-lg px-4 py-2 focus:ring-2 focus:ring-[#17695a] focus:outline-none">
                        @error('email')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="flex gap-2">
                        <div class="w-1/2">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">User name</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" placeholder="User name" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-[#17695a] focus:outline-none">
                            @error('name')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="w-1/2">
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                            <input id="phone" type="text" name="phone" value="{{ old('phone') }}" required placeholder="Contact Number" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-[#17695a] focus:outline-none">
                            @error('phone')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Enter your Password</label>
                        <div class="relative">
                            <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Password" class="w-full border border-gray-300 rounded-lg px-4 py-2 pr-10 focus:ring-2 focus:ring-[#17695a] focus:outline-none">
                            <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg id="eyeIcon" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg id="eyeOffIcon" class="h-5 w-5 text-gray-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                                </svg>
                            </button>
                        </div>
                        @error('password')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="w-full bg-[#17695a] text-white font-semibold rounded-lg py-2 mt-2 shadow hover:bg-[#09443c] transition">Sign up</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    const eyeOffIcon = document.getElementById('eyeOffIcon');

    togglePassword.addEventListener('click', function() {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.add('hidden');
            eyeOffIcon.classList.remove('hidden');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('hidden');
            eyeOffIcon.classList.add('hidden');
        }
    });
});
</script>
@endsection
