<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('employee_code')
                ->nullable()
                ->unique()
                ->after('id');

            $table->string('mobile')
                ->nullable()
                ->after('email');

            $table->string('designation')
                ->nullable()
                ->after('mobile');

            $table->date('joining_date')
                ->nullable()
                ->after('designation');

            $table->boolean('is_active')
                ->default(true)
                ->after('joining_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'employee_code',
                'mobile',
                'designation',
                'joining_date',
                'is_active'
            ]);
        });
    }
};
