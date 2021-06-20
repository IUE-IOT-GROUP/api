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

            if ($model->isGuarded('id'))
                $model->setAttribute($model->getKeyName(), Uuid::uuid4());
        });
    }
}
