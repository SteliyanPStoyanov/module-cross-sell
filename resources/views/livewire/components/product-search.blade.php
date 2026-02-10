<div>
    <x-hub::button type="button"
                   wire:click.prevent="$set('showBrowser', true)">
        {{ __('cross-sell::components.product-search.btn') }}
    </x-hub::button>
    <x-hub::slideover :title="__('cross-sell::components.product-search.title')"
                      wire:model="showBrowser">
        <div class="space-y-4"
             x-data="{ tab: 'search' }">
            <div>
                <nav class="flex space-x-4"
                     aria-label="Tabs">
                    <button x-on:click.prevent="tab = 'search'"
                            class="px-3 py-2 text-sm font-medium rounded-md"
                            :class="{
                                'bg-sky-100 text-sky-700': tab == 'search',
                                'text-gray-500 hover:text-gray-700': tab != 'search'
                            }">
                        {{ __('cross-sell::components.product-search.first_tab') }}
                    </button>
                    <button class="px-3 py-2 text-sm font-medium rounded-md"
                            @click.prevent="tab = 'selected'"
                            :class="{
                                'bg-sky-100 text-sky-700': tab == 'selected',
                                'text-gray-500 hover:text-gray-700': tab != 'selected'
                            }">
                        {{ __('cross-sell::components.product-search.second_tab') }}

                        ({{ $this->selectedModels->count() }})
                    </button>
                </nav>
            </div>
            <div x-show="tab == 'search'">
                <x-hub::input.text wire:model.debounce.300ms="searchTerm"/>
                @if ($this->searchTerm)
                    @if ($this->results->total() > $maxResults)
                        <span class="block p-3 my-2 text-xs text-sky-600 rounded bg-sky-50">
                            {{ __('adminhub::components.collection-search.max_results_exceeded', [
                                'max' => $maxResults,
                                'total' => $this->results->total(),
                            ]) }}
                        </span>
                    @endif
                    <div class="mt-4 space-y-1">
                        @forelse($this->results as $product)
                            <div @class(['flex w-full items-center justify-between rounded shadow-sm text-left border px-2 py-2 text-sm',])>
                                <div class="truncate max-w-64">
                                    {{ $product->getTranslatedName() }}
                                </div>
                                @if (!$this->existingIds->contains($product->product_id))
                                    @if (collect($this->selected)->contains($product->product_id))
                                        <button class="px-2 py-1 text-xs text-red-700 border border-red-200 rounded shadow-sm hover:bg-red-50"
                                                wire:click.prevent="removeCollection('{{ $product->product_id }}')">
                                            {{ __('adminhub::global.deselect') }}
                                        </button>
                                    @else
                                        <button class="px-2 py-1 text-xs text-sky-700 border border-sky-200 rounded shadow-sm hover:bg-sky-50"
                                                wire:click.prevent="selectCollection('{{ $product->product_id }}')">
                                            {{ __('adminhub::global.select') }}
                                        </button>
                                    @endif
                                @else
                                    <span class="text-xs">
                                        {{ __('adminhub::components.collection-search.exists_in_collection') }}
                                    </span>
                                @endif
                            </div>
                        @empty
                            {{ __('adminhub::components.collection-search.no_results') }}
                        @endforelse
                    </div>
                @else
                    <div class="px-3 py-2 mt-4 text-sm text-gray-500 bg-gray-100 rounded">
                        {{ __('cross-sell::components.product-search.pre_search_message') }}
                    </div>
                @endif
            </div>
            <div x-show="tab == 'selected'"
                 class="space-y-2">
                @forelse($this->selectedModels as $product)
                    <div class="flex items-center justify-between w-full px-2 py-2 text-sm text-left border rounded shadow-sm "
                         wire:key="selected_{{ $product->product_id }}">
                        <div class="truncate max-w-64">
                            {{ $product->getTranslatedName() }}
                        </div>
                        <button class="px-2 py-1 text-xs text-red-700 border border-red-200 rounded shadow-sm hover:bg-red-50"
                                wire:click.prevent="removeCollection('{{ $product->product_id }}')">
                            {{ __('adminhub::global.deselect') }}
                        </button>
                    </div>
                @empty
                    <div class="px-3 py-2 mt-4 text-sm text-gray-500 bg-gray-100 rounded">
                        {{ __('cross-sell::components.product-search.select_empty') }}
                    </div>
                @endforelse
            </div>
        </div>
        <x-slot name="footer">
            <x-hub::button wire:click.prevent="triggerSelect">
                {{ __('cross-sell::components.product-search.commit_btn') }}
            </x-hub::button>
        </x-slot>
    </x-hub::slideover>
</div>
