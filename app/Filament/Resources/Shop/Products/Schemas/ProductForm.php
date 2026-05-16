<?php

namespace App\Filament\Resources\Shop\Products\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 1. Product Name
                TextInput::make('name')
                    ->label('Nom du produit')
                    ->required()
                    ->maxLength(255),

                // 2. Quantity (Stock)
                TextInput::make('qty')
                    ->label('qte')
                    ->numeric()
                    ->default(0)
                    ->required(),

                // 3. Buying Price (Cost)
                TextInput::make('cost')
                    ->label('prix achat')
                    ->numeric()
                    ->prefix('TND')
                    ->required(),

                // 4. Selling Price
                TextInput::make('price')
                    ->label('prix vente')
                    ->numeric()
                    ->prefix('TND')
                    ->required(),
            ]);
    }
}
