<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_license_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('erp_license_id')
                ->constrained('erp_licenses')
                ->cascadeOnDelete();

            $table->foreignId('performed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Operation performed
            $table->string('operation_type', 50);

            // Previous and updated licence configuration
            $table->json('previous_configuration')->nullable();
            $table->json('new_configuration')->nullable();

            // Preserve legacy key changes
            $table->longText('previous_license_key')->nullable();
            $table->longText('new_license_key')->nullable();

            // Preserve important date changes
            $table->date('previous_installation_date')->nullable();
            $table->date('new_installation_date')->nullable();

            $table->date('previous_amc_expiry_date')->nullable();
            $table->date('new_amc_expiry_date')->nullable();

            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->index(['erp_license_id', 'operation_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_license_histories');
    }
};
