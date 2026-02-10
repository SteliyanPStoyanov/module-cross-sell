<?php

namespace Modules\CrossSell\Http\Livewire\Components;

use Illuminate\View\View;
use Livewire\Component;
use Lunar\Facades\DB;
use Lunar\Hub\Http\Livewire\Traits\Notifies;
use Modules\CrossSell\Models\CrossSell;

/**
 * Tree component for displaying and managing cross-sell products.
 *
 * This component renders a sortable list of cross-sell products,
 * allowing administrators to drag-and-drop reorder items and
 * remove products from the list.
 */
class Tree extends Component
{
    use Notifies;

    /**
     * Array of node data for the tree display.
     *
     * @var array<int, array{id: int, name: string, thumbnail: string|null}>
     */
    public $nodes;

    /**
     * Get the event listeners for the component.
     *
     * @return array<string, string>
     */
    protected function getListeners(): array
    {
        return [
            'collectionSearch.selected' => 'reloadList',
        ];
    }

    /**
     * Initialize the component.
     */
    public function mount(): void
    {
        $this->reloadList();
    }

    /**
     * Reload the list of cross-sell products.
     *
     * Fetches all cross-sell products ordered by position
     * and transforms them into the node format for display.
     */
    public function reloadList(): void
    {
        $this->nodes = [];

        foreach (CrossSell::orderBy('position')->get() as $crossSell) {
            $this->nodes[] = [
                'id' => $crossSell->product->product_id,
                'name' => $crossSell->product->getTranslatedName(),
                'thumbnail' => $crossSell->product->thumbnail,
            ];
        }
    }

    /**
     * Handle sort event from the drag-and-drop interface.
     *
     * Updates the position of each product based on the new order.
     *
     * @param  array{items: array<int, array{id: int, order: int}>}  $payload
     */
    public function sort(array $payload): void
    {
        foreach ($payload['items'] as $item) {
            $crossSell = CrossSell::where('product_id', $item['id'])->first();
            $crossSell?->update(['position' => $item['order']]);
        }

        $this->reloadList();
    }

    /**
     * Remove a product from the cross-sell list.
     *
     * @param  int|string  $id  The product ID to remove
     */
    public function removeProduct(int|string $id): void
    {
        $index = array_search($id, array_column($this->nodes, 'id'));

        if ($index !== false) {
            unset($this->nodes[$index]);
            $this->nodes = array_values($this->nodes);
        }

        DB::transaction(function () use ($id) {
            $crossSell = CrossSell::where('product_id', $id)->first();
            $crossSell?->delete();

            $this->syncPosition();
        });

        $this->notify(__('cross-sell::notifications.product_removed'));
    }

    /**
     * Synchronize positions after a product is removed.
     *
     * Re-numbers all positions sequentially starting from 1.
     */
    public function syncPosition(): void
    {
        foreach (CrossSell::orderBy('position')->get() as $key => $crossSell) {
            $crossSell->update(['position' => $key + 1]);
        }
    }

    /**
     * Render the livewire component.
     */
    public function render(): View
    {
        return view('cross-sell::livewire.components.tree')
            ->layout('adminhub::layouts.app', [
                'title' => __('cross-sell::catalogue.index.title'),
            ]);
    }
}
