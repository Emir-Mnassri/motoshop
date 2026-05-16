<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_items';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'qty',
        'unit_price', // Added to allow programmatic saving
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function ($item) {
            // Automatically capture the current live price from the related product record
            if ($item->product && empty($item->unit_price)) {
                $item->unit_price = $item->product->price;
            }

            // Fallback default placeholder price to prevent database crash if price is missing
            if (empty($item->unit_price)) {
                $item->unit_price = 0;
            }
        });

        static::created(function ($item) {
            // Automatically update the parent order's total_price after an item is added
            $item->updateParentOrderTotal();
        });
    }

    /**
     * Recalculate and update the total price on the parent order
     */
    public function updateParentOrderTotal(): void
    {
        if ($this->order) {
            $total = $this->order->orderItems()->sum(\DB::raw('qty * unit_price'));
            $this->order->update(['total_price' => $total]);
        }
    }

    /** @return BelongsTo<Order, $this> */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
