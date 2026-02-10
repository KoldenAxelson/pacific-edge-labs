<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\EmailServiceInterface;
use App\Services\EmailService;
use App\Contracts\PaymentGatewayInterface;
use App\Services\Payment\MockPaymentGateway;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Email service binding
        $this->app->singleton(EmailServiceInterface::class, EmailService::class);

        // Payment gateway binding
        $this->app->singleton(PaymentGatewayInterface::class, function ($app) {
            // In production, this would check config and return the appropriate gateway
            // For now, always return MockPaymentGateway for development
            return new MockPaymentGateway();
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

