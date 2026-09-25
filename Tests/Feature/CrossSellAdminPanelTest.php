<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Lunar\Admin\Models\Staff;
use Lunar\Models\Product;
use Modules\CrossSell\Filament\Resources\CrossSellResource;
use Modules\CrossSell\Filament\Resources\CrossSellResource\Pages\ListCrossSells;
use Modules\CrossSell\Models\CrossSell;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('lunar'));

    $this->actingAs(Staff::factory()->create(['admin' => true]), 'staff');
});

/**
 * Create a Lunar product and store its names in the product indexer.
 */
function indexProduct(string $bg, string $en, bool $inStore = true): int
{
    $product = Product::factory()->create();

    DB::table('product_indexer')->updateOrInsert(
        ['product_id' => $product->id],
        ['name' => json_encode(['bg' => $bg, 'en' => $en]), 'price' => 0, 'stock' => 0, 'show_in_store' => $inStore ? 1 : 0],
    );

    return $product->id;
}

it('registers the cross-sell resource on the lunar panel', function () {
    expect(Filament::getPanel('lunar')->getResources())->toContain(CrossSellResource::class);
});

it('lists recommended products in position order', function () {
    $water = indexProduct('Вода', 'Water');
    $juice = indexProduct('Сок', 'Juice');

    $second = CrossSell::create(['product_id' => $juice, 'position' => 2]);
    $first = CrossSell::create(['product_id' => $water, 'position' => 1]);

    Livewire::test(ListCrossSells::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords([$first, $second], inOrder: true);
});

it('finds products by cyrillic and latin name and skips already added ones', function () {
    $water = indexProduct('Вода', 'Water');
    $vodka = indexProduct('Водка', 'Vodka');
    CrossSell::create(['product_id' => $vodka, 'position' => 1]);

    expect(CrossSellResource::searchProducts('Вод'))->toBe([$water => 'Вода'])
        ->and(CrossSellResource::searchProducts('wat'))->toBe([$water => 'Вода']);
});

it('only finds products shown in the web store', function () {
    indexProduct('Вода', 'Water', inStore: false);

    expect(CrossSellResource::searchProducts('Вод'))->toBe([]);
});

it('adds a product at the end of the list', function () {
    $water = indexProduct('Вода', 'Water');
    $juice = indexProduct('Сок', 'Juice');
    CrossSell::create(['product_id' => $water, 'position' => 1]);

    Livewire::test(ListCrossSells::class)
        ->callAction('create', data: ['product_id' => $juice])
        ->assertHasNoActionErrors();

    expect(CrossSell::where('product_id', $juice)->value('position'))->toBe(2);
});

it('rejects a product that is already recommended', function () {
    $water = indexProduct('Вода', 'Water');
    CrossSell::create(['product_id' => $water, 'position' => 1]);

    Livewire::test(ListCrossSells::class)
        ->callAction('create', data: ['product_id' => $water])
        ->assertHasActionErrors(['product_id' => 'unique']);
});

it('reorders recommended products', function () {
    $first = CrossSell::create(['product_id' => indexProduct('Вода', 'Water'), 'position' => 1]);
    $second = CrossSell::create(['product_id' => indexProduct('Сок', 'Juice'), 'position' => 2]);

    Livewire::test(ListCrossSells::class)
        ->call('toggleTableReordering')
        ->call('reorderTable', [$second->id, $first->id]);

    expect(CrossSell::query()->orderBy('position')->pluck('id')->all())->toBe([$second->id, $first->id]);
});

it('forbids staff without the manage-cross-sell permission', function () {
    $this->actingAs(Staff::factory()->create(['admin' => false]), 'staff');

    expect(CrossSellResource::canViewAny())->toBeFalse();
});
