<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceData extends Model
{
    use HasFactory;

    public function device()
    {
        return $this->belongsTo(UserDevice::class, 'user_device_id', 'id');
    }
}
