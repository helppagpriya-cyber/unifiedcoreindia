<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_licenses', function (Blueprint $table) {
            $table->id();

            // Licence reference
            $table->string('license_number', 100)->nullable()->unique();

            // Customer and branch
            $table->foreignId('erp_client_id')
                ->constrained('erp_clients')
                ->restrictOnDelete();

            $table->foreignId('erp_client_branch_id')
                ->nullable()
                ->constrained('erp_client_branches')
                ->nullOnDelete();

            // Software and edition
            $table->foreignId('software_product_id')
                ->constrained('erp_software_products')
                ->restrictOnDelete();

            $table->foreignId('software_version_id')
                ->constrained('erp_software_versions')
                ->restrictOnDelete();

            // Licence classification and status
            $table->string('license_type', 30)->default('full');
            // demo, full

            $table->string('status', 30)->default('draft');
            // draft, issued, active, expired, suspended, cancelled

            // Dates
            $table->dateTime('issued_at')->nullable();
            $table->date('demo_expiry_date')->nullable();
            $table->date('installation_date')->nullable();
            $table->date('amc_expiry_date')->nullable();
            $table->dateTime('activated_at')->nullable();

            // Hardware information
            $table->string('motherboard_serial', 255)->nullable();

            // Legacy VFP licence configuration
            $table->boolean('lan_enabled')->default(false);
            $table->boolean('gst_enabled')->default(false);

            $table->unsignedSmallInteger('sales_points')->nullable();
            $table->unsignedSmallInteger('max_companies')->nullable();

            // MLOGIN = maximum simultaneous users
            $table->unsignedSmallInteger('max_simultaneous_users')->nullable();

            // VFP CRCODE
            $table->string('customer_code', 50)->nullable();

            // Generated legacy licence key
            $table->longText('license_key')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['erp_client_id', 'status']);
            $table->index(['software_product_id', 'software_version_id']);
            $table->index('amc_expiry_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_licenses');
    }
};
