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
                // FIXED: Changed field key from 'shop_customer_id' to 'customer_id' to align with your production database schema
                Select::make('customer_id') 
                    ->relationship('customer', 'name')
                    ->label('Client')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nom du Client')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->nullable(), 

                // 2. Order Items Repeater (Produits commandés)
                Repeater::make('orderItems')
                    ->relationship('orderItems') 
                    ->label('Produits de la commande')
                    ->schema([
                        Select::make('shop_product_id')
                            ->relationship('product', 'name')
                            ->label('Produit')
                            ->searchable()
                            ->preload()
                            ->required(),

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
