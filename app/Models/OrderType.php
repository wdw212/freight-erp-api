<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed|string $name
 */
class OrderType extends Model
{
    public $timestamps = false;

    protected $guarded = [];
    
    protected $casts = [
        'role_ids' => 'json'
    ];
}
