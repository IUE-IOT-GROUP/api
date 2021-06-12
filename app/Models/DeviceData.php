<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceData extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $with = ['parameter', 'device'];

    public function device()
    {
        return $this->belongsTo(UserDevice::class, 'user_device_id', 'id');
    }

    public function parameter()
    {
        return $this->belongsTo(DeviceParameter::class, 'parameter_type_user_device_id', 'id');
    }
}
