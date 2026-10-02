<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Manifesto extends Model
{
    protected $guarded = [];

    protected $casts = [
        'points' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
