<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceType extends Model
{
    protected $fillable = [
        'name',
        'sort',
    ];

    public $timestamps = false;
}
