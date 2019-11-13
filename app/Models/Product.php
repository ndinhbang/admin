<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    // ======================= Hidden Attributes ================= //
    protected $hidden = [
        'id',
        'place_id',
        'category_id',
        'created_at',
    ];

    protected $guarded = ['id'];

    // ======================= Attribute Casting ================= //
    protected $casts = [
        'uuid'        => 'string',
        'place_id'    => 'integer',
        'category_id' => 'integer',
        'position'    => 'integer',
        'state'       => 'boolean',
        'is_hot'      => 'boolean',
        'price'       => 'double',
        'opened'      => 'boolean',
        'can_stock'   => 'boolean',
        'thumbnail'   => 'string',
    ];

    // ======================= Overrided ================= //

    /**
     * {@inheritDoc}
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // ======================= Mutators ================= //
    public function setThumbnailAttribute($value)
    {
        $this->attributes['thumbnail'] = '/' . trim($value, '/');
    }

    // ======================= Local Scopes ================= //
    public function scopeActive($query)
    {
        return $query->where('state', 1);
    }

    // ======================= Relationships ================= //
    public function place()
    {
        return $this->belongsTo('App\Models\Place', 'place_id');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id');
    }

    public function supplies()
    {
        return $this->belongsToMany('App\Models\Supply', 'product_supply', 'product_id', 'supply_id')
            ->withPivot('quantity');
    }
}
