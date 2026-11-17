<?php

namespace App\Services;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Local copy of the HR staff list. Users are linked to it by staff_number.
 */
class StaffDirectory
{
    /** Staff API field => staff table column. */
    private const FIELDS = [
        'PERSON_NUMBER'     => 'staff_number',
        'FULL_NAME'         => 'full_name',
        'FIRST_NAME'        => 'first_name',
        'MIDDLE_NAMES'      => 'middle_names',
        'LAST_NAME'         => 'last_name',
        'TITLE'             => 'title',
        'DEPARTMENT_NAME'   => 'department',
        'SECTION'           => 'section',
        'JOB_NAME'          => 'job_name',
        'POSITION_NAME'     => 'position_name',
        'SUPERVISOR_NUMBER' => 'supervisor_number',
        'EMAIL_ADDRESS'     => 'email',
        'LOCATION_NAME'     => 'location_name',
    ];

    /**
     * Replace the local staff list with the one from the staff API.
     * Nothing is changed when the API fails or returns no staff.
     *
     * @return int number of staff stored
     */
    public function sync(): int
    {
        $response = Http::withOptions(['verify' => (bool) config('services.staff_api.verify')])
            ->timeout((int) config('services.staff_api.timeout'))
            ->acceptJson()
            ->post(config('services.staff_api.url'));

        if ($response->failed()) {
            throw new RuntimeException("The staff API responded with HTTP {$response->status()}.");
        }

        $rows = collect($response->json())
            ->filter(fn ($record) => is_array($record))
            ->map(fn (array $record) => $this->toRow($record))
            ->filter(fn (array $row) => $row['staff_number'] !== null)
            ->keyBy('staff_number');

        if ($rows->isEmpty()) {
            throw new RuntimeException('The staff API returned no staff.');
        }

        $columns = array_values(array_diff(self::FIELDS, ['staff_number']));

        foreach ($rows->values()->chunk(500) as $chunk) {
            Staff::upsert($chunk->all(), ['staff_number'], $columns);
        }

        Staff::whereNotIn('staff_number', $rows->keys()->all())->delete();

        $this->clearOverridesHrHasUpdated();

        return $rows->count();
    }

    /**
     * An admin's location correction lasts until HR changes that person's
     * location. Once HR's value differs from what it was when the correction
     * was made, HR has acted, so the correction is dropped and HR is used.
     *
     * @return int number of corrections cleared
     */
    public function clearOverridesHrHasUpdated(): int
    {
        $normalise = fn (?string $name) => strtolower(trim((string) $name));
        $cleared = 0;

        Staff::whereNotNull('location_id')->each(function (Staff $staff) use ($normalise, &$cleared) {
            if ($normalise($staff->location_name) !== $normalise($staff->location_hr_name_at_override)) {
                // Saved one by one so each clear is written to the audit log.
                $staff->update(['location_id' => null, 'location_hr_name_at_override' => null]);
                $cleared++;
            }
        });

        $this->lastClearedOverrides = $cleared;

        return $cleared;
    }

    /** Corrections cleared by the last sync. */
    public int $lastClearedOverrides = 0;

    public function find(User $user): ?Staff
    {
        if (filled($user->staff_number) && $staff = Staff::where('staff_number', $user->staff_number)->first()) {
            return $staff;
        }

        if (filled($user->email)) {
            return Staff::whereRaw('LOWER(email) = ?', [strtolower($user->email)])->first();
        }

        return null;
    }

    /**
     * Link a user with no staff number (AD account without an employeeid) to
     * their staff record by email. Nothing else is copied onto the user:
     * department, section and location are always read from the staff table.
     */
    public function fill(User $user): bool
    {
        if (filled($user->staff_number) || ! $staff = $this->find($user)) {
            return false;
        }

        $updated = $user->update(['staff_number' => $staff->staff_number]);
        $user->refreshStaff();

        return $updated;
    }

    private function toRow(array $record): array
    {
        $row = [];

        foreach (self::FIELDS as $field => $column) {
            $value = trim((string) ($record[$field] ?? ''));
            $row[$column] = $value === '' ? null : $value;
        }

        return $row;
    }
}
