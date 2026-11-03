<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('erp_software_versions', function (Blueprint $table) {
            $table->unsignedTinyInteger('legacy_edition_number')
                ->nullable()
                ->after('legacy_version_name');

            $table->unique(
                ['software_product_id', 'legacy_edition_number'],
                'erp_versions_product_edition_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('erp_software_versions', function (Blueprint $table) {
            $table->dropUnique('erp_versions_product_edition_unique');
            $table->dropColumn('legacy_edition_number');
        });
    }
};
