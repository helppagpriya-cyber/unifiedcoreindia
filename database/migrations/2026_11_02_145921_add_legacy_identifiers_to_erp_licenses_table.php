<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('erp_licenses', function (Blueprint $table) {
            $table->unsignedSmallInteger('legacy_demo_id')
                ->nullable()
                ->unique()
                ->after('legacy_serial_number');

            $table->string('legacy_software_id', 6)
                ->nullable()
                ->unique()
                ->after('legacy_demo_id');
        });
    }

    public function down(): void
    {
        Schema::table('erp_licenses', function (Blueprint $table) {
            $table->dropUnique(['legacy_demo_id']);
            $table->dropUnique(['legacy_software_id']);

            $table->dropColumn([
                'legacy_demo_id',
                'legacy_software_id',
            ]);
        });
    }
};
