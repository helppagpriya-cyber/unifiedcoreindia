<?php

namespace App\Traits;

trait HasCodeGeneration
{
    protected static function bootHasCodeGeneration(): void
    {
        static::created(function ($model) {

            if (empty($model->client_code)) {

                $model->updateQuietly([
                    'client_code' => 'CLI-' .
                        str_pad(
                            $model->id,
                            5,
                            '0',
                            STR_PAD_LEFT
                        ),
                ]);
            }
        });
    }

    protected static function generateCode(
        string $prefix
    ): string {

        $lastId = static::withTrashed()
            ->max('id');

        $nextId = $lastId + 1;

        return sprintf(
            '%s-%05d',
            $prefix,
            $nextId
        );
    }
}
