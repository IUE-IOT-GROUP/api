<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ParameterTypeUserDevice extends Pivot
{
    public $incrementing = true;
    protected $table = 'parameter_type_user_device';
    protected $with = ['device'];

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
