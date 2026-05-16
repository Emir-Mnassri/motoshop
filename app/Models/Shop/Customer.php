<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'shop_customers'; // Or 'customers' depending on your template migration

    protected $fillable = [
        'name',
        'phone',
        'email', // Keep this in fillable so Laravel can insert our placeholder
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function ($customer) {
            // If no email is provided by our clean form, automatically supply a valid fake fallback
            if (empty($customer->email)) {
                $customer->email = 'client_' . time() . '_' . uniqid() . '@example.com';
            }
        });
    }

    // Keep any existing relationship methods below (like orders, addresses, etc.)
    public function orders()
    {
        return $this->hasMany(Order::class, 'shop_customer_id');
    }
}
