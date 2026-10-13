<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\StaffDirectory;
use Illuminate\Console\Command;
use RuntimeException;

class SyncStaff extends Command
{
    protected $signature = 'staff:sync';

    protected $description = 'Pull the staff list from the staff API and fill blank user departments and locations from it';

    public function handle(StaffDirectory $directory): int
    {
        try {
            $count = $directory->sync();
        } catch (RuntimeException $e) {
            $this->error($e->getMessage().' The stored staff list was left unchanged.');

            return self::FAILURE;
        }

        $this->info("Stored {$count} staff record(s).");

        $filled = 0;

        User::whereNull('staff_number')->orWhereNull('department')->orWhereNull('section')->orWhereNull('location_id')
            ->each(function (User $user) use ($directory, &$filled) {
                $filled += (int) $directory->fill($user);
            });

        $this->info("Filled details for {$filled} user(s).");

        return self::SUCCESS;
    }
}
