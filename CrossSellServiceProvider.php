<?php

namespace Modules\CrossSell;

use Illuminate\Support\ServiceProvider;

/**
 * Service provider for the Cross Sell (recommended products) module.
 *
 * Registers translations and migrations. The admin UI is provided by
 * Filament\CrossSellPlugin, which the application registers on the Lunar panel.
 */
class CrossSellServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'cross-sell');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->publishes([__DIR__.'/resources/lang' => resource_path('lang/vendor/cross-sell')], 'modules-lang');
    }
}
