<?php

namespace App\Filament\Resources\Shop\Orders\Schemas;

use Filament\Forms\Form; // Standard Filament form blueprint class
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;

class OrderForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([ // Uses ->schema() wrapper to load components array cleanly
                // 1. Select or Create Customer (Client)
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
                        // MATCHED: Correct database column pointer for line items
                        Select::make('product_id')
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
