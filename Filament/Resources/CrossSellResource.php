<?php

namespace Modules\CrossSell\Filament\Resources;

use App\Models\ProductsIndexer;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Lunar\Admin\Support\Resources\BaseResource;
use Modules\CrossSell\Filament\Resources\CrossSellResource\Pages\ListCrossSells;
use Modules\CrossSell\Models\CrossSell;

/**
 * Lunar admin resource for the recommended (cross-sell) products shown on product pages.
 *
 * Replaces the Admin Hub cross-sell page from Lunar 0.8. Access requires the
 * "manage-cross-sell" staff permission (admins pass automatically).
 */
class CrossSellResource extends BaseResource
{
    protected static ?string $permission = 'manage-cross-sell';

    protected static ?string $model = CrossSell::class;

    protected static ?int $navigationSort = 40;

    /**
     * Deny every resource action without the staff permission.
     *
     * Filament 4 authorizes through getAuthorizationResponse() (model policies),
     * which bypasses the permission check in Lunar's BaseResource::can(), so the
     * permission would otherwise only hide the navigation item.
     */
    public static function getAuthorizationResponse(string $action, ?Model $record = null): Response
    {
        if (!static::hasPermission()) {
            return Response::deny();
        }

        return parent::getAuthorizationResponse($action, $record);
    }

    public static function getLabel(): string
    {
        return __('cross-sell::panel.label');
    }

    public static function getPluralLabel(): string
    {
        return __('cross-sell::panel.plural_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('cross-sell::global.menu.recommended_products');
    }

    public static function getNavigationIcon(): string|Heroicon|null
    {
        return Heroicon::OutlinedSparkles;
    }

    /**
     * Shared admin navigation group for the Fitfeast modules.
     *
     * Filament groups items by label, so every module returning the same "Modules" label
     * ends up in one group.
     */
    public static function getNavigationGroup(): ?string
    {
        return __('cross-sell::panel.navigation_group');
    }

    /**
     * @return array<int, mixed>
     */
    protected static function getMainFormComponents(): array
    {
        return [
            Select::make('product_id')
                ->label(__('cross-sell::panel.product'))
                ->required()
                ->searchable()
                ->getSearchResultsUsing(fn (string $search): array => static::searchProducts($search))
                ->getOptionLabelUsing(fn ($value): ?string => ProductsIndexer::query()->where('product_id', $value)->first()?->getTranslatedName())
                ->unique(CrossSell::class, 'product_id'),
        ];
    }

    public static function getDefaultTable(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('product.thumbnail')
                    ->label('')
                    ->square(),
                TextColumn::make('product_name')
                    ->label(__('cross-sell::panel.product'))
                    ->state(fn (CrossSell $record): ?string => $record->product?->getTranslatedName()),
            ])
            ->defaultSort('position')
            ->reorderable('position')
            ->paginated(false)
            ->recordActions([
                DeleteAction::make(),
            ]);
    }

    /**
     * Products whose Bulgarian or English name matches the search term, keyed by product id.
     *
     * Names are JSON, so the lookup goes through the JSON path (a plain LIKE on the
     * column would miss Cyrillic, which is stored escaped).
     *
     * @return array<int, string>
     */
    public static function searchProducts(string $search): array
    {
        $existing = CrossSell::query()->pluck('product_id');

        return ProductsIndexer::query()
            ->whereNotIn('product_id', $existing)
            ->where(fn (Builder $query) => $query
                ->where('name->bg', 'like', "%{$search}%")
                ->orWhere('name->en', 'like', "%{$search}%"))
            ->limit(50)
            ->get()
            ->mapWithKeys(fn (ProductsIndexer $product): array => [$product->product_id => (string) $product->getTranslatedName()])
            ->all();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getDefaultPages(): array
    {
        return [
            'index' => ListCrossSells::route('/'),
        ];
    }
}
