<?php

namespace App\Models;

use App\Traits\HasUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;

class Fog extends Model
{
    use HasUuidAsPrimaryKey;

    protected $keyType = 'string';
    public $incrementing = false;
    protected static $unguarded = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }
}
