<?php $__env->startSection('content'); ?>
<div class="chronicles-container py-12 flex items-center justify-center min-h-[70vh]">
    <div class="w-full max-w-md">
        <div class="chronicles-panel-ivy pt-8 px-6 sm:px-8">
            <div class="chronicles-ribbon-header">
                <span>Enter The Realm</span>
            </div>

            <div class="text-center mb-6 mt-1">
                <h2 class="font-cinzel text-xl font-bold text-[#4A2200] uppercase tracking-wider">
                    Account Login
                </h2>
                <p class="text-xs text-[#795E43] mt-1">
                    Authenticate to manage your characters and donations
                </p>
            </div>

            <form method="POST" action="<?php echo e(route('login')); ?>" class="space-y-4">
                <?php echo csrf_field(); ?>

                <div>
                    <label class="block text-xs font-cinzel font-bold text-[#4A3319] uppercase tracking-wider mb-1">
                        Game Username
                    </label>
                    <input type="text" 
                           name="username" 
                           value="<?php echo e(old('username')); ?>" 
                           required 
                           autofocus
                           placeholder="Enter username" 
                           class="chronicles-input">
                    <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="text-xs text-red-600 block mt-1 font-semibold"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-xs font-cinzel font-bold text-[#4A3319] uppercase tracking-wider mb-1">
                        Secret Password
                    </label>
                    <input type="password" 
                           name="password" 
                           required 
                           placeholder="••••••••" 
                           class="chronicles-input">
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="text-xs text-red-600 block mt-1 font-semibold"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-1.5 cursor-pointer text-[#4A3319] hover:text-[#211E27]">
                        <input type="checkbox" name="remember" class="rounded border-[#CBB493] text-[#D88914]">
                        <span>Remember session</span>
                    </label>
                    <?php if(Route::has('password.request')): ?>
                        <a href="<?php echo e(route('password.request')); ?>" class="text-[#8B5A2B] hover:text-[#D4441D] italic underline decoration-dotted">
                            Forgot Password?
                        </a>
                    <?php endif; ?>
                </div>

                <div class="pt-2">
                    <button type="submit" class="chronicles-btn-red w-full">
                        <i data-lucide="key" class="w-4 h-4"></i>
                        <span>Authenticate</span>
                    </button>
                </div>

                <div class="text-center pt-3 border-t border-[#DECBB1] text-xs text-[#6B5541]">
                    <span>Need an account?</span>
                    <a href="<?php echo e(route('register')); ?>" class="font-bold text-[#D4441D] hover:underline ml-1">
                        Create One Now
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\aether\themes\aether-chronicles\views/auth/login.blade.php ENDPATH**/ ?>