<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'filename',
        'file_path',
        'version_number',
        'change_type',
        'change_description',
        'change_metadata'
    ];

    protected $casts = [
        'change_metadata' => 'array'
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
