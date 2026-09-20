<?php

declare(strict_types=1);

namespace Hatchyu\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

if (! function_exists(__NAMESPACE__.'\eloquent_ulid')) {
    function eloquent_ulid(): string
    {
        return Str::ulid()->toBase32();
    }
}

if (! function_exists(__NAMESPACE__.'\image_url')) {
    function image_url(
        ?string $path,
        ?int $width = null,
        ?int $height = null,
        array $options = [],
    ): ?string {
        if (! $path) {
            return null;
        }

        /*
         * Placeholder implementation.
         *
         * Consumers may override this behaviour by:
         * - Using an image CDN
         * - Adding resizing parameters
         * - Plugging into Glide / Imgix / Cloudinary / S3
         */
        return Storage::disk('public')->url($path);
    }
}
