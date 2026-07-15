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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_record_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('document_no')->nullable();

            $table->string('document_title');

            $table->integer('page_count')->default(0);

            $table->enum('status', [
                'pending',
                'assigned',
                'scanned',
                'qc',
                'completed'
            ])->default('pending');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
