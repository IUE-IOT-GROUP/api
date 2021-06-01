<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserDeviceParameterType extends Pivot
{
    public $incrementing = true;

    protected $guarded = ['id'];

    public function device()
    {
        return $this->belongsTo(UserDevice::class, 'user_device_id');
    }

    public function parameterType()
    {
        return $this->belongsTo(ParameterType::class, 'parameter_type_id');
    }
}
