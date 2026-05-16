<?php

namespace App\Filament\Resources\Shop\Orders\Pages;

use App\Filament\Resources\Shop\Orders\OrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    // Ripped out the broken getSteps() function entirely.
    // Filament will now naturally pull the beautiful 2-column form 
    // we built in OrderForm.php without throwing relationship or method errors.
}
