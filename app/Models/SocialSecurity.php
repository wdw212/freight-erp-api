<?php

namespace App\Models;

use App\Observers\SocialSecurityObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $total_social_security
 * @property mixed $id
 */
#[ObservedBy(SocialSecurityObserver::class)]
class SocialSecurity extends Model
{
    protected $guarded = [];
}
