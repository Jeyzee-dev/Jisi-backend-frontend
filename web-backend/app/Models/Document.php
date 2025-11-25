<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'appointment_id',
        'filename',
        'original_name',
        'mime_type',
        'file_size',
        'file_path',
        'document_type',
        'description',
        'status',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function versions()
    {
        return $this->hasMany(DocumentVersion::class);
    }

    public function getDisplayNameAttribute()
    {
        return pathinfo($this->original_name, PATHINFO_FILENAME);
    }

    public function getExtensionAttribute()
    {
        return pathinfo($this->original_name, PATHINFO_EXTENSION);
    }
}
