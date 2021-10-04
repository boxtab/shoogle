<?php

namespace App\Providers;

use App\Helpers\HelperStream;
use App\Repositories\InviteRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use App\Repositories\TestRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
//        $this->app->singleton(HelperStream::class, function () {
//            return new HelperStream();
//        });

//        $this->app->bind(HelperStream::class, function ($app) {
//            return HelperStream::init();
//        });

        //
//        $this->app->bind(InviteRepositoryInterface::class, InviteRepository::class);
//        $this->app->bind(TestRepositoryInterface::class, TestRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @throws \GetStream\StreamChat\StreamException
     */
    public function boot()
    {
        HelperStream::init();

//        new HelperStream();
        Collection::macro('toAssoc', function() {
            return $this->reduce(function ($assoc, $keyValuePair) {
                list($key, $value) = $keyValuePair;
                $assoc[$key] = $value;
                return $assoc;
            }, new static);
        });
    }
}
