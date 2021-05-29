<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Place extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function children()
    {
        return $this->hasMany(Place::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Place::class, 'parent_id');
    }

    public function devices()
    {
        return $this->hasMany(UserDevice::class);
    }
}
