<?php

namespace App\Docs\Operations\Pages;

use App\Docs\Parameters\MaxDimensionParameter;
use App\Docs\Parameters\UpdateRequestIdParameter;
use App\Docs\Responses\PngResponse;
use App\Docs\Tags\PagesTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;

class ImagePageOperation extends Operation
{
    /**
     * @return static
     *
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->action(static::ACTION_GET)
            ->tags(PagesTag::create())
            ->summary("Get a specific information page's image")
            ->description('**Permission:** `Open`')
            ->noSecurity()
            ->parameters(
                MaxDimensionParameter::create(),
                UpdateRequestIdParameter::create()
            )
            ->responses(PngResponse::create());
    }
}
