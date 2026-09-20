<?php

declare(strict_types=1);

namespace Hatchyu\Eloquent\Foundation\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait UuidPrimary
{
    public function getIncrementing(): bool
    {
        return false;
    }

    public function getKeyType(): string
    {
        return 'string';
    }

    protected static function bootUuidPrimary(): void
    {
        static::creating(function (Model $model): void {
            if (! $model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
