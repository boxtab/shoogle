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
//        $this->app->bind(TestRepositoryInterface::class, TestRepository::class);
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
