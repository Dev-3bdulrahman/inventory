<?php

namespace Dev3bdulrahman\Inventory\Policies;

use App\Models\User;
use Dev3bdulrahman\Inventory\Models\StockAdjustment;

class StockAdjustmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('inventory.adjustments.view');
    }

    public function view(User $user, StockAdjustment $adjustment): bool
    {
        return $user->can('inventory.adjustments.view') && $adjustment->company_id === $user->company_id;
    }

    public function create(User $user): bool
    {
        return $user->can('inventory.adjustments.create');
    }

    public function update(User $user, StockAdjustment $adjustment): bool
    {
        return $user->can('inventory.adjustments.update') && $adjustment->company_id === $user->company_id;
    }

    public function delete(User $user, StockAdjustment $adjustment): bool
    {
        return $user->can('inventory.adjustments.delete') && $adjustment->company_id === $user->company_id;
    }

    public function approve(User $user, StockAdjustment $adjustment): bool
    {
        return $user->can('inventory.adjustments.approve') && $adjustment->company_id === $user->company_id;
    }
}
