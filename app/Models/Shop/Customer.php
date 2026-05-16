<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    // FIXED: Changed from 'shop_customers' to 'customers' to match your Railway database table name
    protected $table = 'customers'; 

    protected $fillable = [
        'name',
        'phone',
        'email', 
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function ($customer) {
            // Automatically supply a valid fallback email string to satisfy database constraints
            if (empty($customer->email)) {
                $customer->email = 'client_' . time() . '_' . uniqid() . '@example.com';
            }
        });
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'shop_customer_id');
    }
}
