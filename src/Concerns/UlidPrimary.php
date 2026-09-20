<?php

declare(strict_types=1);

namespace Hatchyu\Eloquent\Foundation\Concerns;

use Illuminate\Database\Eloquent\Model;

use function Hatchyu\Support\eloquent_ulid;

trait UlidPrimary
{
    public function getIncrementing(): bool
    {
        return false;
    }

    public function getKeyType(): string
    {
        return 'string';
    }

    protected static function bootUlidPrimary(): void
    {
        static::creating(function (Model $model): void {
            if (! $model->getKey()) {
                $model->{$model->getKeyName()} = eloquent_ulid();
            }
        });
    }
}
