<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed|string $name
 */
class InvoiceType extends Model
{
    protected $fillable = [
        'name',
        'sort',
    ];

    public $timestamps = false;
}
