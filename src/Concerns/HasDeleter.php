<?php

declare(strict_types=1);

namespace Hatchyu\Eloquent\Foundation\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

trait HasDeleter
{
    public static function deleterColumn(): string
    {
        return Config::get('eloquent-foundation.columns.deleter', 'deleted_by');
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(
            Config::get('eloquent-foundation.user_model'),
            static::deleterColumn()
        );
    }

    protected static function bootHasDeleter(): void
    {
        static::deleting(function (Model $model): void {
            if (! Auth::check()) {
                return;
            }

            // Only for SoftDeletes models
            if (! method_exists($model, 'isForceDeleting')) {
                return;
            }

            // Skip force deletes
            if ($model->isForceDeleting()) {
                return;
            }

            $model->{static::deleterColumn()} = Auth::id();

            // We must save the model here, otherwise the 'deleted_by'
            // column won't be updated during the soft-delete process.
            $model->saveQuietly();
        });
    }
}
