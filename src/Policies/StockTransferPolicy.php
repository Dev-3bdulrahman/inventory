<?php

namespace Dev3bdulrahman\Inventory\Policies;

use App\Models\User;
use Dev3bdulrahman\Inventory\Models\StockTransfer;

class StockTransferPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('inventory.transfers.view');
    }

    public function view(User $user, StockTransfer $transfer): bool
    {
        return $user->can('inventory.transfers.view') && $transfer->company_id === $user->company_id;
    }

    public function create(User $user): bool
    {
        return $user->can('inventory.transfers.create');
    }

    public function update(User $user, StockTransfer $transfer): bool
    {
        return $user->can('inventory.transfers.update') && $transfer->company_id === $user->company_id;
    }

    public function delete(User $user, StockTransfer $transfer): bool
    {
        return $user->can('inventory.transfers.delete') && $transfer->company_id === $user->company_id;
    }

    public function approve(User $user, StockTransfer $transfer): bool
    {
        return $user->can('inventory.transfers.approve') && $transfer->company_id === $user->company_id;
    }
}
