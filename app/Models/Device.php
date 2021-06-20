<?php

namespace App\Models;

use App\Traits\HasUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;
    use HasUuidAsPrimaryKey;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deviceType()
    {
        return $this->belongsTo(DeviceType::class);
    }

    public function fog()
    {
        return $this->belongsTo(Fog::class);
    }

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function data()
    {
        return $this->hasMany(DeviceData::class);
    }

    public function parameters()
    {
        return $this->belongsToMany(Parameter::class, DeviceParameter::TABLE_NAME)
            ->as('parameters')
            ->withPivot('id', 'expected_parameter')
            ->withTimestamps()
            ->using(DeviceParameter::class);
    }
}
