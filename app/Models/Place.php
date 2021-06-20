<?php

namespace App\Models;

use App\Events\PlaceSavedEvent;
use App\Traits\HasUuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Place extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUuidAsPrimaryKey;

    public const FIELDS = ['id', 'name', 'parent_id', 'user_id', 'created_at', 'updated_at', 'deleted_at'];
    protected $keyType = 'string';
    public $incrementing = false;

    protected $dispatchesEvents = [
        'saved' => PlaceSavedEvent::class
    ];

    protected static $unguarded = true;

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

    public function fogs()
    {
        return $this->hasMany(Fog::class);
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }
}
