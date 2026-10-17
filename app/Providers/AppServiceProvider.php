<?php

namespace App\Providers;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use App\Services\Payment\ManualPaymentService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            PaymentGatewayInterface::class,
            function ($app) {
                return $app->make(ManualPaymentService::class);
            }
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
