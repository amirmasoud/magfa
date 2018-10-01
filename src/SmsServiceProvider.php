<?php

namespace Amirmasoud\Magfa;

use Illuminate\Support\ServiceProvider;

class SmsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Sms::class, function ($app) {
            return new Sms();
        });

        $this->app->alias(Sms::class, 'Magfa');
    }
}
