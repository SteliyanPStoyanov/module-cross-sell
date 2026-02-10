<?php

namespace Modules\CrossSell\Http\Livewire\Components;

use App\Models\ProductsIndexer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Lunar\Facades\DB;
use Lunar\Hub\Http\Livewire\Traits\Notifies;
use Modules\CrossSell\Models\CrossSell;

/**
 * Product search component for adding products to cross-sell list.
 *
 * This component provides a searchable interface for finding and
 * selecting products to add to the cross-sell recommendations.
 * Selected products are saved to the database with positioning.
 */
class ProductSearch extends Component
{
    use Notifies;

    /**
     * Whether the search browser modal is visible.
     */
    public bool $showBrowser = false;

    /**
     * The current search term.
     */
    public ?string $searchTerm = null;

    /**
     * Maximum number of results to display per page.
     */
    public int $maxResults = 50;

    /**
     * Collection of existing cross-sell products to exclude.
     */
    public Collection $existing;

    /**
     * Collection of current cross-sell products.
     *
     * @var Collection<int, array{id: int, position: int}>
     */
    public $crossSell;

    /**
     * Array of currently selected product IDs.
     *
     * @var array<int, int|string>
     */
    public array $selected = [];

    /**
     * Get the validation rules.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'searchTerm' => 'required|string|max:255',
        ];
    }

    /**
     * Reset state when the browser visibility changes.
     */
    public function updatedShowBrowser(): void
    {
        $this->selected = [];
        $this->searchTerm = null;
    }

    /**
     * Get the selected products as models.
     *
     * @return Collection<int, ProductsIndexer>
     */
    public function getSelectedModelsProperty(): Collection
    {
        return ProductsIndexer::whereIn('product_id', $this->selected)->get();
    }

    /**
     * Get the IDs of existing cross-sell products.
     *
     * @return Collection<int, int>
     */
    public function getExistingIdsProperty(): Collection
    {
        return $this->existing->pluck('product_id');
    }

    /**
     * Add a product to the selected list.
     *
     * @param  int|string  $id  The product ID to select
     */
    public function selectCollection(int|string $id): void
    {
        $this->selected[] = $id;
    }

    /**
     * Remove a product from the selected list.
     *
     * @param  int|string  $id  The product ID to deselect
     */
    public function removeCollection(int|string $id): void
    {
        $index = collect($this->selected)->search($id);

        if ($index !== false) {
            unset($this->selected[$index]);
        }
    }

    /**
     * Trigger the selection and save to database.
     *
     * Saves selected products to cross-sell, emits event,
     * and closes the browser modal.
     */
    public function triggerSelect(): void
    {
        $this->saveSelectedProducts($this->selected);
        $this->emit('collectionSearch.selected', $this->selected);

        $this->notify(__('cross-sell::notifications.product_added'));

        $this->showBrowser = false;
    }

    /**
     * Save selected products to the cross-sell database.
     *
     * @param  array<int, int|string>  $productIds
     */
    public function saveSelectedProducts(array $productIds): void
    {
        $selectedProducts = ProductsIndexer::whereIn('product_id', $productIds)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->product_id,
                    'position' => $this->crossSell->count() + 1,
                ];
            });

        $this->crossSell = $this->crossSell->count()
            ? $this->crossSell->merge($selectedProducts)
            : $selectedProducts;

        DB::transaction(function () {
            foreach ($this->crossSell->toArray() as $item) {
                CrossSell::updateOrCreate(
                    ['product_id' => $item['id']],
                    ['position' => $item['position']]
                );
            }
        });
    }

    /**
     * Sync the cross-sell collection from database.
     */
    protected function syncCrossSell(): void
    {
        $this->crossSell = CrossSell::get()
            ->map(function ($product) {
                return [
                    'id' => $product->product_id,
                    'position' => $product->position,
                ];
            });
    }

    /**
     * Get the search results.
     *
     * Returns paginated products matching the search term.
     */
    public function getResultsProperty(): ?LengthAwarePaginator
    {
        if (!$this->searchTerm) {
            return null;
        }

        return ProductsIndexer::where('name', 'like', "%{$this->searchTerm}%")
            ->paginate($this->maxResults);
    }

    /**
     * Render the livewire component.
     */
    public function render(): View
    {
        $this->syncCrossSell();

        return view('cross-sell::livewire.components.product-search')
            ->layout('adminhub::layouts.base');
    }
}
