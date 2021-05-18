<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function parameters()
    {
        return $this->belongsToMany(ParameterType::class)->as('parameters')->using(DeviceParameterType::class);
    }
}
