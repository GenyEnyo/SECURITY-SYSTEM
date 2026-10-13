<?php

namespace App\Listeners;

use App\Models\User;
use App\Services\StaffDirectory;
use LdapRecord\Laravel\Events\Import\Saved;

class FillUserFromStaff
{
    public function __construct(private StaffDirectory $directory) {}

    /**
     * Runs whenever a directory user is saved locally, on login and on ldap:import.
     */
    public function handle(Saved $event): void
    {
        if ($event->eloquent instanceof User) {
            $this->directory->fill($event->eloquent);
        }
    }
}
