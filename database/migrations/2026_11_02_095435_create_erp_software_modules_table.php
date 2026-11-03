<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_software_modules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('software_product_id')
                ->constrained('erp_software_products')
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('code');

            // Original Visual FoxPro flag name, e.g. GIRVI, KITTY, SMS.
            $table->string('legacy_flag_key')->nullable();

            // Used to reproduce the existing legacy key encoding.
            $table->unsignedTinyInteger('legacy_group')->nullable();
            $table->unsignedTinyInteger('legacy_position')->nullable();

            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(
                ['software_product_id', 'code'],
                'erp_product_module_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_software_modules');
    }
};
