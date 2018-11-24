<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    /** @var string Table */
    protected $table = 'orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'order_number',
        'payment_method',
        'transaction_id',
        'cart_total',
        'discount_total',
        'status',
    ];

    /**
     * Order belongs to user.
     *
     * @return BelongsTo
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Order has many order items.
     *
     * @return HasMany
     */
    public function items() : HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
