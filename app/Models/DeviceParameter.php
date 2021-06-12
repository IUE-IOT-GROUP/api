<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class DeviceParameter extends Pivot
{
    const TABLE_NAME = 'device_parameter';
    public $incrementing = true;
    protected $with = ['device'];

    protected $guarded = ['id'];

    public function device()
    {
        return $this->belongsTo(UserDevice::class, 'user_device_id');
    }

    public function parameter()
    {
        return $this->belongsTo(ParameterType::class, 'parameter_type_id');
    }
}
