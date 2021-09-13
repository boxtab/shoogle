<?php

namespace App\Providers;

use App\Repositories\InviteRepository;
use App\Repositories\InviteRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use App\Repositories\TestRepository;
use App\Repositories\TestRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
//        $this->app->bind(InviteRepositoryInterface::class, InviteRepository::class);
//        $this->app->bind(TestRepositoryInterface::class, TestRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Collection::macro('toAssoc', function() {
            return $this->reduce(function ($assoc, $keyValuePair) {
                list($key, $value) = $keyValuePair;
                $assoc[$key] = $value;
                return $assoc;
            }, new static);
        });
    }
}
