<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'details',
        'log_id',
    ];

    protected $casts = [
        // 'details' => 'array',
    ];

    public function log()
    {
        return $this->belongsTo(Log::class);
    }
}
