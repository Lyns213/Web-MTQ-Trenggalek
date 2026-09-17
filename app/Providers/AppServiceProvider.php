<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Request;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \Filament\Http\Responses\Auth\Contracts\LoginResponse::class,
            \App\Http\Responses\Auth\LoginResponse::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();

        \Filament\Notifications\Notification::configureUsing(function (\Filament\Notifications\Notification $notification): void {
            $notification->duration(1800);
        });

        if (App::environment(['staging', 'production'])) {
            URL::forceScheme('https');
        }
        
        Request::macro('hasValidSignature', function ($absolute = true) {
            $uploading = strpos(URL::current(), '/livewire/upload-file');
            $previewing = strpos(URL::current(), '/livewire/preview-file');
            if ($uploading || $previewing) {
                return true;
            }
        });
    }
}
