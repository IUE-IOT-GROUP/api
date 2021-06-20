<?php

namespace App\Models;

use App\Traits\HasUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;
    use HasUuidAsPrimaryKey;

    public $incrementing = false;
    protected $keyType = 'string';
    protected static $unguarded = true;

    protected $hidden = [
        'password',
        'remember_token',
        'is_admin',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',
    ];

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    public function places(): HasMany
    {
        return $this->hasMany(Place::class);
    }

    public function fogs(): HasMany
    {
        return $this->hasMany(Fog::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }
}
