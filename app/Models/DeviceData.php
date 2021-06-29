<?php

namespace App\Models;

use App\Traits\HasUuidAsPrimaryKey;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceData extends Model
{
    use HasFactory;
    use HasUuidAsPrimaryKey;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $guarded = false;
    protected $with = ['parameter', 'device'];

    protected $casts = [
        'value' => 'float',
    ];

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
        return $date->format('Y-m-d H:i:s');
    }

    public function markAsSynchronized()
    {
        $this->is_synchronized = true;
        $this->synchronization_time = Carbon::now();
    }
}
