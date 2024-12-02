<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\MyEncrypter;

class MyEncryptionServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('my-encrypter', function ($app) {
            $key = base64_decode(env('MY_ENCRYPTION_KEY'));

            if (empty($key)) {
                throw new \RuntimeException('Encryption key is not set or invalid.');
            }
            return new MyEncrypter($key);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
