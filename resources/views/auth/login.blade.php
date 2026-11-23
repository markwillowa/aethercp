@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto px-4 sm:px-6 py-8">
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-8 backdrop-blur-xl shadow-2xl aether-glow-cyan">
        <div class="text-center mb-6">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-cyan-500 to-blue-600 flex items-center justify-center mx-auto mb-3 shadow-lg shadow-cyan-500/30">
                <i data-lucide="shield" class="w-6 h-6 text-slate-950 font-bold"></i>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Account Sign In</h1>
            <p class="text-xs text-slate-400 mt-1">Access your Ragnarok character profile & dashboard</p>
        </div>

        @if ($errors->any())
            <div class="mb-5 bg-red-950/60 border border-red-500/40 rounded-xl p-3.5 text-xs text-red-200 space-y-1">
                @foreach ($errors->all() as $error)
                    <p class="flex items-center gap-1.5">
                        <i data-lucide="alert-circle" class="w-3.5 h-3.5 text-red-400 shrink-0"></i>
                        <span>{{ $error }}</span>
                    </p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label for="username" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Game Username</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i data-lucide="user" class="w-4 h-4"></i>
                    </span>
                    <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-950/70 border border-slate-800 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 rounded-xl text-sm text-slate-100 placeholder-slate-500 transition-colors"
                        placeholder="Enter your game username">
                </div>
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                    </span>
                    <input id="password" type="password" name="password" required
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-950/70 border border-slate-800 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 rounded-xl text-sm text-slate-100 placeholder-slate-500 transition-colors"
                        placeholder="••••••••">
                </div>
            </div>

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center gap-2 cursor-pointer text-slate-400 hover:text-slate-200">
                    <input type="checkbox" name="remember" class="rounded bg-slate-950 border-slate-800 text-cyan-500 focus:ring-cyan-500">
                    <span>Remember this device</span>
                </label>
            </div>

            <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-semibold text-sm shadow-lg shadow-cyan-500/20 hover:shadow-cyan-500/40 transition-all flex items-center justify-center gap-2 mt-2">
                <i data-lucide="log-in" class="w-4 h-4"></i>
                <span>Sign In to Control Panel</span>
            </button>
        </form>

        <div class="mt-6 pt-5 border-t border-slate-800 text-center text-xs text-slate-400">
            <span>Don't have an account yet?</span>
            <a href="{{ route('register') }}" class="text-cyan-400 hover:text-cyan-300 font-semibold ml-1">Create Account</a>
        </div>
    </div>
</div>
@endsection

