<?php

namespace App\Filament\Resources\Shop\Products;

use App\Filament\Resources\Shop\Products\Pages\CreateProduct;
use App\Filament\Resources\Shop\Products\Pages\EditProduct;
use App\Filament\Resources\Shop\Products\Pages\ListProducts;
use App\Filament\Resources\Shop\Products\Schemas\ProductForm;
use App\Filament\Resources\Shop\Products\Tables\ProductsTable;
use App\Models\Shop\Product;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedBolt;

    // Changes the navigation group text to French for your client
    protected static string | UnitEnum | null $navigationGroup = 'Boutique';

    protected static ?int $navigationSort = 0;

    protected static ?string $slug = 'shop/products';

    public static function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
    }

    public static function getRelations(): array
    {
        // SAFELY REMOVED: CommentsRelationManager to prevent errors
        return [];
    }

    public static function getWidgets(): array
    {
        // SAFELY REMOVED: ProductStats overview widgets to speed up loading and prevent 502s
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        // SAFELY REMOVED: 'sku' and 'brand.name' to prevent relationship lookup errors
        return ['name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        // SAFELY REMOVED: Brand details lookups
        return [];
    }

    /** @return Builder<Product> */
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        // SAFELY REMOVED: ->with(['brand']) eager loading bottleneck
        return parent::getGlobalSearchEloquentQuery();
    }

    public static function getNavigationBadge(): ?string
    {
        // SAFELY REMOVED: The low stock security badge lookup to keep things fast and error-free
        return null;
    }
}
