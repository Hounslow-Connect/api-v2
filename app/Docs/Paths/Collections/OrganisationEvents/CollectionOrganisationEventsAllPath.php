<?php

namespace App\Docs\Paths\Collections\OrganisationEvents;

use App\Docs\Operations\Collections\OrganisationEvents\AllCollectionOrganisationEventOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class CollectionOrganisationEventsAllPath extends PathItem
{
    /**
     * @param  string|null  $objectId
     * @return static
     *
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->route('/collections/organisation-events/all')
            ->operations(
                AllCollectionOrganisationEventOperation::create()
            );
    }
}
