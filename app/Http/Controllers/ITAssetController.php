<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetLog;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ITAssetController extends Controller
{
    public function dashboard(): View
    {
        $assets = Asset::with(['category', 'employee'])->orderBy('asset_tag')->get();

        return view('it_inventory', [
            'assets' => $assets,
            'categories' => AssetCategory::orderBy('name')->get(),
            'employees' => Employee::orderBy('name')->get(),
            'totalAssets' => $assets->count(),
            'assignedAssets' => $assets->where('status', 'assigned')->count(),
            'availableAssets' => $assets->where('status', 'available')->count(),
            'maintenanceAssets' => $assets->where('status', 'maintenance')->count(),
            'totalValuation' => $assets->sum(fn (Asset $asset) => (float) $asset->purchase_cost),
            'recentLogs' => AssetLog::with(['asset', 'employee'])->latest()->limit(12)->get(),
        ]);
    }

    public function storeAsset(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:asset_categories,id'],
            'asset_tag' => ['required', 'string', 'max:255', 'unique:assets,asset_tag'],
            'model_name' => ['required', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:255', 'unique:assets,serial_number'],
            'specifications' => ['required', 'string', 'max:5000'],
            'purchase_date' => ['required', 'date'],
            'purchase_cost' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
        ]);

        Asset::create($validated + ['status' => 'available']);

        return redirect()->route('it.dashboard')->with('status', 'Hardware asset added to inventory.');
    }

    public function assignAsset(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated, $id): void {
            $asset = Asset::whereKey($id)->lockForUpdate()->firstOrFail();
            $employee = Employee::findOrFail($validated['employee_id']);
            $asset->assignTo($employee, $validated['remarks'] ?? null);
        });

        return redirect()->route('it.dashboard')->with('status', 'Asset assigned and history recorded.');
    }

    public function returnAsset(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'remarks' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated, $id): void {
            $asset = Asset::whereKey($id)->lockForUpdate()->firstOrFail();
            $asset->returnToStock($validated['remarks'] ?? null);
        });

        return redirect()->route('it.dashboard')->with('status', 'Asset returned to available stock.');
    }

    public function destroyAsset(int $id): RedirectResponse
    {
        Asset::findOrFail($id)->delete();

        return redirect()->route('it.dashboard')->with('status', 'Asset deleted from inventory.');
    }
}
