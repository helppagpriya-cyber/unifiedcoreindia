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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('client_branch_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('project_code')->unique()->nullable();
            $table->string('project_name');

            $table->text('description')->nullable();

            $table->string('tender_no')->nullable();
            $table->string('loa_no')->nullable();

            $table->string('work_order_no')->nullable();
            $table->date('work_order_date')->nullable();

            $table->string('agreement_no')->nullable();

            $table->date('start_date')->nullable();

            $table->date('expected_completion_date')->nullable();

            $table->date('actual_completion_date')->nullable();

            $table->decimal('contract_value', 15, 2)->nullable();

            $table->enum('billing_type', [
                'per_page',
                'per_file',
                'fixed_cost',
                'manpower'
            ])->default('per_page');

            $table->bigInteger('estimated_boxes')->default(0);
            $table->bigInteger('estimated_files')->default(0);
            $table->bigInteger('estimated_documents')->default(0);
            $table->bigInteger('estimated_pages')->default(0);

            $table->enum('status', [
                'draft',
                'awarded',
                'running',
                'hold',
                'completed',
                'closed',
                'cancelled'
            ])->default('draft');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
