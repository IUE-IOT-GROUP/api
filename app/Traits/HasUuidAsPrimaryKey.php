<?php


namespace App\Traits;


use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

trait HasUuidAsPrimaryKey
{
    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            $model->setKeyType('string');
            $model->setIncrementing(false);

            if (empty($model->attributesToArray()['id']))
                $model->setAttribute($model->getKeyName(), Uuid::uuid4());
        });
    }
}
