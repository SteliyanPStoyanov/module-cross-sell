<?php

namespace Modules\CrossSell\Models;

use App\Models\ProductsIndexer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lunar\Base\BaseModel;
use Lunar\Models\Product;

/**
 * CrossSell model for managing recommended products.
 *
 * This model stores the relationship between cross-sell entries
 * and their associated products, along with display position.
 *
 * @property int $id
 * @property int $product_id
 * @property int $position
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read ProductsIndexer|null $product
 * @property-read Product|null $productModel
 *
 * @method static Builder|CrossSell query()
 * @method static Builder|CrossSell orderBy(string $column, string $direction = 'asc')
 * @method static Builder|CrossSell where(string $column, mixed $operator = null, mixed $value = null)
 * @method static CrossSell|null first()
 * @method static CrossSell create(array $attributes = [])
 * @method static CrossSell updateOrCreate(array $attributes, array $values = [])
 */
class CrossSell extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'position',
    ];

    /**
     * Get the associated product from the indexer.
     *
     * @return BelongsTo<ProductsIndexer, CrossSell>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductsIndexer::class, 'product_id', 'product_id');
    }

    /**
     * Get the associated Lunar product model.
     *
     * @return BelongsTo<Product, CrossSell>
     */
    public function productModel(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    protected static function newFactory(): \Modules\CrossSell\Database\Factories\CrossSellFactory
    {
        return \Modules\CrossSell\Database\Factories\CrossSellFactory::new();
    }
}
