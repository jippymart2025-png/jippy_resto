<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantOrder extends Model
{
    protected $table = 'restaurant_orders';

    public $timestamps = false;

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'vendorID',
        'discount',
        'deliveryCharge',
        'adminCommission',
        'adminCommissionType',
        'createdAt',
        'status',
        'products',
        'author',
        'address',
        'taxSetting',
        'specialDiscount',
        'takeAway',
        'ToPay',
        'toPayAmount',
    ];
}

