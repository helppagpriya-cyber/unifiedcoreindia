<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_license_modules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('erp_license_id')
                ->constrained('erp_licenses')
                ->cascadeOnDelete();

            $table->foreignId('erp_software_module_id')
                ->constrained('erp_software_modules')
                ->restrictOnDelete();

            $table->timestamps();

            $table->unique(
                ['erp_license_id', 'erp_software_module_id'],
                'erp_license_module_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_license_modules');
    }
};
