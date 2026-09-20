<?php

declare(strict_types=1);

namespace Hatchyu\Eloquent\Foundation\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use LogicException;

trait HasCreator
{
    public static function creatorColumn(): string
    {
        return Config::get('eloquent-foundation.columns.creator', 'created_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            Config::get('eloquent-foundation.user_model'),
            static::creatorColumn()
        );
    }

    protected static function bootHasCreator(): void
    {
        static::creating(function (Model $model): void {
            if (! Auth::check() || App::runningInConsole()) {
                return;
            }

            $column = static::creatorColumn();

            // If override is allowed AND value is explicitly provided → respect it
            if (
                property_exists($model, 'allowCreatorOverride')
                && $model->allowCreatorOverride
            ) {
                if (is_null($model->{$column})) {
                    throw new LogicException(
                        'allowCreatorOverride is true but '.$column.' is not set.'
                    );
                }

                return;
            }

            $model->{$column} = Auth::id();
        });
    }
}
