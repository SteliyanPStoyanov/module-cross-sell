<?php

namespace Modules\CrossSell\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\CrossSell\Filament\Resources\CrossSellResource;

/**
 * Filament plugin that adds recommended products management to the Lunar admin panel.
 *
 * Register it from the application, as Lunar recommends for add-ons:
 * LunarPanel::panel(fn ($panel) => $panel->plugin(CrossSellPlugin::make()))->register();
 */
class CrossSellPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'cross-sell';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            CrossSellResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
