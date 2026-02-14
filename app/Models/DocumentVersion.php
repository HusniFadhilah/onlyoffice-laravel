<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentVersion extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'document_id',
        'version',
        'file_path',
        'created_by',
        'changes_summary'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    public function document()
    {
        return $this->belongsTo(OnlyOfficeDocument::class, 'document_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
