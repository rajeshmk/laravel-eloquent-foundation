<?php

declare(strict_types=1);

namespace Hatchyu\Eloquent\Foundation\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

trait HasMediaUploader
{
    /**
     * Temporary storage for pending uploads, keyed by model instance.
     */
    protected array $pendingUploads = [];

    /**
     * Boot the trait to listen for model events.
     */
    public static function bootHasMediaUploader(): void
    {
        // Listen for the 'saving' event to collect UploadedFile instances
        static::saving(function (Model $model): void {
            $model->pendingUploads = []; // Initialize temporary storage

            foreach ($model->getDirty() as $column => $value) {
                if ($value instanceof UploadedFile) {
                    // Store both the UploadedFile and the current column value
                    $model->pendingUploads[$column] = [
                        'file' => $value,
                        'previous' => $model->getOriginal($column),
                    ];

                    $model->{$column} = null; // Clear the attribute to avoid serialization issues
                }
            }
        });

        // Listen for the 'saved' event to schedule uploads after transaction commits
        static::saved(function (Model $model): void {
            if (! empty($model->pendingUploads)) {
                // Schedule file uploads to run after the transaction commits
                DB::afterCommit(function () use ($model): void {
                    foreach ($model->pendingUploads as $column => $upload) {
                        $model->handleUploadedFile($upload['file'], $column, null, $upload['previous']);
                    }

                    $model->pendingUploads = []; // Clear pending uploads
                });
            }
        });
    }

    /**
     * Handle the upload of a file for a given column.
     */
    public function handleUploadedFile(UploadedFile $file, string $column, ?string $path = null, ?string $previous = null): self
    {
        // Use provided path or model/trait's FILE_UPLOAD_PATH
        $storagePath = $path ?? $this->getFileStoragePath();

        $this->forceFill([
            $column => $file->storePublicly($storagePath, ['disk' => 'public']),
        ])->save();

        if ($previous) {
            Storage::disk('public')->delete($previous);
        }

        return $this;
    }

    /**
     * Get the file storage path for the model.
     */
    protected function getFileStoragePath(): string
    {
        return defined('static::FILE_UPLOAD_PATH')
            ? static::FILE_UPLOAD_PATH
            : 'uploads/default';
    }
}
