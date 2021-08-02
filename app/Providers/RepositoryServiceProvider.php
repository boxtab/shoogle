<?php

namespace App\Providers;

use App\Repositories\InviteRepository;
use App\Repositories\InviteRepositoryInterface;
use App\Repositories\TestRepository;
use App\Repositories\TestRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        Log::info('Test before');
        $this->app->bind(InviteRepositoryInterface::class, InviteRepository::class);
        $this->app->bind(TestRepositoryInterface::class, TestRepository::class);
        Log::info('Test after');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
