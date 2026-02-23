<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditItem extends Model
{
    protected $fillable = [
        'audit_id',
        'title',
        'description',
        'status',
        'score',
        'notes',
    ];

    public function audit()
    {
        return $this->belongsTo(Audit::class);
    }
}
