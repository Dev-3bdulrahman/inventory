<?php

namespace Dev3bdulrahman\Inventory\Listeners;

use App\Models\User;
use Dev3bdulrahman\Inventory\Events\StockBelowMinimum;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SendStockAlert implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Handle the StockBelowMinimum event.
     */
    public function handle(StockBelowMinimum $event): void
    {
        try {
            $users = User::where('company_id', $event->companyId)
                ->where(function ($query) {
                    $query->whereHas('permissions', function ($q) {
                        $q->where('name', 'inventory.alerts.receive');
                    })->orWhereHas('roles.permissions', function ($q) {
                        $q->where('name', 'inventory.alerts.receive');
                    });
                })
                ->get();

            $notificationData = [
                'title' => __('inventory::inventory.stock_below_minimum'),
                'message' => __('inventory::inventory.stock_below_minimum_message', [
                    'product' => $event->stockItem->product->name ?? 'N/A',
                    'warehouse' => $event->warehouse->name,
                    'current' => $event->currentQuantity,
                    'minimum' => $event->minimumQuantity,
                ]),
                'stock_item_id' => $event->stockItem->id,
                'warehouse_id' => $event->warehouse->id,
                'current_quantity' => $event->currentQuantity,
                'minimum_quantity' => $event->minimumQuantity,
            ];

            foreach ($users as $user) {
                DB::table('notifications')->insert([
                    'id' => Str::uuid()->toString(),
                    'type' => 'Dev3bdulrahman\\Inventory\\Notifications\\StockBelowMinimumNotification',
                    'notifiable_type' => get_class($user),
                    'notifiable_id' => $user->id,
                    'data' => json_encode($notificationData),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('SendStockAlert: Failed to send stock alert notification.', [
                'error' => $e->getMessage(),
                'stock_item_id' => $event->stockItem->id ?? null,
                'warehouse_id' => $event->warehouse->id ?? null,
                'company_id' => $event->companyId ?? null,
            ]);
        }
    }
}
