<?php

namespace App\Observers;

use App\Models\Organisation;
use App\Models\Role;
use App\Models\User;

class OrganisationObserver
{
    /**
     * Handle the organisation "created" event.
     *
     * @param  \App\Models\Organisation  $organisation
     * @return void
     */
    public function created(Organisation $organisation)
    {
        Role::globalAdmin()->users()->get()->each(function (User $user) use ($organisation) {
            $user->makeOrganisationAdmin($organisation);
        });
    }

    /**
     * Handle the organisation "deleting" event.
     *
     * @param  \App\Models\Organisation  $organisation
     * @return void
     */
    public function deleting(Organisation $organisation)
    {
        $organisation->userRoles()->delete();
        $organisation->updateRequests()->delete();
        $organisation->services()->delete();
    }
}
