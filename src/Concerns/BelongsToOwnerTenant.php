<?php

declare(strict_types=1);

namespace Hatchyu\Eloquent\Foundation\Concerns;

use Hatchyu\Eloquent\Foundation\Scopes\OwnerScope;

trait BelongsToOwnerTenant
{
    use HasOwner;

    public static function bootBelongsToOwnerTenant(): void
    {
        static::addGlobalScope(new OwnerScope(static::ownerColumn()));
    }
}
