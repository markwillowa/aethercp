@extends('layouts.app')

@section('title', 'Submit Support Ticket')

@section('content')
<div class="chronicles-container py-10 space-y-8">

    <!-- Top Wood Board Header -->
    <div class="chronicles-wood-board flex flex-col md:flex-row md:items-center justify-between gap-6 p-6 shadow-2xl">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('support.index') }}" class="text-xs text-[#D4AF37] hover:text-[#FFE4A0] font-cinzel font-bold flex items-center gap-1">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Support Center</span>
                </a>
                <span class="text-slate-500">•</span>
                <span class="text-[10px] text-[#A898B5] font-mono uppercase">New Ticket</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black font-cinzel text-[#FCE69B] tracking-wider uppercase mt-1">
                Submit Support Ticket
            </h1>
            <p class="text-xs text-[#C8BDAA] mt-0.5">Please provide thorough details so our game masters and technical staff can resolve your inquiry swiftly.</p>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-950/60 border border-rose-500/50 text-rose-300 text-xs flex items-center gap-3 shadow-lg">
            <i data-lucide="alert-triangle" class="w-5 h-5 shrink-0 text-rose-400"></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-950/60 border border-rose-500/50 text-rose-300 text-xs space-y-1 shadow-lg">
            <div class="font-bold flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-400"></i>
                <span>Please correct the errors below:</span>
            </div>
            <ul class="list-disc list-inside pl-2 space-y-0.5 text-[11px]">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Ticket Submission Form -->
    <div class="chronicles-panel p-6 sm:p-8" style="background: linear-gradient(180deg, #1C1525 0%, #150F1D 100%); border: 2px solid #5A456B; box-shadow: 0 6px 25px rgba(0,0,0,0.7);">
        <form action="{{ route('support.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Subject -->
            <div>
                <label class="block text-xs font-bold text-[#FFD700] uppercase font-cinzel mb-1.5 flex items-center gap-1.5">
                    <span>Subject / Short Summary</span>
                    <span class="text-rose-400">*</span>
                </label>
                <input type="text" name="subject" value="{{ old('subject') }}" required placeholder="e.g. Unable to retrieve storage item after server maintenance..." 
                       class="w-full px-4 py-2.5 bg-[#100B17] border border-[#6B537F] rounded-xl text-xs font-medium text-[#F3E5AB] placeholder:text-[#8D7D9A] focus:outline-none focus:border-[#D4AF37] focus:ring-1 focus:ring-[#D4AF37] transition-all">
                <p class="text-[10px] text-[#A898B5] mt-1">Provide a concise, descriptive title for the issue you are experiencing.</p>
            </div>

            <!-- Category, Priority & Character Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Category -->
                <div>
                    <label class="block text-xs font-bold text-[#FFD700] uppercase font-cinzel mb-1.5 flex items-center gap-1.5">
                        <span>Category</span>
                        <span class="text-rose-400">*</span>
                    </label>
                    <select name="category" required class="w-full px-3 py-2.5 bg-[#100B17] border border-[#6B537F] rounded-xl text-xs font-medium text-[#EDE4D2] focus:outline-none focus:border-[#D4AF37] cursor-pointer">
                        <option value="account" {{ old('category') === 'account' ? 'selected' : '' }}>Account Issues</option>
                        <option value="character" {{ old('category') === 'character' ? 'selected' : '' }}>Character Issues</option>
                        <option value="in_game" {{ old('category') === 'in_game' ? 'selected' : '' }}>In-Game Issues</option>
                        <option value="technical" {{ old('category') === 'technical' ? 'selected' : '' }}>Technical Support</option>
                        <option value="donation" {{ old('category') === 'donation' ? 'selected' : '' }}>Payment / Donation Issues</option>
                        <option value="bug_report" {{ old('category') === 'bug_report' ? 'selected' : '' }}>Bug Reports</option>
                        <option value="player_report" {{ old('category') === 'player_report' ? 'selected' : '' }}>Report a Player</option>
                        <option value="other" {{ old('category') === 'other' || !old('category') ? 'selected' : '' }}>Other Inquiries</option>
                    </select>
                </div>

                <!-- Priority -->
                <div>
                    <label class="block text-xs font-bold text-[#FFD700] uppercase font-cinzel mb-1.5 flex items-center gap-1.5">
                        <span>Priority Level</span>
                        <span class="text-rose-400">*</span>
                    </label>
                    <select name="priority" required class="w-full px-3 py-2.5 bg-[#100B17] border border-[#6B537F] rounded-xl text-xs font-medium text-[#EDE4D2] focus:outline-none focus:border-[#D4AF37] cursor-pointer">
                        <option value="low" {{ old('priority', $settings['default_priority']) === 'low' ? 'selected' : '' }}>Low (General question)</option>
                        <option value="normal" {{ old('priority', $settings['default_priority']) === 'normal' ? 'selected' : '' }}>Normal (Standard inquiry)</option>
                        <option value="high" {{ old('priority', $settings['default_priority']) === 'high' ? 'selected' : '' }}>High (Important game block)</option>
                        <option value="urgent" {{ old('priority', $settings['default_priority']) === 'urgent' ? 'selected' : '' }}>Urgent (Critical compromise / loss)</option>
                    </select>
                </div>

                <!-- Character Involved -->
                <div>
                    <label class="block text-xs font-bold text-[#FFD700] uppercase font-cinzel mb-1.5 flex items-center gap-1.5">
                        <span>Character Involved</span>
                    </label>
                    <select name="char_id" class="w-full px-3 py-2.5 bg-[#100B17] border border-[#6B537F] rounded-xl text-xs font-medium text-[#EDE4D2] focus:outline-none focus:border-[#D4AF37] cursor-pointer">
                        <option value="">None / Account-Wide</option>
                        @foreach($characters as $c)
                            <option value="{{ $c->char_id }}" {{ old('char_id') == $c->char_id ? 'selected' : '' }}>
                                {{ $c->name }} (Lv. {{ $c->base_level }}/{{ $c->job_level }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Optional Incident Details -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                <div>
                    <label class="block text-[11px] font-bold text-[#C8BDAA] uppercase font-cinzel mb-1">
                        Related Reference (Optional)
                    </label>
                    <input type="text" name="related_reference" value="{{ old('related_reference') }}" placeholder="e.g. PayPal TX, Trade Code..." 
                           class="w-full px-3 py-2 bg-[#100B17] border border-[#5A456B] rounded-lg text-xs text-[#EDE4D2] placeholder:text-[#7A6B85] focus:outline-none focus:border-[#D4AF37]">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-[#C8BDAA] uppercase font-cinzel mb-1">
                        Map Name (Optional)
                    </label>
                    <input type="text" name="map_name" value="{{ old('map_name') }}" placeholder="e.g. prontera, pay_dun02..." 
                           class="w-full px-3 py-2 bg-[#100B17] border border-[#5A456B] rounded-lg text-xs text-[#EDE4D2] placeholder:text-[#7A6B85] focus:outline-none focus:border-[#D4AF37]">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-[#C8BDAA] uppercase font-cinzel mb-1">
                        Date &amp; Time of Incident
                    </label>
                    <input type="text" name="incident_time" value="{{ old('incident_time') }}" placeholder="e.g. Nov 24 at ~6:30 PM..." 
                           class="w-full px-3 py-2 bg-[#100B17] border border-[#5A456B] rounded-lg text-xs text-[#EDE4D2] placeholder:text-[#7A6B85] focus:outline-none focus:border-[#D4AF37]">
                </div>
            </div>

            <!-- Message Content -->
            <div>
                <label class="block text-xs font-bold text-[#FFD700] uppercase font-cinzel mb-1.5 flex items-center gap-1.5">
                    <span>Detailed Explanation</span>
                    <span class="text-rose-400">*</span>
                </label>
                <textarea name="message" rows="6" required placeholder="Describe what happened, step-by-step reproduction details, error messages, or context that helps staff assist you..." 
                          class="w-full p-4 bg-[#100B17] border border-[#6B537F] rounded-xl text-xs font-medium text-[#F3E5AB] placeholder:text-[#8D7D9A] focus:outline-none focus:border-[#D4AF37] focus:ring-1 focus:ring-[#D4AF37] transition-all leading-relaxed">{{ old('message') }}</textarea>
            </div>

            <!-- File Attachments -->
            @if($settings['allow_attachments'])
                <div class="p-4 rounded-xl bg-[#140E1B] border border-[#4A3A5A] space-y-2">
                    <label class="block text-xs font-bold text-[#FFE894] uppercase font-cinzel flex items-center gap-1.5">
                        <i data-lucide="paperclip" class="w-4 h-4 text-[#FFD700]"></i>
                        <span>Attach Screenshots or Supporting Files</span>
                    </label>
                    <input type="file" name="attachments[]" multiple 
                           class="block w-full text-xs text-[#C8BDAA] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-cinzel file:font-bold file:bg-[#2A1D36] file:text-[#FFE894] hover:file:bg-[#382647] cursor-pointer">
                    <p class="text-[10px] text-[#A898B5]">
                        Allowed formats: <strong class="text-[#E2D9EB] uppercase">{{ $settings['allowed_extensions'] }}</strong> • Max size: <strong class="text-[#E2D9EB]">{{ number_format($settings['max_attachment_size_kb'] / 1024, 1) }} MB</strong> per file (Max {{ $settings['max_attachments_per_msg'] }} files).
                    </p>
                </div>
            @endif

            <!-- Submit Button Row -->
            <div class="flex items-center justify-between pt-4 border-t border-[#352643]">
                <a href="{{ route('support.index') }}" class="chronicles-btn-dark py-2.5 px-5 cursor-pointer text-xs font-cinzel font-bold">
                    Cancel
                </a>
                <button type="submit" class="chronicles-btn-gold py-2.5 px-7 cursor-pointer text-xs font-cinzel font-black tracking-wide shadow-xl flex items-center gap-2">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    <span>SUBMIT SUPPORT TICKET</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
