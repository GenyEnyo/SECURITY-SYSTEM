<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\StaffDirectory;
use Illuminate\Console\Command;
use RuntimeException;

class SyncStaff extends Command
{
    protected $signature = 'staff:sync';

    protected $description = 'Pull the staff list from the staff API and link users with no staff number to it by email';

    public function handle(StaffDirectory $directory): int
    {
        try {
            $count = $directory->sync();
        } catch (RuntimeException $e) {
            $this->error($e->getMessage().' The stored staff list was left unchanged.');

            return self::FAILURE;
        }

        $this->info("Stored {$count} staff record(s).");

        if ($directory->lastClearedOverrides > 0) {
            $this->info("{$directory->lastClearedOverrides} location correction(s) cleared because HR updated the location.");
        }

        $filled = 0;

        User::whereNull('staff_number')->orWhere('staff_number', '')
            ->each(function (User $user) use ($directory, &$filled) {
                $filled += (int) $directory->fill($user);
            });

        $this->info("Linked {$filled} user(s) to their staff record.");

        return self::SUCCESS;
    }
}
