<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Employee extends Authenticatable implements JWTSubject
{
    protected $primaryKey = 'employee_id';
    public $incrementing = false;
    protected $keyType = 'integer';

    protected $fillable = [
        'employee_id',
        'name',
        'email',
        'phone_number',
        'address',
        'points',
        'password',
        'first_login',
        'bio_data',
        'status'
    ];

    protected $hidden = [
        'password',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
