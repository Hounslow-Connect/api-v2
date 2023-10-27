<?php

namespace App\Docs\Paths\Search;

use App\Docs\Operations\Search\StoreSearchOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class SearchRootPath extends PathItem
{
    /**
     * @return static
     *
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/search')
            ->operations(
                StoreSearchOperation::create()
            );
    }
}
