<?php

namespace App\Providers;

use App\Helpers\HelperStream;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use App\Services\PasswordRecoveryService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('PasswordRecoveryService', function ($app) {
            return new PasswordRecoveryService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @throws \GetStream\StreamChat\StreamException
     */
    public function boot()
    {
        HelperStream::init();

        Collection::macro('toAssoc', function() {
            return $this->reduce(function ($assoc, $keyValuePair) {
                list($key, $value) = $keyValuePair;
                $assoc[$key] = $value;
                return $assoc;
            }, new static);
        });
    }
}
