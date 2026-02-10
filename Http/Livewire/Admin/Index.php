<?php

namespace Modules\CrossSell\Http\Livewire\Admin;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Modules\CrossSell\Models\CrossSell;

/**
 * Admin index component for managing cross-sell products.
 *
 * This is the main admin page for the cross-sell module,
 * displaying the list of recommended products with controls
 * for adding, reordering, and removing items.
 */
class Index extends Component
{
    /**
     * Collection of cross-sell products.
     *
     * @var Collection<int, CrossSell>
     */
    public $crossSell;

    /**
     * Initialize the component on each request.
     */
    public function boot(): void
    {
        $this->crossSell = CrossSell::get();
    }

    /**
     * Render the livewire component.
     */
    public function render(): View
    {
        return view('cross-sell::livewire.admin.index')
            ->layout('adminhub::layouts.app', [
                'title' => __('cross-sell::catalogue.index.title'),
            ]);
    }
}
