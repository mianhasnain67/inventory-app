<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class Asset extends Model
{
    protected $fillable = [
        'category_id',
        'assigned_to',
        'asset_tag',
        'model_name',
        'serial_number',
        'specifications',
        'purchase_date',
        'purchase_cost',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'purchase_cost' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AssetLog::class);
    }

    public function assignTo(Employee $employee, ?string $remarks = null): void
    {
        if ($this->status !== 'available' || $this->assigned_to !== null) {
            throw ValidationException::withMessages([
                'asset' => 'Only available assets can be assigned.',
            ]);
        }

        $this->update([
            'assigned_to' => $employee->id,
            'status' => 'assigned',
        ]);

        $this->logs()->create([
            'employee_id' => $employee->id,
            'action' => 'Assigned',
            'remarks' => $remarks,
        ]);
    }

    public function returnToStock(?string $remarks = null): void
    {
        if ($this->status !== 'assigned' || $this->assigned_to === null) {
            throw ValidationException::withMessages([
                'asset' => 'Only assigned assets can be returned.',
            ]);
        }

        $employeeId = $this->assigned_to;

        $this->update([
            'assigned_to' => null,
            'status' => 'available',
        ]);

        $this->logs()->create([
            'employee_id' => $employeeId,
            'action' => 'Returned',
            'remarks' => $remarks,
        ]);
    }
}
