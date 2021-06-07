<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
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
        return $this->belongsToMany(ParameterType::class)
            ->as('parameters')
            ->withPivot('id', 'expected_parameter')
            ->withTimestamps()
            ->using(ParameterTypeUserDevice::class);
    }
}
