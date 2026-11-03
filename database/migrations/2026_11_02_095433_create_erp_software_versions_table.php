<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_software_versions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('software_product_id')
                ->constrained('erp_software_products')
                ->cascadeOnDelete();

            $table->string('version_name');

            // Example: 01 No, 02 No, etc.
            $table->string('legacy_version_name')->nullable();

            // Existing VFP version encoding: O, P, Q, L, etc.
            $table->char('legacy_version_code', 1)->nullable();

            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(
                ['software_product_id', 'version_name'],
                'erp_product_version_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_software_versions');
    }
};
