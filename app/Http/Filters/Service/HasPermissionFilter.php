<?php

namespace App\Http\Filters\Service;

use App\Models\Service;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class HasPermissionFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $serviceIds = request()->user('api')
            ? request()->user('api')->services()->pluck(table(Service::class, 'id'))->toArray()
            : [];

        return $query->whereIn(table(Service::class, 'id'), $serviceIds);
    }
}
