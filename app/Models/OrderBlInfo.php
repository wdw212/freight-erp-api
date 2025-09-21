<?php

namespace App\Models;

use App\Observers\OrderBlInfoObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(OrderBlInfoObserver::class)]
class OrderBlInfo extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    protected $casts = [
        'sender' => 'json',
        'receiver' => 'json',
        'notifier' => 'json',
    ];
}
