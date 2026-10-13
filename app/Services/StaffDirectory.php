<?php

namespace App\Services;

use App\Models\Location;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Local copy of the HR staff list, and the link between a staff record
 * and an app user (department and location).
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

        return $rows->count();
    }

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
     * Fill the user's blank staff number, department, section and location from
     * their staff record. Values that are already set are never overwritten.
     */
    public function fill(User $user): bool
    {
        if (filled($user->staff_number) && filled($user->department) && filled($user->section) && $user->location_id !== null) {
            return false;
        }

        if (! $staff = $this->find($user)) {
            return false;
        }

        $changes = [];

        if (blank($user->staff_number)) {
            $changes['staff_number'] = $staff->staff_number;
        }

        if (blank($user->department) && filled($staff->department)) {
            $changes['department'] = $staff->department;
        }

        if (blank($user->section) && filled($staff->section)) {
            $changes['section'] = $staff->section;
        }

        // A staff location the app does not know is left for an admin to set.
        if ($user->location_id === null && filled($staff->location_name)) {
            $locationId = Location::withoutGlobalScope('user_location')
                ->whereRaw('LOWER(name) = ?', [strtolower($staff->location_name)])
                ->value('id');

            if ($locationId) {
                $changes['location_id'] = $locationId;
            }
        }

        return $changes !== [] && $user->update($changes);
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
