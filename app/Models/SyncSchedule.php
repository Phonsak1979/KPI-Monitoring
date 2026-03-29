<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyncSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'sync_time',
        'is_active',
        'last_run_at',
        'last_run_result',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_run_at' => 'datetime',
        'last_run_result' => 'array',
    ];
}
