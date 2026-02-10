<?php

use Modules\CrossSell\Models\CrossSell;

it('can create a cross sell entry', function () {
    $crossSell = CrossSell::create([
        'product_id' => 1,
        'position' => 1,
    ]);

    expect($crossSell)->toBeInstanceOf(CrossSell::class)
        ->and($crossSell->product_id)->toBe(1)
        ->and($crossSell->position)->toBe(1);
});

it('has fillable attributes', function () {
    $crossSell = new CrossSell();

    expect($crossSell->getFillable())->toContain('product_id')
        ->and($crossSell->getFillable())->toContain('position');
});

it('can update position', function () {
    $crossSell = CrossSell::create([
        'product_id' => 2,
        'position' => 1,
    ]);

    $crossSell->update(['position' => 5]);

    expect((int) $crossSell->fresh()->position)->toBe(5);
});

it('can be deleted', function () {
    $crossSell = CrossSell::create([
        'product_id' => 3,
        'position' => 1,
    ]);

    $id = $crossSell->id;
    $crossSell->delete();

    expect(CrossSell::find($id))->toBeNull();
});

it('can order by position', function () {
    CrossSell::query()->delete();

    CrossSell::create(['product_id' => 10, 'position' => 3]);
    CrossSell::create(['product_id' => 11, 'position' => 1]);
    CrossSell::create(['product_id' => 12, 'position' => 2]);

    $ordered = CrossSell::orderBy('position')->pluck('product_id')->toArray();

    expect($ordered)->toBe([11, 12, 10]);
});

it('can find by product_id', function () {
    CrossSell::query()->delete();

    CrossSell::create(['product_id' => 100, 'position' => 1]);

    $found = CrossSell::where('product_id', 100)->first();

    expect($found)->not->toBeNull()
        ->and($found->product_id)->toBe(100);
});

it('can use updateOrCreate', function () {
    CrossSell::query()->delete();

    CrossSell::updateOrCreate(
        ['product_id' => 200],
        ['position' => 1]
    );

    expect(CrossSell::where('product_id', 200)->exists())->toBeTrue();

    CrossSell::updateOrCreate(
        ['product_id' => 200],
        ['position' => 5]
    );

    expect(CrossSell::where('product_id', 200)->count())->toBe(1)
        ->and((int) CrossSell::where('product_id', 200)->first()->position)->toBe(5);
});
