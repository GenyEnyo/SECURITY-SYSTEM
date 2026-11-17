<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Concerns\Auditable;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use LdapRecord\Laravel\Auth\AuthenticatesWithLdap;
use LdapRecord\Laravel\Auth\LdapAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements LdapAuthenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, AuthenticatesWithLdap, Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'staff_number',
        'name',
        'username',
        'email',
        'password',
        'guid',
        'domain',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(IncidentOccurrence::class);
    }

    /**
     * The user's HR record. Users only hold what AD gives at login; department,
     * section and location always come from here.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_number', 'staff_number');
    }

    /** Location resolved once per request (the location scope asks on every query). */
    private Location|false|null $resolvedLocation = null;

    /**
     * The user's location from their staff record: the admin's correction if
     * set, otherwise the HR location matched by name.
     */
    public function location(): ?Location
    {
        if ($this->resolvedLocation === null) {
            $this->resolvedLocation = $this->staff?->effectiveLocation() ?? false;
        }

        return $this->resolvedLocation ?: null;
    }

    public function locationId(): ?int
    {
        return $this->location()?->id;
    }

    /** Forget the cached staff record and location, e.g. after a staff sync. */
    public function refreshStaff(): static
    {
        $this->resolvedLocation = null;
        $this->unsetRelation('staff');

        return $this;
    }

    public function seesAllLocations(): bool
    {
        return $this->hasRole('Super-Admin') || $this->checkPermissionTo('View All Locations');
    }

    /** Users whose staff record puts them in the security section at the given location. */
    public function scopeSupervisingOfficers(Builder $query, ?int $locationId): void
    {
        $locationName = Location::withoutGlobalScope('user_location')->whereKey($locationId)->value('name');

        $query->whereHas('staff', fn (Builder $staff) => $staff
            ->where(fn (Builder $at) => $at
                ->where('location_id', $locationId)
                ->orWhere(fn (Builder $hr) => $hr
                    ->whereNull('location_id')
                    ->whereRaw('LOWER(location_name) = ?', [strtolower((string) $locationName)])))
            ->whereRaw('LOWER(department) = ?', [strtolower(config('security.supervisor_department'))])
            ->whereRaw('LOWER(section) = ?', [strtolower(config('security.supervisor_section'))]));
    }
}
