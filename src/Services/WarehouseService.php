<?php

namespace Dev3bdulrahman\Inventory\Services;

use Dev3bdulrahman\Inventory\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class WarehouseService
{
    /**
     * Get all warehouses for a company.
     */
    public function getCompanyWarehouses(?int $companyId)
    {
        if (is_null($companyId)) {
            return Warehouse::all();
        }
        return Warehouse::where('company_id', $companyId)->get();
    }

    /**
     * Create a new warehouse.
     */
    public function createWarehouse(array $data)
    {
        return DB::transaction(function () use ($data) {
            if (!empty($data['is_main']) && !empty($data['company_id'])) {
                Warehouse::where('company_id', $data['company_id'])
                    ->update(['is_main' => false]);
            }

            return Warehouse::create($data);
        });
    }

    /**
     * Update a warehouse.
     */
    public function updateWarehouse(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $warehouse = Warehouse::findOrFail($id);

            if (!empty($data['is_main'])) {
                Warehouse::where('company_id', $warehouse->company_id)
                    ->where('id', '!=', $id)
                    ->update(['is_main' => false]);
            }

            $warehouse->update($data);
            return $warehouse;
        });
    }

    /**
     * Delete a warehouse.
     */
    public function deleteWarehouse(int $id): bool
    {
        $warehouse = Warehouse::findOrFail($id);
        return $warehouse->delete();
    }
}
