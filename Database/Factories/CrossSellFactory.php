<?php

namespace Modules\CrossSell\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\CrossSell\Models\CrossSell;

class CrossSellFactory extends Factory
{
    protected $model = CrossSell::class;

    public function definition(): array
    {
        return [
            'product_id' => fake()->unique()->numberBetween(1, 1000),
            'position' => fake()->numberBetween(1, 100),
        ];
    }
}
