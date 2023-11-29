<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = 'logs';

    protected $fillable = [
        'type',
        'platform',
        'action',
        'payload',
	    'is_parsed',
        'pipedrive_duplicate_parsed',
	    'webhook_id'
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
