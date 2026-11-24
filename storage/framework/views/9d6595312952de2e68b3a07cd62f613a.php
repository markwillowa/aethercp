<?php $__env->startSection('content'); ?>
<div class="chronicles-container py-10 space-y-8">

    <!-- Header Overview Card -->
    <div class="chronicles-wood-board flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-b from-[#FFF3D6] to-[#C99A6B] border-2 border-[#F3C474] flex items-center justify-center text-3xl font-black font-cinzel text-[#4A2200] shadow-xl">
                <?php echo e(strtoupper(substr($user->username, 0, 1))); ?>

            </div>
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="font-cinzel text-2xl font-black text-[#FFE4A0] tracking-wide uppercase">
                        <?php echo e($user->username); ?>

                    </h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#3B1F0A] text-[#FFE4A0] border border-[#C99A6B]">
                        <?php echo e($user->role); ?>

                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-[#2D5A1E] text-[#D8FFB8] border border-[#6BB336]">
                        Account #<?php echo e($user->account_id); ?>

                    </span>
                </div>
                <p class="text-xs text-[#EAD2B0] mt-1.5 flex items-center gap-3 flex-wrap">
                    <span>Joined: <strong class="text-white"><?php echo e($user->created_at->format('M d, Y')); ?></strong></span>
                    <span>•</span>
                    <span>Last Login: <strong class="text-white"><?php echo e($user->last_login_at?->diffForHumans() ?? 'Active now'); ?></strong></span>
                    <span>•</span>
                    <span>Cash Points: <strong class="text-[#FFE600] font-bold"><?php echo e(number_format($user->donation_points ?? 0)); ?></strong></span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="<?php echo e(route('account.characters')); ?>" class="chronicles-btn-wood text-xs py-2 px-4 w-auto">
                <i data-lucide="users" class="w-4 h-4 text-[#FFD700]"></i>
                <span>Adventurer Roster (<?php echo e($charCount); ?>)</span>
            </a>
            <?php if($user->isAdmin()): ?>
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="chronicles-btn-red text-xs py-2 px-4">
                    <i data-lucide="shield-alert" class="w-4 h-4"></i>
                    <span>Admin Panel</span>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Security & Password Forms -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Change Password -->
        <div class="chronicles-panel-ivy pt-6 space-y-4">
            <div class="chronicles-ribbon-header">
                <span>Security</span>
            </div>

            <div class="border-b border-[#DECBB1] pb-2 mt-2">
                <h3 class="font-cinzel text-sm font-bold text-[#4A2200] uppercase">Update Password</h3>
            </div>

            <form method="POST" action="<?php echo e(route('account.password')); ?>" class="space-y-3.5">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-xs font-cinzel font-bold text-[#4A3319] uppercase mb-1">Current Password</label>
                    <input type="password" name="current_password" required class="chronicles-input" placeholder="••••••••">
                </div>
                <div>
                    <label class="block text-xs font-cinzel font-bold text-[#4A3319] uppercase mb-1">New Password</label>
                    <input type="password" name="password" required class="chronicles-input" placeholder="Min 6 characters">
                </div>
                <div>
                    <label class="block text-xs font-cinzel font-bold text-[#4A3319] uppercase mb-1">Confirm New Password</label>
                    <input type="password" name="password_confirmation" required class="chronicles-input" placeholder="Repeat new password">
                </div>
                <div class="pt-2">
                    <button type="submit" class="chronicles-btn-red w-full text-xs">
                        <i data-lucide="key" class="w-3.5 h-3.5"></i>
                        <span>Update Password</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Update Email -->
        <div class="chronicles-panel-ivy pt-6 space-y-4">
            <div class="chronicles-ribbon-header">
                <span>Communication</span>
            </div>

            <div class="border-b border-[#DECBB1] pb-2 mt-2">
                <h3 class="font-cinzel text-sm font-bold text-[#4A2200] uppercase">Update Email</h3>
            </div>

            <form method="POST" action="<?php echo e(route('account.email')); ?>" class="space-y-3.5">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-xs font-cinzel font-bold text-[#4A3319] uppercase mb-1">Registered Email</label>
                    <input type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" required class="chronicles-input">
                </div>
                <div>
                    <label class="block text-xs font-cinzel font-bold text-[#4A3319] uppercase mb-1">Account Password</label>
                    <input type="password" name="password" required class="chronicles-input" placeholder="Authorize change">
                </div>
                <div class="pt-2">
                    <button type="submit" class="chronicles-btn-wood text-xs">
                        <i data-lucide="mail" class="w-3.5 h-3.5 text-[#FFD700]"></i>
                        <span>Save Email</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Quick Info & Support -->
        <div class="chronicles-panel-ivy pt-6 space-y-4">
            <div class="chronicles-ribbon-header">
                <span>Directives</span>
            </div>

            <div class="border-b border-[#DECBB1] pb-2 mt-2">
                <h3 class="font-cinzel text-sm font-bold text-[#4A2200] uppercase">Account Directives</h3>
            </div>

            <div class="space-y-3 text-xs text-[#5A3D28] leading-relaxed">
                <p>
                    • Keep your game credentials strictly confidential. Staff will never request your secret password.
                </p>
                <p>
                    • Use the <strong class="text-[#8B5A2B]">Character Roster</strong> to reset stuck positions back to Prontera if your client encounters a crash.
                </p>
                <p>
                    • Claim donation transaction codes instantly under <a href="<?php echo e(route('donations.index')); ?>" class="text-[#D4441D] font-bold hover:underline">Donations</a>.
                </p>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\aether\themes\aether-chronicles\views/account/profile.blade.php ENDPATH**/ ?>