@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto py-8">
    <div class="ro-panel">
        <div class="ro-panel-header">
            <i data-lucide="key" class="w-4 h-4 text-[#FFDA89]"></i>
            <span>Account Sign In &bull; Adventurer Verification</span>
        </div>

        <div class="p-6 bg-[#FCF7ED] space-y-4">
            <div class="text-center pb-3 border-b border-[#DECBB1]">
                <div class="w-12 h-12 rounded bg-[#493322] border-2 border-[#8C6A2D] flex items-center justify-center mx-auto mb-2 shadow-md">
                    <i data-lucide="shield" class="w-6 h-6 text-[#FFDA89]"></i>
                </div>
                <h1 class="text-xl font-bold font-serif text-[#2D1D13]">Ragnarok Game Account</h1>
                <p class="text-xs text-[#5C4632] mt-0.5">Enter your game credentials to manage your characters</p>
            </div>

            @if ($errors->any())
                <div class="p-3 bg-[#FDEEED] border border-[#EF5350] rounded text-xs text-[#B71C1C] space-y-1">
                    @foreach ($errors->all() as $error)
                        <p class="flex items-center gap-1.5">
                            <i data-lucide="alert-circle" class="w-3.5 h-3.5 text-[#C62828] shrink-0"></i>
                            <span>{{ $error }}</span>
                        </p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-3.5">
                @csrf

                <div>
                    <label for="username" class="block text-xs font-bold uppercase tracking-wider text-[#5C4632] mb-1">
                        Game Username
                    </label>
                    <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus
                        class="w-full" placeholder="Enter your username">
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-[#5C4632] mb-1">
                        Password
                    </label>
                    <input id="password" type="password" name="password" required
                        class="w-full" placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 cursor-pointer text-[#5C4632]">
                        <input type="checkbox" name="remember" class="rounded border-[#8C6A2D]">
                        <span>Remember device</span>
                    </label>
                    <a href="{{ route('register') }}" class="text-[#315B88] font-bold hover:underline">
                        Need an account?
                    </a>
                </div>

                <div class="pt-2">
                    <button type="submit" class="ro-btn-primary w-full py-2.5 text-xs">
                        <i data-lucide="log-in" class="w-4 h-4"></i>
                        <span>Sign In to Control Panel</span>
                    </button>
                </div>
            </form>

            <div class="pt-3 border-t border-[#DECBB1] text-center text-xs text-[#5C4632]">
                <span>New to Rune-Midgard?</span>
                <a href="{{ route('register') }}" class="text-[#315B88] font-bold hover:underline ml-1">
                    Create New Account
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

