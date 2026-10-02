<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignSetting extends Model
{
    protected $guarded = [];

    protected $casts = [
        'stats' => 'array',
        'bio_data' => 'array',
        'election_date' => 'datetime',
    ];
}
