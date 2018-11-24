<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    /** @var string Table */
    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'quantity',
        'price',
        'discount',
    ];

    /**
     * Product belongs to many bundle.
     *
     * @return BelongsToMany
     */
    public function bundles() : BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
