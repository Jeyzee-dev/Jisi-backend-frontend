<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_date',
        'type',
        'reason',
        'start_time',
        'end_time',
        'is_recurring',
        'recurring_days'
    ];

    protected $casts = [
        'event_date' => 'date',
        'recurring_days' => 'array',
        'is_recurring' => 'boolean'
    ];
}