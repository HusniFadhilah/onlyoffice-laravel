<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentShare extends Model
{
    protected $fillable = [
        'document_id',
        'user_id',
        'permission'
    ];

    public function document()
    {
        return $this->belongsTo(OnlyOfficeDocument::class, 'document_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function canEdit()
    {
        return $this->permission === 'edit';
    }

    public function canComment()
    {
        return in_array($this->permission, ['edit', 'comment']);
    }
}
