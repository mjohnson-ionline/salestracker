<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'xero_code',
	    'pipedrive_id',
        'percentage',
        'reseller_id',
        'created_at',
        'update_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'percentage' => 'integer',
        'created_at' => 'timestamp',
        'update_at' => 'timestamp',
    ];


    public function reseller()
    {
        return $this->belongsTo(\App\Models\Reseller::class, 'reseller_id');
    }
}


