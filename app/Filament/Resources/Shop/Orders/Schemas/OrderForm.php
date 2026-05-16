<?php

namespace App\Filament\Resources\Shop\Orders\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 1. Select or Create Customer (Client)
                Select::make('shop_customer_id') // Keeps database column intact
                    ->relationship('customer', 'name')
                    ->label('Client')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nom du Client')
                            ->required(),
                        TextInput::make('email')
                            ->email()
                            ->required(),
                    ])
                    ->nullable(), // Safe for Guest or Registered options

                // 2. Order Items Repeater (Produits commandés)
                Repeater::make('items')
                    ->relationship('items') // Keeps standard Eloquent relationship
                    ->label('Produits de la commande')
                    ->schema([
                        // Select Product
                        Select::make('shop_product_id')
                            ->relationship('product', 'name')
                            ->label('Produit')
                            ->searchable()
                            ->preload()
                            ->required(),

                        // Set Quantity
                        TextInput::make('qty')
                            ->label('Quantité')
                            ->numeric()
                            ->default(1)
                            ->required(),
                    ])
                    ->columns(2)
                    ->createItemButtonLabel('Ajouter un produit'),
            ]);
    }
}
