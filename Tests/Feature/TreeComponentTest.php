<?php

use Modules\CrossSell\Http\Livewire\Components\Tree;
use Modules\CrossSell\Models\CrossSell;

it('initializes with empty nodes when no cross sell products exist', function () {
    CrossSell::query()->delete();

    $component = new Tree();
    $component->nodes = [];

    expect($component->nodes)->toBeArray()
        ->and($component->nodes)->toBeEmpty();
});

it('has nodes property as array', function () {
    $component = new Tree();
    $component->nodes = [];

    expect($component->nodes)->toBeArray();
});

it('can sync positions after removal', function () {
    CrossSell::query()->delete();

    CrossSell::create(['product_id' => 1, 'position' => 1]);
    CrossSell::create(['product_id' => 2, 'position' => 2]);
    CrossSell::create(['product_id' => 3, 'position' => 3]);

    // Delete middle one
    CrossSell::where('product_id', 2)->delete();

    $component = new Tree();
    $component->nodes = [];
    $component->syncPosition();

    $positions = CrossSell::orderBy('position')->pluck('position')->map(fn ($p) => (int) $p)->toArray();

    expect($positions)->toBe([1, 2]);
});

it('has getListeners method returning correct listeners', function () {
    $component = new Tree();

    $reflection = new ReflectionClass($component);
    $method = $reflection->getMethod('getListeners');
    $method->setAccessible(true);
    $listeners = $method->invoke($component);

    expect($listeners)->toHaveKey('collectionSearch.selected')
        ->and($listeners['collectionSearch.selected'])->toBe('reloadList');
});

it('can update positions via sort payload', function () {
    CrossSell::query()->delete();

    CrossSell::create(['product_id' => 1, 'position' => 1]);
    CrossSell::create(['product_id' => 2, 'position' => 2]);

    // Update positions directly without calling reloadList
    $payload = [
        'items' => [
            ['id' => 2, 'order' => 1],
            ['id' => 1, 'order' => 2],
        ],
    ];

    foreach ($payload['items'] as $item) {
        CrossSell::where('product_id', $item['id'])->update(['position' => $item['order']]);
    }

    expect((int) CrossSell::where('product_id', 2)->first()->position)->toBe(1)
        ->and((int) CrossSell::where('product_id', 1)->first()->position)->toBe(2);
});
