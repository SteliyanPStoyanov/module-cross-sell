<?php

use Illuminate\Support\Facades\Schema;
use Modules\CrossSell\CrossSellServiceProvider;
use Spatie\Permission\Models\Permission;

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

it('creates the manage-cross-sell staff permission', function () {
    expect(Permission::where('name', 'manage-cross-sell')->where('guard_name', 'staff')->exists())->toBeTrue();
});

it('stores the position as an integer', function () {
    expect(Schema::getColumnType('lunar_cross_sells', 'position'))->toContain('int');
});
