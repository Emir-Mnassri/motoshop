<?php

namespace App\Filament\Resources\Shop\Products\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // 1. Nom du Produit
                TextColumn::make('name')
                    ->label('Nom du Produit')
                    ->searchable()
                    ->sortable(),

                // 2. Qte (Matching your exact abbreviation)
                TextColumn::make('qty')
                    ->label('Qte')
                    ->sortable(),

                // 3. Prix Achat
                TextColumn::make('cost')
                    ->label('Prix Achat')
                    ->money('TND')
                    ->sortable(),

                // 4. Prix Vente
                TextColumn::make('price')
                    ->label('Prix Vente')
                    ->money('TND')
                    ->sortable(),
            ]);
    }
}
