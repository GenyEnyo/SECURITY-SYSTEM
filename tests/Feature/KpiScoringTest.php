<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\KpiGroup;
use App\Models\KpiScorecard;
use App\Models\Place;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KpiScoringTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);

        $this->admin = User::where('email', 'kwasi@example.com')->firstOrFail();
    }

    public function test_new_group_cannot_push_total_weight_over_100(): void
    {
        // Seeded groups already total 100.
        $this->actingAs($this->admin)->post('/kpi/groups', ['name' => 'Extra', 'weight' => 1])
            ->assertSessionHasErrors('weight');
        $this->assertDatabaseMissing('kpi_groups', ['name' => 'Extra']);

        $this->actingAs($this->admin)->post('/kpi/groups', ['name' => 'Extra', 'weight' => 0])
            ->assertSessionHasNoErrors();
    }

    public function test_editing_a_group_respects_the_100_total(): void
    {
        $deployment = KpiGroup::where('name', 'Deployment')->firstOrFail();

        $this->actingAs($this->admin)->put("/kpi/groups/{$deployment->id}", ['name' => 'Deployment', 'weight' => 61])
            ->assertSessionHasErrors('weight');
        $this->actingAs($this->admin)->put("/kpi/groups/{$deployment->id}", ['name' => 'Deployment', 'weight' => 50])
            ->assertSessionHasNoErrors();

        $this->actingAs($this->admin)->get('/kpi/settings')->assertSee('Total weight 90 / 100');
    }

    public function test_fully_scored_group_totals_its_full_weight(): void
    {
        $building = Building::where('name', 'Head Office')->firstOrFail();
        $group = KpiGroup::where('name', 'Bearing and App')->with('subItems')->firstOrFail();

        $this->actingAs($this->admin)->post('/kpi/entries', [
            'location_id' => $building->location_id,
            'building_id' => $building->id,
            'date'        => '2026-10-06',
            'scored'      => $group->subItems->mapWithKeys(fn ($item) => [$item->id => $item->target])->all(),
        ])->assertSessionHasNoErrors();

        $record = KpiScorecard::firstOrFail();

        // Three equal lines of a 10-weight group: 10.0, not 3 × 3.3 = 9.9.
        $this->assertSame('10.0', number_format($record->lines()->sum('merit'), 1));

        $this->actingAs($this->admin)->get('/kpi/records')->assertSee('<td>10.0</td>', false);
    }

    public function test_score_above_target_is_rejected_on_entry(): void
    {
        $building = Building::where('name', 'Head Office')->firstOrFail();
        $item = KpiGroup::where('name', 'Bearing and App')->with('subItems')->firstOrFail()->subItems->first();

        $this->actingAs($this->admin)->post('/kpi/entries', [
            'location_id' => $building->location_id,
            'building_id' => $building->id,
            'date'        => '2026-10-06',
            'scored'      => [$item->id => $item->target + 1],
        ])->assertSessionHasErrors("scored.{$item->id}");

        $this->assertSame(0, KpiScorecard::count());
    }

    public function test_deployment_beats_are_not_capped(): void
    {
        $building = Building::where('name', 'Head Office')->firstOrFail();
        $place = Place::create(['building_id' => $building->id, 'name' => 'Main gate', 'estimated_guards' => 4]);

        $this->actingAs($this->admin)->post('/kpi/entries', [
            'location_id' => $building->location_id,
            'building_id' => $building->id,
            'date'        => '2026-10-06',
            'beat_scored' => [$place->id => 6],
        ])->assertSessionHasNoErrors();

        $record = KpiScorecard::firstOrFail();
        $beat = $record->lines()->whereNotNull('place_id')->firstOrFail();

        // Editing a beat above its estimate is also allowed.
        $this->actingAs($this->admin)->put("/kpi/records/{$record->id}", ['lines' => [$beat->id => ['scored' => 7]]])
            ->assertSessionHasNoErrors();
        $this->assertSame(7, (int) $beat->fresh()->scored);
    }

    public function test_score_above_target_is_rejected_on_edit(): void
    {
        $building = Building::where('name', 'Head Office')->firstOrFail();
        $item = KpiGroup::where('name', 'Bearing and App')->with('subItems')->firstOrFail()->subItems->first();

        $this->actingAs($this->admin)->post('/kpi/entries', [
            'location_id' => $building->location_id,
            'building_id' => $building->id,
            'date'        => '2026-10-06',
            'scored'      => [$item->id => $item->target],
        ])->assertSessionHasNoErrors();

        $record = KpiScorecard::firstOrFail();
        $line = $record->lines()->firstOrFail();

        $this->actingAs($this->admin)->put("/kpi/records/{$record->id}", ['lines' => [$line->id => ['scored' => $item->target + 1]]])
            ->assertSessionHasErrors("lines.{$line->id}.scored");
        $this->assertSame((int) $item->target, (int) $line->fresh()->scored);
    }
}
