<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Students table এ default_discount_id যোগ - প্রতি ছাত্রের স্থায়ী ছাড়
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('default_discount_id')
                ->nullable()
                ->after('notes')
                ->constrained('fee_discounts')
                ->nullOnDelete();
        });

        // FeeStructure এ is_for_boarder যোগ - আবাসিক/অনাবাসিক আলাদা ফি
        Schema::table('fee_structures', function (Blueprint $table) {
            $table->boolean('is_for_boarder')
                ->nullable()
                ->after('is_active')
                ->comment('null=সবার জন্য, true=শুধু আবাসিক, false=শুধু অনাবাসিক');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropConstrainedForeignId('default_discount_id');
        });

        Schema::table('fee_structures', function (Blueprint $table) {
            $table->dropColumn('is_for_boarder');
        });
    }
};
