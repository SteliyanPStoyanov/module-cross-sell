<?php

namespace Modules\CrossSell;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Lunar\Hub\Auth\Manifest;
use Lunar\Hub\Auth\Permission;
use Lunar\Hub\Facades\Menu;
use Modules\AdminMenu\Support\TranslatableString;
use Modules\CrossSell\Http\Livewire\Admin\Index;
use Modules\CrossSell\Http\Livewire\Components\ProductSearch;
use Modules\CrossSell\Http\Livewire\Components\Tree;

/**
 * Service provider for the CrossSell module.
 *
 * This module provides functionality for managing recommended/cross-sell
 * products in the admin panel. Products can be added, reordered, and
 * removed from the cross-sell list which is displayed to customers.
 */
class CrossSellServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * Registers translations, permissions, menu items, routes,
     * views, migrations, and Livewire components.
     */
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'cross-sell');

        $this->registerPermissions();
        $this->registerMenuItems();

        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'cross-sell');
        $this->publishes([__DIR__.'/resources/lang' => resource_path('lang/vendor/cross-sell')], 'modules-lang');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->registerLivewireComponents();
    }

    /**
     * Register module permissions with the Lunar admin hub.
     */
    protected function registerPermissions(): void
    {
        $this->app->booted(function () {
            $manifest = $this->app->get(Manifest::class);
            $manifest->addPermission(function (Permission $permission) {
                $permission->name = __('cross-sell::global.manage.cross-sell.title');
                $permission->handle = 'manage-cross-sell';
                $permission->description = __('cross-sell::global.manage.cross-sell.description');
            });
        });
    }

    /**
     * Register menu items in the Lunar admin hub sidebar.
     */
    protected function registerMenuItems(): void
    {
        $slot = Menu::slot('sidebar');

        $catalogueGroup = $slot
            ->group('hub.catalogue')
            ->name(new TranslatableString('adminhub::menu.sidebar.catalogue'));

        $catalogueGroup->addItem(function ($menuItem) {
            $menuItem
                ->name(new TranslatableString('cross-sell::global.menu.recommended_products'))
                ->handle('hub.cross-sell')
                ->route('hub.cross-sell.index')
                ->icon('beaker');
        });
    }

    /**
     * Register Livewire components for this module.
     */
    protected function registerLivewireComponents(): void
    {
        Livewire::component('cross-sell.admin.index', Index::class);
        Livewire::component('cross-sell.components.tree', Tree::class);
        Livewire::component('cross-sell.components.product-search', ProductSearch::class);
    }
}
