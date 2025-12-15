<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Vehicle Maintenance Table
        Schema::create('vehicle_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->string('maintenance_type');
            $table->text('description')->nullable();
            $table->date('maintenance_date');
            $table->date('next_maintenance_date')->nullable();
            $table->decimal('cost', 12, 2)->default(0);
            $table->integer('odometer_reading')->nullable();
            $table->string('service_provider')->nullable();
            $table->string('invoice_no')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('completed');
            $table->timestamps();

            $table->index(['vehicle_id', 'maintenance_date']);
        });

        // Fuel Log Table
        Schema::create('fuel_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('staff')->nullOnDelete();
            $table->date('date');
            $table->string('fuel_type')->default('diesel');
            $table->decimal('quantity', 10, 2); // Liters
            $table->decimal('rate', 10, 2); // Per liter
            $table->decimal('total_cost', 12, 2);
            $table->integer('odometer_reading')->nullable();
            $table->string('fuel_station')->nullable();
            $table->string('receipt_no')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['vehicle_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_logs');
        Schema::dropIfExists('vehicle_maintenances');
    }
};
