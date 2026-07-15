<?php

namespace App\Traits;

trait HasEntityCode
{
    protected static function bootHasEntityCode(): void
    {
        static::created(function ($model) {

            $column = $model->getCodeColumn();

            if (! empty($model->{$column})) {
                return;
            }

            $code = sprintf(
                '%s-%05d',
                $model->getCodePrefix(),
                $model->id
            );

            $model->updateQuietly([
                $column => $code,
            ]);
        });
    }

    abstract public function getCodeColumn(): string;

    abstract public function getCodePrefix(): string;
}
