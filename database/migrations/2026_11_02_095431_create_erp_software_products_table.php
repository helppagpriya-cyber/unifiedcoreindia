<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_software_products', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('code')->unique();

            // Existing Visual FoxPro product identification
            $table->string('legacy_product_code')->nullable();

            // Each product can have its own software ID format.
            // We will configure the actual rules after examining examples.
            $table->json('software_id_format')->nullable();

            // Licensing compatibility profile
            $table->string('license_profile')->default('legacy_v1');

            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_software_products');
    }
};
