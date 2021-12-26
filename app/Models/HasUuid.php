<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

trait HasUuid
{

    protected static function booted()
    {
        static::creating(fn(Model $model) => $model->{$model->getKeyName()} = (string) Uuid::uuid4());
    }

    public function getKeyType()
    {
        return 'string';
    }

    public function getIncrementing()
    {
        return false;
    }

}
