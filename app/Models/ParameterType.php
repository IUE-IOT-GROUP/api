<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParameterType extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function device()
    {
        return $this->belongsToMany(UserDevice::class, DeviceParameter::TABLE_NAME)
            ->as('devices')
            ->withPivot('id', 'expected_parameter')
            ->withTimestamps()
            ->using(DeviceParameter::class);
    }
}
