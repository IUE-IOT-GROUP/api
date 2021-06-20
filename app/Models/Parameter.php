<?php

namespace App\Models;

use App\Traits\HasUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parameter extends Model
{
    use HasFactory;
    use HasUuidAsPrimaryKey;

    protected $keyType = 'string';
    public $incrementing = false;

    protected static $unguarded = true;

    public function device()
    {
        return $this->belongsToMany(Device::class, DeviceParameter::TABLE_NAME)
            ->as('devices')
            ->withPivot('id', 'expected_parameter')
            ->withTimestamps()
            ->using(DeviceParameter::class);
    }
}
