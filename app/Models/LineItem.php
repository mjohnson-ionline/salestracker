<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LineItem extends Model
{
    protected $table = 'line_items';
    protected $guarded = ['id'];

    use CrudTrait;

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
