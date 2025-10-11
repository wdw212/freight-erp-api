<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeType extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'type',
        'remark',
        'sort',
    ];

    public $timestamps = false;
}
