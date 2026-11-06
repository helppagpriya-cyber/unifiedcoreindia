<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('erp_software_versions', function (Blueprint $table) {
            $table->string('package_path')->nullable();
            $table->string('package_original_name')->nullable();
        });

        Schema::table('erp_software_modules', function (Blueprint $table) {
            $table->string('package_path')->nullable();
            $table->string('package_original_name')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('erp_software_modules', function (Blueprint $table) {
            $table->dropColumn(['package_path', 'package_original_name']);
        });

        Schema::table('erp_software_versions', function (Blueprint $table) {
            $table->dropColumn(['package_path', 'package_original_name']);
        });
    }
};
