<?php

namespace App\Models;

use App\Traits\HasUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParameterType extends Model
{
    use HasFactory;
    use HasUuidAsPrimaryKey;

    protected $keyType = 'string';
    public $incrementing = false;

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
