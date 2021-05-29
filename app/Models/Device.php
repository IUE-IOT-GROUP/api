<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Device extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];

    public function parameters()
    {
        return $this->belongsToMany(ParameterType::class)->as('parameters')->using(DeviceParameterType::class);
    }
}
