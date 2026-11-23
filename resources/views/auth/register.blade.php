@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto px-4 sm:px-6 py-8">
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-8 backdrop-blur-xl shadow-2xl aether-glow-cyan">
        <div class="text-center mb-6">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-cyan-500 to-blue-600 flex items-center justify-center mx-auto mb-3 shadow-lg shadow-cyan-500/30">
                <i data-lucide="user-plus" class="w-6 h-6 text-slate-950 font-bold"></i>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Create Free Account</h1>
            <p class="text-xs text-slate-400 mt-1">One account for both game client and web panel</p>
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

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <label for="username" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Username (Game & Web)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i data-lucide="user" class="w-4 h-4"></i>
                    </span>
                    <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-950/70 border border-slate-800 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 rounded-xl text-sm text-slate-100 placeholder-slate-500 transition-colors"
                        placeholder="Choose a username">
                </div>
                <p class="text-[11px] text-slate-500 mt-1">4-23 alphanumeric characters or underscore.</p>
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Email Address</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i data-lucide="mail" class="w-4 h-4"></i>
                    </span>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-950/70 border border-slate-800 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 rounded-xl text-sm text-slate-100 placeholder-slate-500 transition-colors"
                        placeholder="adventurer@example.com">
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

            <div>
                <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Confirm Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                    </span>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-950/70 border border-slate-800 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 rounded-xl text-sm text-slate-100 placeholder-slate-500 transition-colors"
                        placeholder="••••••••">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="gender" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Gender</label>
                    <select id="gender" name="gender" class="w-full px-3 py-2.5 bg-slate-950/70 border border-slate-800 focus:border-cyan-500 rounded-xl text-sm text-slate-100">
                        <option value="M" {{ old('gender') === 'M' ? 'selected' : '' }}>Male (M)</option>
                        <option value="F" {{ old('gender') === 'F' ? 'selected' : '' }}>Female (F)</option>
                    </select>
                </div>

                <div>
                    <label for="birthdate" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Birthdate</label>
                    <input id="birthdate" type="date" name="birthdate" value="{{ old('birthdate', '2000-01-01') }}"
                        class="w-full px-3 py-2.5 bg-slate-950/70 border border-slate-800 focus:border-cyan-500 rounded-xl text-sm text-slate-100">
                </div>
            </div>

            <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 font-semibold text-sm shadow-lg shadow-cyan-500/20 hover:shadow-cyan-500/40 transition-all flex items-center justify-center gap-2 mt-2">
                <i data-lucide="check" class="w-4 h-4"></i>
                <span>Complete Registration</span>
            </button>
        </form>

        <div class="mt-6 pt-5 border-t border-slate-800 text-center text-xs text-slate-400">
            <span>Already have an account?</span>
            <a href="{{ route('login') }}" class="text-cyan-400 hover:text-cyan-300 font-semibold ml-1">Sign In</a>
        </div>
    </div>
</div>
@endsection

