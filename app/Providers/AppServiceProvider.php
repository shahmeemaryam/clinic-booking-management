<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use App\Contracts\PaymentGateway;
use App\Services\Payments\SandboxPaymentGateway;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
         $this->app->bind(
        PaymentGateway::class,
        SandboxPaymentGateway::class
    );
    }

    public function boot(): void
    {
        $basePath = trim(
            parse_url(config('app.url'), PHP_URL_PATH) ?? '',
            '/'
        );

        Livewire::setUpdateRoute(function ($handle) use ($basePath) {
            return Route::post(
                '/' . ($basePath ? $basePath . '/' : '') . 'livewire/update',
                $handle
            )->name('livewire.update.custom');
        });
    }
}