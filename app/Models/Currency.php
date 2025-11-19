<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $table = 'currencies';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'symbol',
        'code',
        'symbolAtRight',
        'name',
        'decimal_degits',
        'isActive',
    ];

    protected $casts = [
        'symbolAtRight' => 'boolean',
        'isActive' => 'boolean',
        'decimal_degits' => 'integer',
    ];
}

