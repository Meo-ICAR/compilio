<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingRecord extends Model
{
    protected $fillable = [
        'training_session_id',
        'user_id',
        'name',
        'email',
        'status',
        'completion_date',
        'notes',
    ];

    public function trainingSession()
    {
        return $this->belongsTo(TrainingSession::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
