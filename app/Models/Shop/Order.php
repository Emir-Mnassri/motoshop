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
            // 1. Generate unique order tracking code reference
            if (empty($order->number)) {
                $order->number = 'OR-' . strtoupper(uniqid());
            }

            // 2. FIXED: Injected fallback via lower-level attribute array to bypass strict Enum checks
            if (empty($order->currency)) {
                $order->attributes['currency'] = 'usd'; // Uses the raw underlying table string format directly
            }

            // 3. FIXED: Handle status fallback matching standard lowercase structures safely
            if (empty($order->status)) {
                $order->attributes['status'] = 'new'; 
            }

            // 4. Ensure total price defaults cleanly to avoid decimal errors
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
