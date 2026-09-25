<?php

namespace Modules\CrossSell\Filament\Resources\CrossSellResource\Pages;

use Filament\Actions\CreateAction;
use Lunar\Admin\Support\Pages\BaseListRecords;
use Modules\CrossSell\Filament\Resources\CrossSellResource;
use Modules\CrossSell\Models\CrossSell;

/**
 * Recommended products list; products are added in a modal and appended at the end.
 */
class ListCrossSells extends BaseListRecords
{
    protected static string $resource = CrossSellResource::class;

    protected function getDefaultHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('cross-sell::panel.add'))
                ->mutateDataUsing(fn (array $data): array => [
                    ...$data,
                    'position' => (int) CrossSell::query()->max('position') + 1,
                ]),
        ];
    }
}
