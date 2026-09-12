<?php

namespace App\Filament\Plugins;

use Filament\Contracts\Plugin;
use Filament\Panel;

class TahunFilterPlugin implements Plugin
{
    public function getId(): string
    {
        return "tahun-filter";
    }

    public function register(Panel $panel): void
    {
        $panel->renderHook(
            "panels::global-search.before",
            fn () => view("filament.plugins.tahun-filter")
        );
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }
}
