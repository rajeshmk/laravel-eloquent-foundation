<?php

declare(strict_types=1);

namespace Hatchyu\Eloquent\Foundation\Concerns;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory as BaseHasFactory;

trait HasFactory
{
    use BaseHasFactory {
        newFactory as baseNewFactory;
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return Factory<static>
     */
    protected static function newFactory()
    {
        $classString = get_called_class();
        $classString = str_replace('App\\', 'Database\\Factories\\', $classString);
        $classString = str_replace('Domain\\Models\\', '', $classString);
        $classString .= 'Factory';

        return resolve($classString);
    }
}
