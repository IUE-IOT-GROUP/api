<?php

namespace App\Models;

use App\Traits\HasUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fog extends Model
{
    use HasUuidAsPrimaryKey;
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = false;

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
