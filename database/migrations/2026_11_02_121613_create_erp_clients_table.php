<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_clients', function (Blueprint $table) {
            $table->id();

            $table->string('client_code')->unique();
            $table->string('company_name');

            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('alternate_phone', 20)->nullable();

            $table->string('gst_number', 20)->nullable();
            $table->string('pan_number', 20)->nullable();

            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode', 15)->nullable();
            $table->string('country')->nullable();

            $table->text('notes')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_clients');
    }
};
