<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnavailableDate extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'reason',
        'all_day',
        'start_time',
        'end_time'
        // No 'created_by' here
    ];

    protected $casts = [
        'date' => 'date',
        'all_day' => 'boolean',
    ];
}