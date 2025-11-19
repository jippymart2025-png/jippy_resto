<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'document_name',
        'fields',
    ];

    protected $casts = [
        'fields' => 'array',
    ];

    public static function getFields(string $documentName): array
    {
        $setting = self::where('document_name', $documentName)->first();

        if (!$setting) {
            return [];
        }

        if (is_array($setting->fields)) {
            return $setting->fields;
        }

        $decoded = json_decode($setting->fields, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
    }
}

