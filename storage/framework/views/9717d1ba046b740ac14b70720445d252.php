<?php $__env->startSection('title', 'Frequently Asked Questions & Knowledge Base'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-8 max-w-6xl mx-auto py-2">

    <!-- Hero Section -->
    <div class="relative overflow-hidden rounded-2xl p-6 sm:p-10 border-2 border-[#5A456B]" 
         style="background: radial-gradient(circle at 50% 0%, #2A1A3E 0%, #170F22 75%, #100A18 100%); box-shadow: 0 10px 30px rgba(0,0,0,0.7);">
        
        <div class="relative z-10 text-center max-w-2xl mx-auto space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#D4AF37]/15 border border-[#D4AF37]/40 text-[#FCE69B] text-xs font-cinzel font-bold tracking-widest uppercase">
                <i data-lucide="help-circle" class="w-3.5 h-3.5 text-[#FFD700]"></i>
                <span>Codex &amp; Knowledge Base</span>
            </div>

            <h1 class="text-2xl sm:text-4xl font-black font-cinzel text-white tracking-wide uppercase drop-shadow-md">
                Frequently Asked Questions
            </h1>

            <p class="text-xs sm:text-sm text-[#C8BDAA] leading-relaxed">
                Find clear answers to common questions about server gameplay, account security, downloads, rates, and donation privileges.
            </p>

            <!-- Search Console -->
            <form action="<?php echo e(route('faq.index')); ?>" method="GET" class="pt-2">
                <div class="relative max-w-xl mx-auto">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                        <i data-lucide="search" class="w-4 h-4 text-[#C9A96E]"></i>
                    </div>
                    <input type="text" name="q" value="<?php echo e($searchTerm); ?>" placeholder="Search questions, keywords, commands (e.g. rates, patcher, accounts)..." 
                           style="padding-left: 3.25rem !important; padding-right: 6.5rem !important; height: 48px;"
                           class="w-full bg-[#110B17] border border-[#6B537F] rounded-2xl text-xs sm:text-sm font-medium text-[#F3E5AB] placeholder:text-[#8D7D9A] focus:outline-none focus:border-[#D4AF37] focus:ring-1 focus:ring-[#D4AF37] transition-all shadow-inner">
                    <div class="absolute inset-y-0 right-1.5 flex items-center">
                        <button type="submit" class="chronicles-btn-gold py-2 px-4 text-xs cursor-pointer flex items-center gap-1 shadow">
                            <span>Search</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Ambient Glow -->
        <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-96 h-48 bg-[#D4AF37]/10 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- If Search is active: Search Results -->
    <?php if($searchResults !== null): ?>
        <div class="chronicles-panel p-5 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-[#352643]">
                <div class="flex items-center gap-2">
                    <i data-lucide="search" class="w-5 h-5 text-[#D4AF37]"></i>
                    <h2 class="text-sm font-bold text-white font-cinzel uppercase tracking-wide">
                        Search Results for "<?php echo e($searchTerm); ?>" (<?php echo e($searchResults->total()); ?>)
                    </h2>
                </div>
                <a href="<?php echo e(route('faq.index')); ?>" class="text-xs text-[#D4AF37] hover:text-[#FCE69B] flex items-center gap-1">
                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                    <span>Clear Search</span>
                </a>
            </div>

            <?php if($searchResults->count() > 0): ?>
                <div class="space-y-3" x-data="{ activeAccordion: null }">
                    <?php $__currentLoopData = $searchResults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="rounded-xl bg-[#140E1B] border border-[#3E2F4D] overflow-hidden transition-all">
                            <button type="button" @click="activeAccordion = (activeAccordion === <?php echo e($faq->id); ?> ? null : <?php echo e($faq->id); ?>)"
                                    class="w-full p-4 text-left flex items-center justify-between gap-4 cursor-pointer hover:bg-[#1E1528] transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-[#251A33] border border-[#5A456B] flex items-center justify-center shrink-0 text-[#FFE894]">
                                        <i data-lucide="help-circle" class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <span class="text-xs sm:text-sm font-bold text-white block"><?php echo e($faq->question); ?></span>
                                        <?php if($faq->category): ?>
                                            <span class="text-[10px] text-[#A898B5] font-cinzel"><?php echo e($faq->category->name); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <i data-lucide="chevron-down" class="w-4 h-4 text-[#C9A96E] shrink-0 transition-transform duration-200"
                                   :class="activeAccordion === <?php echo e($faq->id); ?> ? 'rotate-180' : ''"></i>
                            </button>

                            <div x-show="activeAccordion === <?php echo e($faq->id); ?>" x-collapse class="p-4 pt-0 border-t border-[#251A33] bg-[#0E0814] text-xs text-[#D2C7DD] space-y-3 leading-relaxed">
                                <div class="prose prose-invert max-w-none text-xs text-[#D2C7DD] pt-3">
                                    <?php echo $faq->answer; ?>

                                </div>
                                <div class="flex items-center justify-between pt-2 border-t border-[#1C1425] text-[11px] text-[#8C7B99]">
                                    <a href="<?php echo e(route('faq.show', $faq->slug)); ?>" class="text-[#D4AF37] hover:underline flex items-center gap-1 font-semibold">
                                        <span>Direct Link &amp; Details</span>
                                        <i data-lucide="arrow-right" class="w-3 h-3"></i>
                                    </a>
                                    <span>Updated <?php echo e($faq->updated_at->diffForHumans()); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="pt-3">
                    <?php echo e($searchResults->links()); ?>

                </div>
            <?php else: ?>
                <div class="py-10 text-center space-y-2">
                    <i data-lucide="search-x" class="w-10 h-10 text-[#6B537F] mx-auto"></i>
                    <p class="text-sm font-bold text-white">No answers matched your query</p>
                    <p class="text-xs text-[#A898B5]">Try checking your spelling or search for alternative keywords.</p>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Categories Grid -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-black font-cinzel text-white uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="folder" class="w-4 h-4 text-[#D4AF37]"></i>
                <span>Explore by Category</span>
            </h2>
            <span class="text-xs text-[#A898B5] font-mono"><?php echo e($categories->count()); ?> Categories</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('faq.category', $cat->slug)); ?>" 
                   class="group p-5 rounded-2xl bg-[#1C1425] border border-[#3E2F4D] hover:border-[#D4AF37]/60 hover:bg-[#231830] transition-all flex flex-col justify-between shadow-lg">
                    <div class="space-y-2.5">
                        <div class="w-10 h-10 rounded-xl bg-[#291D38] border border-[#5A456B] text-[#FFD700] flex items-center justify-center shrink-0 group-hover:scale-110 group-hover:border-[#D4AF37] transition-all shadow-inner">
                            <i data-lucide="<?php echo e($cat->icon ?: 'folder'); ?>" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold font-cinzel text-white group-hover:text-[#FFE894] transition-colors">
                                <?php echo e($cat->name); ?>

                            </h3>
                            <p class="text-xs text-[#A898B5] line-clamp-2 mt-1 leading-relaxed">
                                <?php echo e($cat->description ?: 'Browse questions and official guides in this category.'); ?>

                            </p>
                        </div>
                    </div>

                    <div class="pt-4 mt-3 border-t border-[#291D38] flex items-center justify-between text-[11px] font-mono text-[#C8BDAA]">
                        <span><?php echo e($cat->published_faqs_count); ?> <?php echo e(Str::plural('Question', $cat->published_faqs_count)); ?></span>
                        <span class="text-[#D4AF37] group-hover:translate-x-1 transition-transform flex items-center gap-1">
                            <span>Browse</span>
                            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                        </span>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <!-- Popular Questions Accordion -->
    <?php if($featuredFaqs->count() > 0): ?>
        <div class="chronicles-panel p-5 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-[#352643]">
                <h2 class="text-sm font-black font-cinzel text-white uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="star" class="w-4 h-4 text-[#FFD700]"></i>
                    <span>Popular &amp; Frequently Asked Questions</span>
                </h2>
                <span class="text-xs text-[#A898B5] font-mono">Top Community Topics</span>
            </div>

            <div class="space-y-3" x-data="{ activeAccordion: null }">
                <?php $__currentLoopData = $featuredFaqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="rounded-xl bg-[#140E1B] border border-[#3E2F4D] overflow-hidden transition-all">
                        <button type="button" @click="activeAccordion = (activeAccordion === <?php echo e($faq->id); ?> ? null : <?php echo e($faq->id); ?>)"
                                class="w-full p-4 text-left flex items-center justify-between gap-4 cursor-pointer hover:bg-[#1E1528] transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-[#251A33] border border-[#5A456B] flex items-center justify-center shrink-0 text-[#FFE894]">
                                    <i data-lucide="help-circle" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <span class="text-xs sm:text-sm font-bold text-white block"><?php echo e($faq->question); ?></span>
                                    <?php if($faq->category): ?>
                                        <span class="text-[10px] text-[#A898B5] font-cinzel"><?php echo e($faq->category->name); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-[#C9A96E] shrink-0 transition-transform duration-200"
                               :class="activeAccordion === <?php echo e($faq->id); ?> ? 'rotate-180' : ''"></i>
                        </button>

                        <div x-show="activeAccordion === <?php echo e($faq->id); ?>" x-collapse class="p-4 pt-0 border-t border-[#251A33] bg-[#0E0814] text-xs text-[#D2C7DD] space-y-3 leading-relaxed">
                            <div class="prose prose-invert max-w-none text-xs text-[#D2C7DD] pt-3">
                                <?php echo $faq->answer; ?>

                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-[#1C1425] text-[11px] text-[#8C7B99]">
                                <a href="<?php echo e(route('faq.show', $faq->slug)); ?>" class="text-[#D4AF37] hover:underline flex items-center gap-1 font-semibold">
                                    <span>Direct Link &amp; Details</span>
                                    <i data-lucide="arrow-right" class="w-3 h-3"></i>
                                </a>
                                <span>Updated <?php echo e($faq->updated_at->diffForHumans()); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Still Need Help Support CTA -->
    <div class="rounded-2xl p-6 sm:p-8 border border-[#5A456B] flex flex-col sm:flex-row items-center justify-between gap-6"
         style="background: linear-gradient(135deg, #1C1226 0%, #120A1A 100%); box-shadow: 0 4px 20px rgba(0,0,0,0.5);">
        <div class="flex items-center gap-4 text-center sm:text-left">
            <div class="w-12 h-12 rounded-2xl bg-[#D4AF37]/20 border border-[#D4AF37]/50 text-[#FFD700] flex items-center justify-center shrink-0 shadow-inner">
                <i data-lucide="life-buoy" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="text-base font-bold font-cinzel text-white uppercase tracking-wide">Still need help?</h3>
                <p class="text-xs text-[#C8BDAA] mt-0.5">
                    If you cannot find the answer you are looking for, our server support team and game masters are ready to assist.
                </p>
            </div>
        </div>

        <?php if(app(\App\Services\Module\ModuleManager::class)->isEnabled('support')): ?>
            <a href="<?php echo e(route('support.index')); ?>" class="chronicles-btn-gold py-2.5 px-6 font-cinzel font-bold text-xs uppercase tracking-wider shrink-0 flex items-center gap-2 shadow-lg">
                <i data-lucide="message-square" class="w-4 h-4"></i>
                <span>Contact Support</span>
            </a>
        <?php else: ?>
            <a href="<?php echo e(\App\Models\SiteSetting::get('social_discord', '#')); ?>" target="_blank" rel="noopener" class="chronicles-btn-gold py-2.5 px-6 font-cinzel font-bold text-xs uppercase tracking-wider shrink-0 flex items-center gap-2 shadow-lg">
                <i data-lucide="message-circle" class="w-4 h-4"></i>
                <span>Join Discord Support</span>
            </a>
        <?php endif; ?>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\aether\themes\aether-chronicles\views/faq/index.blade.php ENDPATH**/ ?>