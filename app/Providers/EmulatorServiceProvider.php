<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\EmulatorInterface;
use App\Contracts\PasswordHasherInterface;
use App\Contracts\Repositories\AccountRepositoryInterface;
use App\Contracts\Repositories\CharacterRepositoryInterface;
use App\Contracts\Repositories\GuildRepositoryInterface;
use App\Contracts\Repositories\RankingRepositoryInterface;
use App\Contracts\Repositories\ItemRepositoryInterface;
use App\Services\Emulator\EmulatorManager;
use App\Services\Emulator\EmulatorPasswordHasher;
use App\Repositories\AccountRepository;
use App\Repositories\CharacterRepository;
use App\Repositories\GuildRepository;
use App\Repositories\RankingRepository;
use App\Repositories\ItemRepository;

class EmulatorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Emulator Manager
        $this->app->singleton(EmulatorManager::class, function ($app) {
            return new EmulatorManager(config('emulator', []));
        });

        // Current Active Emulator Driver
        $this->app->singleton(EmulatorInterface::class, function ($app) {
            return $app->make(EmulatorManager::class)->driver();
        });

        // Password Hasher
        $this->app->singleton(PasswordHasherInterface::class, function ($app) {
            return new EmulatorPasswordHasher(config('emulator.password_hash'));
        });

        // Repositories
        $this->app->bind(AccountRepositoryInterface::class, AccountRepository::class);
        $this->app->bind(CharacterRepositoryInterface::class, CharacterRepository::class);
        $this->app->bind(GuildRepositoryInterface::class, GuildRepository::class);
        $this->app->bind(RankingRepositoryInterface::class, RankingRepository::class);
        $this->app->bind(ItemRepositoryInterface::class, ItemRepository::class);
    }

    public function boot(): void
    {
        //
    }
}

