<?php

namespace App\Models;

use App\Traits\HasUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Relations\Pivot;

class DeviceParameter extends Pivot
{
    use HasUuidAsPrimaryKey;

    const TABLE_NAME = 'device_parameter';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = false;
    protected $with = ['device'];

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id');
    }

    public function parameter()
    {
        return $this->belongsTo(Parameter::class, 'parameter_id');
    }
}
