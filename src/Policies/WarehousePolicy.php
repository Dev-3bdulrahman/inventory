<?php

namespace Dev3bdulrahman\Inventory\Policies;

use App\Models\User;
use Dev3bdulrahman\Inventory\Models\Warehouse;

class WarehousePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('inventory.warehouses.view');
    }

    public function view(User $user, Warehouse $warehouse): bool
    {
        return $user->can('inventory.warehouses.view') && $warehouse->company_id === $user->company_id;
    }

    public function create(User $user): bool
    {
        return $user->can('inventory.warehouses.create');
    }

    public function update(User $user, Warehouse $warehouse): bool
    {
        return $user->can('inventory.warehouses.update') && $warehouse->company_id === $user->company_id;
    }

    public function delete(User $user, Warehouse $warehouse): bool
    {
        return $user->can('inventory.warehouses.delete') && $warehouse->company_id === $user->company_id;
    }
}
