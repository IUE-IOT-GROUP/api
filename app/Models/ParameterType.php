<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParameterType extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function device()
    {
        return $this->belongsToMany(UserDevice::class)
            ->as('devices')
            ->withPivot('id', 'expected_parameter')
            ->withTimestamps()
            ->using(ParameterTypeUserDevice::class);
    }
}
