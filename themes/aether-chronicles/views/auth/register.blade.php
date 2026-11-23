@extends('layouts.app')

@section('content')
<div class="chronicles-container py-12 flex items-center justify-center min-h-[75vh]">
    <div class="w-full max-w-lg">
        <div class="chronicles-panel-ivy pt-8 px-6 sm:px-8">
            <div class="chronicles-ribbon-header">
                <span>Forge Your Legacy</span>
            </div>

            <div class="text-center mb-6 mt-1">
                <h2 class="font-cinzel text-xl font-bold text-[#4A2200] uppercase tracking-wider">
                    New Account Registration
                </h2>
                <p class="text-xs text-[#795E43] mt-1">
                    Begin your adventure in {{ \App\Models\SiteSetting::get('server_name', config('aether.server_name', 'MYTHERA')) }}
                </p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-cinzel font-bold text-[#4A3319] uppercase tracking-wider mb-1">
                        Desired Username
                    </label>
                    <input type="text" 
                           name="username" 
                           value="{{ old('username') }}" 
                           required 
                           autofocus
                           placeholder="4-23 characters, alphanumeric" 
                           class="chronicles-input">
                    @error('username')
                        <span class="text-xs text-red-600 block mt-1 font-semibold">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-cinzel font-bold text-[#4A3319] uppercase tracking-wider mb-1">
                        Valid Email Address
                    </label>
                    <input type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           placeholder="For security & password recovery" 
                           class="chronicles-input">
                    @error('email')
                        <span class="text-xs text-red-600 block mt-1 font-semibold">{{ $message }}</span>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-cinzel font-bold text-[#4A3319] uppercase tracking-wider mb-1">
                            Password
                        </label>
                        <input type="password" 
                               name="password" 
                               required 
                               placeholder="Min 6 characters" 
                               class="chronicles-input">
                        @error('password')
                            <span class="text-xs text-red-600 block mt-1 font-semibold">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-cinzel font-bold text-[#4A3319] uppercase tracking-wider mb-1">
                            Confirm Password
                        </label>
                        <input type="password" 
                               name="password_confirmation" 
                               required 
                               placeholder="Repeat password" 
                               class="chronicles-input">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-cinzel font-bold text-[#4A3319] uppercase tracking-wider mb-1">
                        Gender
                    </label>
                    <div class="flex items-center gap-6 text-xs text-[#4A3319]">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="sex" value="M" checked class="text-[#D88914] border-[#CBB493]">
                            <span>Male (M)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="sex" value="F" class="text-[#D88914] border-[#CBB493]">
                            <span>Female (F)</span>
                        </label>
                    </div>
                </div>

                <div class="pt-1">
                    <label class="flex items-start gap-2 cursor-pointer text-xs text-[#5A3D28]">
                        <input type="checkbox" name="terms" required class="rounded border-[#CBB493] text-[#D88914] mt-0.5">
                        <span>I accept the server rules, terms of service, and anti-cheat policies.</span>
                    </label>
                </div>

                <div class="pt-2">
                    <button type="submit" class="chronicles-btn-red w-full">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                        <span>Create Game Account</span>
                    </button>
                </div>

                <div class="text-center pt-3 border-t border-[#DECBB1] text-xs text-[#6B5541]">
                    <span>Already an adventurer?</span>
                    <a href="{{ route('login') }}" class="font-bold text-[#D4441D] hover:underline ml-1">
                        Log In Here
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
