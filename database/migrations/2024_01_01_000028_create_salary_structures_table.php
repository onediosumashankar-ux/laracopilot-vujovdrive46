<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Master salary structure template (e.g. "Senior Engineer Grade A")
        Schema::create('salary_structures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('name');                          // "Senior Engineer – Grade A"
            $table->string('code')->nullable();              // "SE-GRADE-A"
            $table->text('description')->nullable();
            $table->enum('type', ['monthly', 'annual'])->default('monthly');
            $table->decimal('ctc_amount', 14, 2)->default(0); // total CTC
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        // Salary components within a structure
        Schema::create('salary_components', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('salary_structure_id');
            $table->string('name');                          // "Basic Salary"
            $table->string('code');                          // "BASIC"
            $table->enum('type', ['earning', 'deduction', 'employer_contribution'])->default('earning');
            $table->enum('calculation_type', [
                'fixed',           // fixed amount
                'percentage_basic',// % of Basic
                'percentage_ctc',  // % of CTC
                'percentage_gross',// % of Gross
                'formula'          // custom formula string
            ])->default('fixed');
            $table->decimal('value', 14, 2)->default(0);    // amount or percentage
            $table->string('formula')->nullable();           // e.g. "basic * 0.4 - 10000"
            $table->boolean('taxable')->default(true);
            $table->boolean('pf_applicable')->default(false);
            $table->boolean('esi_applicable')->default(false);
            $table->decimal('max_limit', 14, 2)->nullable(); // cap (e.g. 80C max)
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->foreign('salary_structure_id')->references('id')->on('salary_structures')->onDelete('cascade');
        });

        // Assignment of a salary structure to a specific employee
        Schema::create('employee_salary_structures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('salary_structure_id');
            $table->decimal('ctc_override', 14, 2)->nullable(); // override CTC for this employee
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->boolean('is_current')->default(true);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('salary_structure_id')->references('id')->on('salary_structures')->onDelete('cascade');
        });

        // Calculated salary breakdown snapshot per employee (per structure assignment)
        Schema::create('salary_breakdowns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_salary_structure_id');
            $table->unsignedBigInteger('salary_component_id');
            $table->string('component_name');
            $table->string('component_code');
            $table->enum('type', ['earning', 'deduction', 'employer_contribution']);
            $table->decimal('monthly_amount', 14, 2)->default(0);
            $table->decimal('annual_amount', 14, 2)->default(0);
            $table->boolean('taxable')->default(true);
            $table->timestamps();
            $table->foreign('employee_salary_structure_id')->references('id')->on('employee_salary_structures')->onDelete('cascade');
            $table->foreign('salary_component_id')->references('id')->on('salary_components')->onDelete('cascade');
        });

        // Offer letters
        Schema::create('offer_letters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('employee_id')->nullable();  // null if for candidate
            $table->unsignedBigInteger('candidate_id')->nullable();
            $table->unsignedBigInteger('employee_salary_structure_id')->nullable();
            $table->string('offer_number')->unique();
            $table->string('position');
            $table->string('department');
            $table->date('joining_date');
            $table->date('offer_expiry');
            $table->decimal('ctc_annual', 14, 2);
            $table->string('employment_type');
            $table->string('work_location')->nullable();
            $table->text('custom_clauses')->nullable();
            $table->enum('status', ['draft', 'sent', 'accepted', 'rejected', 'expired'])->default('draft');
            $table->datetime('sent_at')->nullable();
            $table->datetime('responded_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('offer_letters');
        Schema::dropIfExists('salary_breakdowns');
        Schema::dropIfExists('employee_salary_structures');
        Schema::dropIfExists('salary_components');
        Schema::dropIfExists('salary_structures');
    }
};