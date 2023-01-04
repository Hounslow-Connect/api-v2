<?php

namespace App\Docs\Paths\Users;

use App\Docs\Operations\Users\UserUserOperation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\PathItem;

class UsersUserPath extends PathItem
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
            ->route('/users/user')
            ->operations(
                UserUserOperation::create()
            );
    }
}
