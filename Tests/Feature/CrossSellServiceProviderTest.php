<?php

use Livewire\Livewire;
use Modules\CrossSell\CrossSellServiceProvider;
use Modules\CrossSell\Http\Livewire\Admin\Index;
use Modules\CrossSell\Http\Livewire\Components\ProductSearch;
use Modules\CrossSell\Http\Livewire\Components\Tree;

it('registers the service provider', function () {
    $providers = app()->getLoadedProviders();

    expect($providers)->toHaveKey(CrossSellServiceProvider::class);
});

it('loads translations with cross-sell namespace', function () {
    $translation = __('cross-sell::global.menu.recommended_products');

    expect($translation)->not->toBe('cross-sell::global.menu.recommended_products')
        ->and($translation)->toBeString();
});

it('loads bulgarian translations', function () {
    app()->setLocale('bg');

    expect(__('cross-sell::catalogue.index.title'))->toBe('Препоръчани продукти')
        ->and(__('cross-sell::catalogue.node.delete'))->toBe('Премахни');
});

it('loads english translations', function () {
    app()->setLocale('en');

    expect(__('cross-sell::catalogue.index.title'))->toBe('Recommended products')
        ->and(__('cross-sell::catalogue.node.delete'))->toBe('Remove');
});

it('loads notification translations', function () {
    app()->setLocale('en');

    expect(__('cross-sell::notifications.product_added'))->toBe('Product added successfully')
        ->and(__('cross-sell::notifications.product_removed'))->toBe('Product removed successfully');
});

it('loads views with cross-sell namespace', function () {
    $viewFinder = app('view');

    expect($viewFinder->exists('cross-sell::livewire.admin.index'))->toBeTrue()
        ->and($viewFinder->exists('cross-sell::livewire.components.tree'))->toBeTrue()
        ->and($viewFinder->exists('cross-sell::livewire.components.product-search'))->toBeTrue();
});

it('registers livewire components', function () {
    expect(Livewire::getClass('cross-sell.admin.index'))->toBe(Index::class)
        ->and(Livewire::getClass('cross-sell.components.tree'))->toBe(Tree::class)
        ->and(Livewire::getClass('cross-sell.components.product-search'))->toBe(ProductSearch::class);
});

it('has hub cross-sell index route registered', function () {
    expect(route('hub.cross-sell.index'))->toBeString()
        ->and(route('hub.cross-sell.index'))->toContain('cross-sell');
});

it('uses manage-cross-sell permission on routes', function () {
    $routes = app('router')->getRoutes();
    $route = $routes->getByName('hub.cross-sell.index');

    expect($route)->not->toBeNull();

    $middleware = $route->gatherMiddleware();

    expect($middleware)->toContain('can:manage-cross-sell');
});

it('loads permission translation keys', function () {
    expect(__('cross-sell::global.manage.cross-sell.title', [], 'en'))->toBe('Recommended products')
        ->and(__('cross-sell::global.manage.cross-sell.description', [], 'en'))->toBe('Manage recommended products');
});

it('loads migrations', function () {
    $migrator = app('migrator');
    $paths = $migrator->paths();

    $hasCrossSellMigrations = false;
    foreach ($paths as $path) {
        if (str_contains($path, 'CrossSell')) {
            $hasCrossSellMigrations = true;
            break;
        }
    }

    expect($hasCrossSellMigrations)->toBeTrue();
});
