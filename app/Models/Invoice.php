<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use CrudTrait;

    protected $table = 'invoices';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }

    public function reseller()
    {
        return $this->belongsTo(Reseller::class);
    }

    public function lineItems()
    {
        return $this->hasMany(LineItem::class, 'invoice_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'invoice_id');
    }

}
