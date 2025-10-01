@extends('layouts.guest')

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
            <img src="{{ asset('images/logo-simpedas.svg') }}" alt="SIMPEDAS" class="h-10">
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
                        <h2 class="text-4xl font-bold text-gray-900">Sign in</h2>
                    </div>
                    {{-- Registration disabled for internal system --}}
                    <div class="text-right text-sm">
                        <span class="text-gray-500">Login with your internal account</span>
                    </div>
                </div>
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf
                    {{-- Session status (e.g., password reset success) --}}
                    @if (session('status'))
                        <div class="text-green-700 bg-green-100 rounded-md p-3 text-sm">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Enter your username or email address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="Username or email address" class="w-full border border-[#17695a] rounded-lg px-4 py-2 focus:ring-2 focus:ring-[#17695a] focus:outline-none">
                        @error('email')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Enter your Password</label>
                        <div class="relative">
                            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Password" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-[#17695a] focus:outline-none pr-10">
                            <button type="button" tabindex="-1" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 focus:outline-none" onclick="togglePassword()">
                                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-.274.832-.64 1.627-1.09 2.367M15.54 15.54A8.963 8.963 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.014 9.014 0 012.042-3.362" /></svg>
                            </button>
                        </div>
                        @error('password')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="flex justify-end items-center">
            @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs text-red-600 hover:underline">Forgot Password</a>
            @endif
                    </div>
                    <button type="submit" class="w-full bg-[#17695a] text-white font-semibold rounded-lg py-2 mt-2 shadow hover:bg-[#09443c] transition">Sign in</button>
                    <div class="flex items-center my-2">
                        <div class="flex-grow border-t border-gray-200"></div>
                        <span class="mx-2 text-gray-400 text-xs">OR</span>
                        <div class="flex-grow border-t border-gray-200"></div>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" class="flex-1 flex items-center justify-center bg-green-100 rounded-lg py-2 font-semibold text-gray-700 hover:bg-green-200 transition"><img src="https://www.svgrepo.com/show/475656/google-color.svg" class="h-5 mr-2">Sign in with Google</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');
    const eyeOpen = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-.274.832-.64 1.627-1.09 2.367M15.54 15.54A8.963 8.963 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.014 9.014 0 012.042-3.362" />';
    const eyeClosed = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.014 9.014 0 012.042-3.362m1.518-2.478A8.963 8.963 0 0112 5c4.478 0 8.268 2.943 9.542 7-.274.832-.64 1.627-1.09 2.367M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />';
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = eyeClosed;
    } else {
        input.type = 'password';
        icon.innerHTML = eyeOpen;
    }
}
</script>
@endsection
