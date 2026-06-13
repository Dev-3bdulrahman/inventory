<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Warehouses (المستودعات)
        Schema::create('inventory_warehouses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->string('name');
            $table->string('code');
            $table->text('address')->nullable();
            $table->string('status')->default('active'); // active, inactive
            $table->boolean('is_main')->default(false);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

            $table->index('company_id');
            $table->index('branch_id');
            $table->index('status');
        });

        // 2. Warehouse Locations (مواقع التخزين)
        Schema::create('inventory_warehouse_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained('inventory_warehouses')->onDelete('cascade');
            $table->string('name');
            $table->string('code')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('warehouse_id');
        });

        // 3. Batches (الدفعات)
        Schema::create('inventory_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('batch_number');
            $table->date('expiry_date')->nullable();
            $table->date('production_date')->nullable();
            $table->timestamps();

            $table->index('company_id');
            $table->index('product_id');
            $table->index('batch_number');
        });

        // 4. Stock Items / Balances (أرصدة المستودعات)
        Schema::create('inventory_stock_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('inventory_warehouses')->onDelete('cascade');
            $table->foreignId('warehouse_location_id')->nullable()->constrained('inventory_warehouse_locations')->onDelete('set null');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->onDelete('set null');
            $table->foreignId('batch_id')->nullable()->constrained('inventory_batches')->onDelete('set null');
            $table->decimal('quantity', 15, 4)->default(0.0000);
            $table->timestamps();

            $table->index('company_id');
            $table->index('warehouse_id');
            $table->index('product_id');
        });

        // 5. Serial Numbers (الأرقام التسلسلية)
        Schema::create('inventory_serial_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('serial_number');
            $table->string('status')->default('active'); // active, sold, returned, damaged
            $table->timestamps();

            $table->index('company_id');
            $table->index('product_id');
            $table->index('serial_number');
        });

        // 6. Stock Moves (حركات المخزن)
        Schema::create('inventory_stock_moves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('inventory_warehouses')->onDelete('cascade');
            $table->foreignId('warehouse_location_id')->nullable()->constrained('inventory_warehouse_locations')->onDelete('set null');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->onDelete('set null');
            $table->foreignId('batch_id')->nullable()->constrained('inventory_batches')->onDelete('set null');
            $table->foreignId('serial_number_id')->nullable()->constrained('inventory_serial_numbers')->onDelete('set null');
            $table->string('type'); // in, out
            $table->string('source_type'); // purchase, sale, transfer, adjustment, return, manufacture
            $table->unsignedBigInteger('source_id')->nullable();
            $table->decimal('quantity', 15, 4);
            $table->string('reference')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index('company_id');
            $table->index('warehouse_id');
            $table->index('product_id');
            $table->index(['source_type', 'source_id']);
        });

        // 7. Stock Adjustments (تسويات المخزون)
        Schema::create('inventory_stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('inventory_warehouses')->onDelete('cascade');
            $table->string('adjustment_number');
            $table->date('adjustment_date');
            $table->string('status')->default('draft'); // draft, completed
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index('company_id');
            $table->index('warehouse_id');
            $table->index('adjustment_number');
            $table->index('status');
        });

        // 8. Stock Adjustment Items (عناصر التسوية)
        Schema::create('inventory_stock_adjustment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_adjustment_id', 'inv_adj_items_adj_id_foreign')->constrained('inventory_stock_adjustments')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->onDelete('set null');
            $table->foreignId('batch_id')->nullable()->constrained('inventory_batches')->onDelete('set null');
            $table->string('type'); // addition, subtraction
            $table->decimal('quantity', 15, 4);
            $table->decimal('unit_cost', 15, 4)->default(0.0000);
            $table->timestamps();

            $table->index('stock_adjustment_id', 'inv_adj_items_adj_id_idx');
            $table->index('product_id');
        });

        // 9. Stock Transfers (تحويلات المخزون)
        Schema::create('inventory_stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('from_warehouse_id')->constrained('inventory_warehouses')->onDelete('cascade');
            $table->foreignId('to_warehouse_id')->constrained('inventory_warehouses')->onDelete('cascade');
            $table->string('transfer_number');
            $table->date('transfer_date');
            $table->string('status')->default('draft'); // draft, in_transit, completed, cancelled
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index('company_id');
            $table->index('from_warehouse_id');
            $table->index('to_warehouse_id');
            $table->index('transfer_number');
            $table->index('status');
        });

        // 10. Stock Transfer Items (عناصر التحويل)
        Schema::create('inventory_stock_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_transfer_id', 'inv_trans_items_trans_id_foreign')->constrained('inventory_stock_transfers')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->onDelete('set null');
            $table->foreignId('batch_id')->nullable()->constrained('inventory_batches')->onDelete('set null');
            $table->decimal('quantity', 15, 4);
            $table->timestamps();

            $table->index('stock_transfer_id', 'inv_trans_items_trans_id_idx');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_stock_transfer_items');
        Schema::dropIfExists('inventory_stock_transfers');
        Schema::dropIfExists('inventory_stock_adjustment_items');
        Schema::dropIfExists('inventory_stock_adjustments');
        Schema::dropIfExists('inventory_stock_moves');
        Schema::dropIfExists('inventory_serial_numbers');
        Schema::dropIfExists('inventory_stock_items');
        Schema::dropIfExists('inventory_batches');
        Schema::dropIfExists('inventory_warehouse_locations');
        Schema::dropIfExists('inventory_warehouses');
    }
};
