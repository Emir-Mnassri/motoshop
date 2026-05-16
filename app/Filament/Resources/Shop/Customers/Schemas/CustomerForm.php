<?php

namespace App\Filament\Resources\Shop\Customers\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 1. Nom du Client
                TextInput::make('name')
                    ->label('Nom du Client')
                    ->required()
                    ->maxLength(255),

                // 2. Numéro de Téléphone (Optional)
                TextInput::make('phone') // Ensure this matches your database column name ('phone' or 'phone_number')
                    ->label('Numéro de Téléphone')
                    ->maxLength(255),
            ]);
    }
}
