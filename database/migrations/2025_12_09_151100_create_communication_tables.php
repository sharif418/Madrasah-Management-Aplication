<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Message Templates
        Schema::create('message_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['sms', 'email', 'both'])->default('sms');
            $table->string('subject')->nullable(); // For email
            $table->text('content');
            $table->string('category')->nullable(); // fee, attendance, notice, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Circulars
        Schema::create('circulars', function (Blueprint $table) {
            $table->id();
            $table->string('circular_no')->unique();
            $table->string('title');
            $table->text('content');
            $table->enum('target_audience', ['all', 'students', 'teachers', 'staff', 'parents'])->default('all');
            $table->date('issue_date');
            $table->date('effective_date')->nullable();
            $table->enum('priority', ['normal', 'urgent', 'important'])->default('normal');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->string('attachment')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Emergency Alerts
        Schema::create('emergency_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->enum('severity', ['info', 'warning', 'critical'])->default('warning');
            $table->enum('target', ['all', 'students', 'teachers', 'parents'])->default('all');
            $table->boolean('send_sms')->default(false);
            $table->boolean('send_email')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_alerts');
        Schema::dropIfExists('circulars');
        Schema::dropIfExists('message_templates');
    }
};
