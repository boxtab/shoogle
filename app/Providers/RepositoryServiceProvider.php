<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\InviteRepositoryInterface;
use App\Repositories\InviteRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(InviteRepositoryInterface::class, InviteRepository::class);
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
