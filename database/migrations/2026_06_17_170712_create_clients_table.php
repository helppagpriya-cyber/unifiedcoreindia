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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('client_code')->unique()->nullable();

            $table->string('company_name');
            $table->enum('client_type', [
                'government',
                'railway',
                'psu',
                'university',
                'hospital',
                'private',
                'ngo'
            ])->default('private');

            $table->string('contact_person')->nullable();

            $table->string('mobile', 20)->nullable();

            $table->string('email')->nullable();
            $table->string('website')->nullable();

            $table->text('address')->nullable();

            $table->string('gst_no')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
