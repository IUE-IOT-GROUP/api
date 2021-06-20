<?php

namespace App\Models;

use App\Traits\HasUuidAsPrimaryKey;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceData extends Model
{
    use HasFactory;
    use HasUuidAsPrimaryKey;

    protected $keyType = 'string';
    public $incrementing = false;

    protected static $unguarded = true;
    protected $with = ['parameter', 'device'];

    public function device()
    {
        return $this->belongsTo(Device::class, 'user_device_id', 'id');
    }

    public function parameter()
    {
        return $this->belongsTo(DeviceParameter::class, 'parameter_type_user_device_id', 'id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        ray($date);
        return $date->format('Y-m-d H:i:s');
    }
}
