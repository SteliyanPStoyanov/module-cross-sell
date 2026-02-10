<div>
    <div class="flex items-center justify-between">
        <div class="w-full">
            <strong class="text-lg font-bold md:text-2xl">
                {{ __('cross-sell::global.menu.recommended_products') }}
            </strong>
        </div>
        <div class="ml-4 w-80">
            <div class="flex justify-end w-full space-x-4">
               @livewire('cross-sell.components.product-search', ['existing' => $crossSell ])
            </div>
        </div>
    </div>
    <div class="mt-4 space-y-2">
        @livewire('cross-sell.components.tree')
    </div>
</div>
