<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_client_branches', function (Blueprint $table) {
            $table->id();

            $table->foreignId('erp_client_id')
                ->constrained('erp_clients')
                ->cascadeOnDelete();

            $table->string('branch_code');
            $table->string('branch_name');

            $table->boolean('is_head_office')->default(false);

            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();

            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode', 15)->nullable();
            $table->string('country')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->unique(
                ['erp_client_id', 'branch_code'],
                'erp_client_branch_code_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_client_branches');
    }
};
