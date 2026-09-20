<?php

declare(strict_types=1);

namespace Hatchyu\Eloquent\Foundation\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class OwnerScope implements Scope
{
    public function __construct(
        protected string $column
    ) {}

    public function apply(Builder $builder, Model $model): void
    {
        if (! Auth::check()) {
            $builder->whereRaw('1 = 0');

            return;
        }

        $builder->where(
            $model->qualifyColumn($this->column),
            Auth::id()
        );
    }

    public function extend(Builder $builder): void
    {
        $builder->macro(
            'withoutOwner',
            fn ($b) => $b->withoutGlobalScope($this)
        );
    }
}
