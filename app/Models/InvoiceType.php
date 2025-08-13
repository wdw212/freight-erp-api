<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed|string $name
 * @property mixed $id
 */
class InvoiceType extends Model
{
    protected $fillable = [
        'name',
        'sort',
    ];

    public $timestamps = false;
}
