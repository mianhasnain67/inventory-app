<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetLog;
use App\Models\Employee;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ITAssetInventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_inventory_summary_and_history_tabs(): void
    {
        $employee = $this->createEmployee();
        $asset = $this->createAsset([
            'assigned_to' => $employee->id,
            'status' => 'assigned',
        ]);
        $asset->logs()->create([
            'employee_id' => $employee->id,
            'action' => 'Assigned',
            'remarks' => 'Development equipment',
        ]);

        $this->get(route('it.dashboard'))
            ->assertOk()
            ->assertSee('IT asset inventory')
            ->assertSee('IT-LAP-TEST')
            ->assertSee('Assignment history')
            ->assertSee('Development equipment');
    }

    public function test_asset_creation_form_includes_a_csrf_token(): void
    {
        $this->get(route('it.dashboard'))
            ->assertOk()
            ->assertSee('action="'.route('assets.store').'"', false)
            ->assertSee('name="_token"', false)
            ->assertSee('value="'.csrf_token().'"', false);
    }

    public function test_asset_assignment_and_return_update_status_and_write_history(): void
    {
        $employee = $this->createEmployee();
        $asset = $this->createAsset();

        $this->post(route('assets.assign', $asset->id), [
            'employee_id' => $employee->id,
            'remarks' => 'Issued for a new project.',
        ])->assertRedirect(route('it.dashboard'));

        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
            'assigned_to' => $employee->id,
            'status' => 'assigned',
        ]);
        $this->assertDatabaseHas('asset_logs', [
            'asset_id' => $asset->id,
            'employee_id' => $employee->id,
            'action' => 'Assigned',
            'remarks' => 'Issued for a new project.',
        ]);

        $this->post(route('assets.return', $asset->id), [
            'remarks' => 'Returned at project close.',
        ])->assertRedirect(route('it.dashboard'));

        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
            'assigned_to' => null,
            'status' => 'available',
        ]);
        $this->assertDatabaseHas('asset_logs', [
            'asset_id' => $asset->id,
            'employee_id' => $employee->id,
            'action' => 'Returned',
            'remarks' => 'Returned at project close.',
        ]);
        $this->assertSame(2, AssetLog::where('asset_id', $asset->id)->count());
    }

    public function test_unavailable_asset_cannot_be_assigned(): void
    {
        $employee = $this->createEmployee();
        $asset = $this->createAsset(['status' => 'maintenance']);

        $this->from(route('it.dashboard'))
            ->post(route('assets.assign', $asset->id), [
                'employee_id' => $employee->id,
            ])
            ->assertRedirect(route('it.dashboard'))
            ->assertSessionHasErrors('asset');

        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
            'assigned_to' => null,
            'status' => 'maintenance',
        ]);
        $this->assertDatabaseCount('asset_logs', 0);
    }

    public function test_seeder_creates_repeatable_sample_inventory(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('employees', 4);
        $this->assertDatabaseCount('asset_categories', 5);
        $this->assertDatabaseCount('assets', 6);
        $this->assertDatabaseCount('asset_logs', 5);
        $this->assertDatabaseHas('assets', [
            'asset_tag' => 'IT-LAP-001',
            'status' => 'assigned',
        ]);
    }

    public function test_asset_can_be_added_and_deleted(): void
    {
        $category = AssetCategory::create(['name' => 'Monitors']);

        $this->post(route('assets.store'), [
            'category_id' => $category->id,
            'asset_tag' => 'IT-MON-NEW',
            'model_name' => '4K Monitor',
            'serial_number' => 'MON-NEW-001',
            'specifications' => '27-inch, 4K UHD',
            'purchase_date' => '2025-06-01',
            'purchase_cost' => 450.00,
        ])->assertRedirect(route('it.dashboard'));

        $asset = Asset::where('asset_tag', 'IT-MON-NEW')->firstOrFail();
        $this->assertSame('available', $asset->status);

        $this->delete(route('assets.destroy', $asset->id))
            ->assertRedirect(route('it.dashboard'));

        $this->assertDatabaseMissing('assets', ['id' => $asset->id]);
    }

    private function createEmployee(): Employee
    {
        return Employee::create([
            'name' => 'Alex Rivera',
            'email' => 'alex@example.test',
            'designation' => 'Software Developer',
            'department' => 'Engineering',
        ]);
    }

    private function createAsset(array $overrides = []): Asset
    {
        $category = AssetCategory::firstOrCreate(['name' => 'Laptops']);

        return Asset::create(array_merge([
            'category_id' => $category->id,
            'asset_tag' => 'IT-LAP-TEST',
            'model_name' => 'ThinkPad X1 Carbon',
            'serial_number' => 'SERIAL-TEST-001',
            'specifications' => '16GB RAM, 512GB SSD',
            'purchase_date' => '2025-01-15',
            'purchase_cost' => 1299.00,
            'status' => 'available',
        ], $overrides));
    }
}
