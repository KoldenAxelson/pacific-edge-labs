<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\EmailServiceInterface;
use App\Services\EmailService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Email service binding
        $this->app->singleton(EmailServiceInterface::class, EmailService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
