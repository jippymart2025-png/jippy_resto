<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $table = 'vendors';

    public $timestamps = false;

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'title',
        'author',
        'zoneId',
        'isOpen',
        'walletAmount',
        'adminCommission',
        'adminCommissionType',
        'subscriptionPlanId',
    ];
}

