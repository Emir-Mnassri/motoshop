<?php

namespace App\Models\Shop;

use App\Enums\CurrencyCode;
use App\Enums\OrderStatus;
use Database\Factories\Shop\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'orders';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'customer_id', 
        'number',
        'total_price',
        'status',
        'currency',
        'shipping_price',
        'shipping_method',
        'notes',
    ];

    protected $casts = [
        'currency' => CurrencyCode::class,
        'status' => OrderStatus::class,
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function ($order) {
            // 1. Automatically generate a unique order tracking reference
            if (empty($order->number)) {
                $order->number = 'OR-' . strtoupper(uniqid());
            }

            // 2. FIXED: Fallback currency enum (Uses the CurrencyCode backend enum class)
            // Change CurrencyCode::USD to matches your target primary setup (e.g., EUR, TND etc.)
            if (empty($order->currency)) {
                $order->currency = CurrencyCode::USD; 
            }

            // 3. FIXED: Safe fallback execution status 
            if (empty($order->status)) {
                $order->status = OrderStatus::New; // Or 'pending' depending on your OrderStatus Enum options
            }

            // 4. FIXED: Prevent strict decimal mismatch crash if empty
            if (empty($order->total_price)) {
                $order->total_price = 0;
            }
        });
    }

    /** @return MorphOne<OrderAddress, $this> */
    public function address(): MorphOne
    {
        return $this->morphOne(OrderAddress::class, 'addressable');
    }

    /** @return BelongsTo<Customer, $this> */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /** @return HasMany<OrderItem, $this> */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    /** @return HasMany<Payment, $this> */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
