<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    protected $table = 'payments';

    protected $fillable = [
        'xero_contact_id',
        'transaction_id',
        'amount',
        'client_email',
        'log_id',
    ];

    protected $casts = [
        // 'details' => 'array',
    ];

    public function log()
    {
        return $this->belongsTo(Log::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
