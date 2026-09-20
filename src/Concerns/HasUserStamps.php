<?php

declare(strict_types=1);

namespace Hatchyu\Eloquent\Foundation\Concerns;

trait HasUserStamps
{
    use HasCreator;
    use HasDeleter;
    use HasUpdater;
}
