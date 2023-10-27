<?php

namespace App\Docs\Tags;

use GoldSpecDigital\ObjectOrientedOAS\Objects\BaseObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Tag;

class OrganisationSignUpFormsTag extends Tag
{
    /**
     * @return static
     */
    public static function create(string $objectId = null): BaseObject
    {
        return parent::create($objectId)
            ->name('Organisation Sign Up Forms');
    }
}
