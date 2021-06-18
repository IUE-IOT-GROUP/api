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

    protected $guarded = ['id'];
    protected $with = ['device'];

    public function device()
    {
        return $this->belongsTo(UserDevice::class, 'user_device_id');
    }

    public function parameter()
    {
        return $this->belongsTo(ParameterType::class, 'parameter_type_id');
    }
}
