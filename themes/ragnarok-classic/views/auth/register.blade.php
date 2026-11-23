@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto py-8">
    <div class="ro-panel">
        <div class="ro-panel-header">
            <i data-lucide="user-plus" class="w-4 h-4 text-[#FFDA89]"></i>
            <span>Adventurer Enrollment &bull; Free Registration</span>
        </div>

        <div class="p-6 bg-[#FCF7ED] space-y-4">
            <div class="text-center pb-3 border-b border-[#DECBB1]">
                <div class="w-12 h-12 rounded bg-[#493322] border-2 border-[#8C6A2D] flex items-center justify-center mx-auto mb-2 shadow-md">
                    <i data-lucide="shield-plus" class="w-6 h-6 text-[#FFDA89]"></i>
                </div>
                <h1 class="text-xl font-bold font-serif text-[#2D1D13]">Create Free Game Account</h1>
                <p class="text-xs text-[#5C4632] mt-0.5">One unified account for in-game client & web dashboard</p>
            </div>

            @if (isset($errors) && $errors->any())
                <div class="p-3 bg-[#FDEEED] border border-[#EF5350] rounded text-xs text-[#B71C1C] space-y-1">
                    @foreach ($errors->all() as $error)
                        <p class="flex items-center gap-1.5">
                            <i data-lucide="alert-circle" class="w-3.5 h-3.5 text-[#C62828] shrink-0"></i>
                            <span>{{ $error }}</span>
                        </p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-3.5">
                @csrf

                <div>
                    <label for="username" class="block text-xs font-bold uppercase tracking-wider text-[#5C4632] mb-1">
                        Login Username
                    </label>
                    <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus
                        class="w-full" placeholder="Choose a username">
                    <p class="text-[10px] text-[#77644E] mt-0.5">4 to 23 alphanumeric characters.</p>
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-[#5C4632] mb-1">
                        Email Address
                    </label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                        class="w-full" placeholder="adventurer@example.com">
                </div>

                <div>
                    <label for="gender" class="block text-xs font-bold uppercase tracking-wider text-[#5C4632] mb-1">
                        Gender
                    </label>
                    <select id="gender" name="gender" class="w-full">
                        <option value="M" {{ old('gender') === 'M' ? 'selected' : '' }}>Male (M)</option>
                        <option value="F" {{ old('gender') === 'F' ? 'selected' : '' }}>Female (F)</option>
                    </select>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-[#5C4632] mb-1">
                        Password
                    </label>
                    <input id="password" type="password" name="password" required
                        class="w-full" placeholder="••••••••">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-[#5C4632] mb-1">
                        Confirm Password
                    </label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        class="w-full" placeholder="••••••••">
                </div>

                <div class="pt-2">
                    <button type="submit" class="ro-btn-gold w-full py-2.5 text-xs">
                        <i data-lucide="sparkles" class="w-4 h-4"></i>
                        <span>Register Adventurer Account</span>
                    </button>
                </div>
            </form>

            <div class="pt-3 border-t border-[#DECBB1] text-center text-xs text-[#5C4632]">
                <span>Already registered an account?</span>
                <a href="{{ route('login') }}" class="text-[#315B88] font-bold hover:underline ml-1">
                    Sign In Here
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

