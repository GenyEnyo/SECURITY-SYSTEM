<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\IncidentOccurrence;
use App\Models\IncidentStatus;
use App\Models\IncidentType;
use App\Models\KpiGroup;
use App\Models\KpiScorecard;
use App\Models\Location;
use App\Models\Place;
use App\Models\Severity;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private Location $accra;

    private Location $tema;

    protected function setUp(): void
    {
        parent::setUp();

        // A Wednesday, so "this week" has days on both sides.
        Carbon::setTestNow('2026-10-14 12:00:00');

        $this->seed(DatabaseSeeder::class);

        $this->accra = Location::where('name', 'Accra')->firstOrFail();
        $this->tema = Location::where('name', 'Tema')->firstOrFail();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    private function userAt(Location $location, string $role): User
    {
        return User::factory()->create(['location_id' => $location->id])->assignRole($role);
    }

    private function incidentAt(Location $location, User $reporter, string $occurredAt = '2026-10-13 09:00:00', string $severity = 'Low'): IncidentOccurrence
    {
        $building = Building::where('location_id', $location->id)->firstOrFail();
        $place = Place::firstOrCreate(['building_id' => $building->id, 'name' => 'Main gate']);

        return IncidentOccurrence::create([
            'incident_type_id'   => IncidentType::value('id'),
            'occurred_at'        => $occurredAt,
            'location_id'        => $location->id,
            'building_id'        => $building->id,
            'place_id'           => $place->id,
            'severity_id'        => Severity::where('name', $severity)->value('id'),
            'incident_status_id' => IncidentStatus::where('name', 'Reported')->value('id'),
            'user_id'            => $reporter->id,
        ]);
    }

    private function scorecardFor(Building $building, int $target = 0, int $scored = 0): KpiScorecard
    {
        $card = KpiScorecard::create([
            'location_id'   => $building->location_id,
            'building_id'   => $building->id,
            'location_name' => $building->location?->name,
            'building_name' => $building->name,
            'date'          => now()->toDateString(),
        ]);

        if ($target > 0) {
            $place = Place::firstOrCreate(['building_id' => $building->id, 'name' => 'Main gate']);
            $deployment = KpiGroup::where('name', 'Deployment')->firstOrFail();
            $other = KpiGroup::where('name', 'Reliability')->firstOrFail();

            $card->lines()->create(['kpi_group_id' => $deployment->id, 'place_id' => $place->id, 'criteria' => 'Main gate', 'target' => $target, 'scored' => $scored]);
            // Non-deployment lines must not count as guards.
            $card->lines()->create(['kpi_group_id' => $other->id, 'criteria' => 'Integrity', 'target' => 50, 'scored' => 40]);
        }

        return $card;
    }

    public function test_dashboard_shows_no_template_figures(): void
    {
        $this->actingAs($this->userAt($this->accra, 'Supervisor'))->get('/dashboard')
            ->assertOk()
            ->assertSee('No incidents recorded.')
            ->assertDontSee('Item Loss')
            ->assertDontSee('90.5%')
            ->assertViewHas('incidentsWeek', 0);
    }

    public function test_incidents_are_limited_to_the_users_location(): void
    {
        $supervisor = $this->userAt($this->accra, 'Supervisor');
        $other = $this->userAt($this->accra, 'Senior Officer');

        $this->incidentAt($this->accra, $other);
        $this->incidentAt($this->accra, $other, '2026-10-12 08:00:00', 'Urgent');
        $this->incidentAt($this->accra, $other, '2026-10-06 08:00:00'); // last week
        $this->incidentAt($this->tema, $other);

        $this->actingAs($supervisor)->get('/dashboard')
            ->assertOk()
            ->assertViewHas('incidentsWeek', 2)
            ->assertViewHas('incidentsDiff', 1)
            ->assertViewHas('recent', fn ($recent) => $recent->count() === 3 && $recent->every(fn ($i) => $i->location_id === $this->accra->id))
            ->assertViewHas('week', fn ($week) => $week[0]['count'] === 1 && $week[0]['critical']
                && $week[1]['count'] === 1 && ! $week[1]['critical']
                && $week[6]['future']);
    }

    public function test_officer_without_view_all_incidents_sees_only_their_own(): void
    {
        $officer = $this->userAt($this->accra, 'Senior Officer');
        $colleague = $this->userAt($this->accra, 'Senior Officer');

        $mine = $this->incidentAt($this->accra, $officer);
        $this->incidentAt($this->accra, $colleague);

        $this->actingAs($officer)->get('/dashboard')
            ->assertOk()
            ->assertViewHas('incidentsWeek', 1)
            ->assertViewHas('recent', fn ($recent) => $recent->pluck('id')->all() === [$mine->id]);
    }

    public function test_deployment_cards_come_from_todays_scorecards(): void
    {
        $supervisor = $this->userAt($this->accra, 'Supervisor');

        $this->scorecardFor(Building::where('location_id', $this->accra->id)->firstOrFail(), 10, 8);
        $this->scorecardFor(Building::where('location_id', $this->tema->id)->firstOrFail(), 20, 20);

        $this->actingAs($supervisor)->get('/dashboard')
            ->assertOk()
            ->assertViewHas('today', fn ($today) => $today['contracted'] === 10 && $today['deployed'] === 8 && $today['pct'] === 80.0)
            ->assertSee('2 short');
    }

    public function test_deployment_cards_are_blank_without_a_scorecard(): void
    {
        $this->actingAs($this->userAt($this->accra, 'Supervisor'))->get('/dashboard')
            ->assertOk()
            ->assertSee('No scorecard yet today');
    }

    public function test_scorecard_banner_disappears_once_every_building_has_one(): void
    {
        $officer = $this->userAt($this->accra, 'Senior Officer');
        $buildings = Building::where('location_id', $this->accra->id)->get();

        $this->actingAs($officer)->get('/dashboard')
            ->assertSee('KPI scorecard not yet submitted')
            ->assertSee("{$buildings->count()} of {$buildings->count()}");

        $buildings->each(fn ($b) => $this->scorecardFor($b));

        $this->actingAs($officer)->get('/dashboard')
            ->assertDontSee('KPI scorecard not yet submitted');
    }
}
