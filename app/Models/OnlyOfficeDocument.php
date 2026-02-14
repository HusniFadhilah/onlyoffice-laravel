<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OnlyOfficeDocument extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'filename',
        'file_path',
        'file_type',
        'file_size',
        'key',
        'metadata',
        'status',
        'last_modified'
    ];

    protected $casts = [
        'metadata' => 'array',
        'last_modified' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($document) {
            if (!$document->key) {
                $document->key = Str::random(32);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function versions()
    {
        return $this->hasMany(DocumentVersion::class, 'document_id');
    }

    public function shares()
    {
        return $this->hasMany(DocumentShare::class, 'document_id');
    }

    public function getFullPathAttribute()
    {
        return storage_path('app/' . $this->file_path);
    }

    public function getDownloadUrlAttribute()
    {
        return route('onlyoffice.download', $this->id);
    }
}
