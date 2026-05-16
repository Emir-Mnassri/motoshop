<?php

namespace App\Filament\Resources\Shop\Customers\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // 1. Client Name Header
                TextColumn::make('name')
                    ->label('Nom du Client')
                    ->searchable()
                    ->sortable(),

                // 2. Optional Phone Number Header
                TextColumn::make('phone') // Adjust to 'phone_number' or 'mobile' depending on your model column
                    ->label('Numéro de Téléphone')
                    ->searchable()
                    ->default('-'), // Displays a neat placeholder if left empty
            ]);
    }
}
