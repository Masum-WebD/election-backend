<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grievance extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_public' => 'boolean',
    ];
}
