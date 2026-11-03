<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('erp_licenses', function (Blueprint $table) {
            $table->string('legacy_serial_number', 100)
                ->nullable()
                ->after('customer_code')
                ->comment('Original VFP QST input used for legacy licence key generation');
        });
    }

    public function down(): void
    {
        Schema::table('erp_licenses', function (Blueprint $table) {
            $table->dropColumn('legacy_serial_number');
        });
    }
};
