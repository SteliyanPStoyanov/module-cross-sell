# CrossSell Module

Admin interface for managing recommended/cross-sell products displayed to customers. Provides drag-and-drop reordering, product search with duplicate prevention, and position synchronization.

## Features

- Curated recommended products list with thumbnails
- Drag-and-drop reordering with automatic position sync
- Product search modal with duplicate prevention
- Multi-select before committing additions
- Paginated search results (50 per page)
- Bilingual admin interface (bg/en)

## Installation

The module is auto-discovered. No manual registration needed.

## Admin Routes

All routes are prefixed with `/hub/cross-sell` and require the `manage-cross-sell` permission.

| Route | Name | Description |
|-------|------|-------------|
| `GET /` | `hub.cross-sell.index` | Recommended products list with drag-and-drop |

## Database Schema

### lunar_cross_sells

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `product_id` | foreignId | FK to lunar products (nullable) |
| `position` | string | Display order position (nullable) |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

## Model Relationships

```php
CrossSell -> product()       // BelongsTo(ProductsIndexer) - indexed product with translations
CrossSell -> productModel()  // BelongsTo(Product) - raw Lunar product
```

## How It Works

1. Admin visits `/hub/cross-sell` and sees the current recommended products
2. **Add products**: Click "Add Product" -> search modal -> select products -> confirm
3. **Reorder**: Drag-and-drop products to change display order
4. **Remove**: Click dropdown menu on a product -> "Remove"
5. Positions are automatically re-numbered (1, 2, 3...) after removals

## File Structure

```
modules/CrossSell/
├── CrossSellServiceProvider.php       # Routes, views, migrations, menu, permissions
├── Models/
│   └── CrossSell.php                  # Eloquent model with product relationships
├── Http/Livewire/
│   ├── Admin/
│   │   └── Index.php                  # Main admin page
│   └── Components/
│       ├── Tree.php                   # Sortable product list (drag-and-drop)
│       └── ProductSearch.php          # Search modal with multi-select
├── database/
│   └── migrations/
│       └── 2025_01_12_create_cross_sells_table.php
├── resources/
│   ├── lang/{bg,en}/                  # Translations (global, catalogue, components, notifications)
│   └── views/livewire/               # Blade templates
├── routes/
│   └── web.php                        # Admin routes
└── Tests/Feature/
    ├── CrossSellModelTest.php         # Model CRUD, ordering, updateOrCreate
    ├── TreeComponentTest.php          # Sort, remove, position sync, event listeners
    └── CrossSellServiceProviderTest.php  # Translations, views, routes, components
```

## Testing

```bash
php artisan test --env=testing --filter=CrossSell
```

Tests cover (21 tests):
- Model CRUD, fillable attributes, ordering, updateOrCreate
- Tree component: initialization, sort, remove, position sync
- Service provider: translations (bg/en), views, routes, Livewire components, migrations
