<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorProduct extends Model
{
    protected $table = 'vendor_products';

    public $timestamps = false;

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'vendorID',
        'name',
        'price',
        'disPrice',
        'publish',
    ];
}

