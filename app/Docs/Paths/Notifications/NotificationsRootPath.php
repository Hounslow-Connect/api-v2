<?php

namespace App\Docs\Paths\Notifications;

use App\Docs\Operations\Notifications\IndexNotificationOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class NotificationsRootPath extends PathItem
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
            ->route('/notifications')
            ->operations(
                IndexNotificationOperation::create()
            );
    }
}
