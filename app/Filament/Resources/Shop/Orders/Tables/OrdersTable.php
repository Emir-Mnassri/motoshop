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

                // 2. Products Sold Summary (Fixed relationship string)
                TextColumn::make('orderItems.product.name')
                    ->label('Produit(s)')
                    ->bulleted(), 

                // 3. Total Quantity Sold (Fixed relationship string)
                TextColumn::make('orderItems.qty')
                    ->label('Quantité Total')
                    ->sum('orderItems', 'qty'), 

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
