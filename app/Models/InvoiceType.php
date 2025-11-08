<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed|string $name
 * @property mixed $id
 */
class InvoiceType extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'sort',
        'tax_rate',
        'remark',
        'type'
    ];

    public $timestamps = false;
}
