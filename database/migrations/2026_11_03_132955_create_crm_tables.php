
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | CRM Lead Sources
        |--------------------------------------------------------------------------
        */
        Schema::create('crm_lead_sources', function (Blueprint $table) {
            $table->id();

            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | CRM Lead Statuses
        |--------------------------------------------------------------------------
        */
        Schema::create('crm_lead_statuses', function (Blueprint $table) {
            $table->id();

            $table->string('name')->unique();
            $table->string('slug')->unique();

            $table->string('color', 30)->default('gray');

            $table->boolean('is_final')->default(false);
            $table->boolean('is_won')->default(false);
            $table->boolean('is_lost')->default(false);

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | CRM Leads
        |--------------------------------------------------------------------------
        */
        Schema::create('crm_leads', function (Blueprint $table) {
            $table->id();

            // Automatic lead reference number
            $table->string('lead_number')->nullable()->unique();

            $table->date('lead_date')->nullable();

            // Company information
            $table->string('company_name');
            $table->string('business_type')->nullable();

            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode', 15)->nullable();
            $table->string('country')->nullable();

            $table->string('gst_number', 20)->nullable();
            $table->string('pan_number', 20)->nullable();

            // Contact information
            $table->string('contact_person')->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('alternate_mobile', 20)->nullable();
            $table->string('whatsapp_number', 20)->nullable();
            $table->string('email')->nullable();

            // Business opportunity
            $table->string('product_interested')->nullable();
            $table->string('existing_software')->nullable();
            $table->text('requirements')->nullable();
            $table->decimal('estimated_budget', 14, 2)->nullable();

            // CRM classification
            $table->foreignId('source_id')
                ->nullable()
                ->constrained('crm_lead_sources')
                ->nullOnDelete();

            $table->foreignId('status_id')
                ->nullable()
                ->constrained('crm_lead_statuses')
                ->nullOnDelete();

            $table->string('priority', 20)->default('normal');

            // Current assignment
            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Follow-up
            $table->dateTime('next_follow_up_at')->nullable();

            // Conversion into ERP client
            $table->foreignId('converted_erp_client_id')
                ->nullable()
                ->constrained('erp_clients')
                ->nullOnDelete();

            $table->dateTime('converted_at')->nullable();

            // Lost lead information
            $table->text('lost_reason')->nullable();

            // Internal notes
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Search and filtering indexes
            $table->index('company_name');
            $table->index('mobile');
            $table->index('email');
            $table->index('city');
            $table->index('lead_date');
            $table->index('priority');
            $table->index('next_follow_up_at');
            $table->index('assigned_to');
            $table->index('status_id');
            $table->index('source_id');
        });

        /*
        |--------------------------------------------------------------------------
        | CRM Lead Activities
        |--------------------------------------------------------------------------
        */
        Schema::create('crm_lead_activities', function (Blueprint $table) {
            $table->id();

            $table->foreignId('crm_lead_id')
                ->constrained('crm_leads')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // call, meeting, email, whatsapp, note, follow_up
            $table->string('activity_type', 50);

            $table->string('subject')->nullable();
            $table->text('description')->nullable();
            $table->string('outcome')->nullable();

            $table->dateTime('activity_at')->nullable();
            $table->dateTime('next_follow_up_at')->nullable();

            $table->timestamps();

            $table->index(['crm_lead_id', 'activity_at']);
            $table->index('activity_type');
        });

        /*
        |--------------------------------------------------------------------------
        | CRM Lead Assignment History
        |--------------------------------------------------------------------------
        */
        Schema::create('crm_lead_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('crm_lead_id')
                ->constrained('crm_leads')
                ->cascadeOnDelete();

            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('assigned_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('assigned_at');

            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->index(['crm_lead_id', 'assigned_at']);
        });

        /*
        |--------------------------------------------------------------------------
        | CRM Lead Import Batches
        |--------------------------------------------------------------------------
        */
        Schema::create('crm_lead_import_batches', function (Blueprint $table) {
            $table->id();

            $table->string('file_name');

            $table->foreignId('imported_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->unsignedInteger('total_rows')->default(0);
            $table->unsignedInteger('successful_rows')->default(0);
            $table->unsignedInteger('skipped_rows')->default(0);
            $table->unsignedInteger('failed_rows')->default(0);

            // pending, processing, completed, failed
            $table->string('status', 30)->default('pending');

            $table->json('summary')->nullable();

            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_lead_import_batches');
        Schema::dropIfExists('crm_lead_assignments');
        Schema::dropIfExists('crm_lead_activities');
        Schema::dropIfExists('crm_leads');
        Schema::dropIfExists('crm_lead_statuses');
        Schema::dropIfExists('crm_lead_sources');
    }
};
