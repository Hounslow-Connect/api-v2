<?php

namespace App\Docs\Operations\OrganisationEvents;

use App\Docs\Responses\ResourceDeletedResponse;
use App\Docs\Tags\OrganisationEventsTag;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Operation;

class DestroyOrganisationEventOperation extends Operation
{
    /**
     * @param string|null $objectId
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->action(static::ACTION_DELETE)
            ->tags(OrganisationEventsTag::create())
            ->summary('Delete a specific organisation event')
            ->description('**Permission:** `Organisation Admin`')
            ->responses(ResourceDeletedResponse::create(null, 'organisation event'));
    }
}
