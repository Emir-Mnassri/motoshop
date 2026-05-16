<?php

namespace App\Filament\Resources\Shop\Orders\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // 1. Customer Name
                TextColumn::make('customer.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->default('Client Invité'),

                // 2. Products Sold Summary
                TextColumn::make('items.product.name')
                    ->label('Produit(s)')
                    ->bulleted(), // Displays products cleanly as a neat list inside the row

                // 3. Total Quantity Sold
                TextColumn::make('items.qty')
                    ->label('Quantité Total')
                    ->sum('items', 'qty'), // Efficiently sums up quantities mathematically without crashing

                // 4. Total Order Price
                TextColumn::make('total_price')
                    ->label('Prix Total')
                    ->money('TND')
                    ->sortable(),

                // 5. Order Date
                TextColumn::make('created_at')
                    ->label('Date')
                    ->date('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                // PRESERVED: The excellent calendar date filter your client loves
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->label('Du'),
                        DatePicker::make('created_until')->label('Au'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ]);
    }
}
