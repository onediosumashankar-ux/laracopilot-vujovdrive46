<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // TDS Configuration per employee
        Schema::create('tds_declarations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('financial_year'); // e.g. 2024-25
            $table->enum('tax_regime', ['old', 'new'])->default('new');
            $table->enum('residential_status', ['resident', 'non_resident'])->default('resident');
            $table->boolean('is_senior_citizen')->default(false);  // 60-80 years
            $table->boolean('is_super_senior')->default(false);    // 80+ years

            // Old Regime Exemptions / Deductions
            $table->decimal('hra_exemption', 12, 2)->default(0);      // House Rent Allowance
            $table->decimal('lta_exemption', 12, 2)->default(0);      // Leave Travel Allowance
            $table->decimal('section_80c', 12, 2)->default(0);        // PF, LIC, ELSS etc. max 150000
            $table->decimal('section_80ccd1b', 12, 2)->default(0);    // NPS additional max 50000
            $table->decimal('section_80d', 12, 2)->default(0);        // Health Insurance max 25000
            $table->decimal('section_80dd', 12, 2)->default(0);       // Disabled dependent
            $table->decimal('section_80e', 12, 2)->default(0);        // Education loan interest
            $table->decimal('section_80g', 12, 2)->default(0);        // Donations
            $table->decimal('section_80tta', 12, 2)->default(0);      // Savings interest max 10000
            $table->decimal('section_24b', 12, 2)->default(0);        // Home loan interest max 200000
            $table->decimal('other_deductions', 12, 2)->default(0);

            // Allowances
            $table->decimal('hra_actual', 12, 2)->default(0);         // Actual HRA received
            $table->decimal('basic_salary_annual', 12, 2)->default(0);
            $table->decimal('rent_paid_annual', 12, 2)->default(0);
            $table->boolean('metro_city')->default(false);

            // Calculated fields
            $table->decimal('gross_annual_income', 12, 2)->default(0);
            $table->decimal('total_exemptions', 12, 2)->default(0);
            $table->decimal('total_deductions', 12, 2)->default(0);
            $table->decimal('taxable_income', 12, 2)->default(0);
            $table->decimal('annual_tax', 12, 2)->default(0);
            $table->decimal('surcharge', 12, 2)->default(0);
            $table->decimal('health_education_cess', 12, 2)->default(0);
            $table->decimal('total_tax_liability', 12, 2)->default(0);
            $table->decimal('monthly_tds', 12, 2)->default(0);
            $table->decimal('tds_already_deducted', 12, 2)->default(0);

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->unique(['employee_id', 'financial_year']);
        });

        // Monthly TDS deduction records
        Schema::create('tds_deductions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('payroll_id')->nullable();
            $table->string('financial_year');
            $table->integer('month');
            $table->integer('year');
            $table->decimal('gross_salary', 12, 2)->default(0);
            $table->decimal('taxable_income_monthly', 12, 2)->default(0);
            $table->decimal('tds_amount', 12, 2)->default(0);
            $table->decimal('surcharge', 12, 2)->default(0);
            $table->decimal('cess', 12, 2)->default(0);
            $table->decimal('total_tds', 12, 2)->default(0);
            $table->enum('status', ['pending', 'deducted', 'deposited'])->default('pending');
            $table->date('deduction_date')->nullable();
            $table->string('challan_number')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('payroll_id')->references('id')->on('payrolls')->onDelete('set null');
        });

        // Add TDS columns to payrolls
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('tds_amount', 12, 2)->default(0)->after('tax');
            $table->decimal('professional_tax', 12, 2)->default(0)->after('tds_amount');
            $table->decimal('pf_employee', 12, 2)->default(0)->after('professional_tax');
            $table->decimal('pf_employer', 12, 2)->default(0)->after('pf_employee');
            $table->decimal('esi_employee', 12, 2)->default(0)->after('pf_employer');
            $table->decimal('esi_employer', 12, 2)->default(0)->after('esi_employee');
            $table->string('financial_year')->nullable()->after('esi_employer');
            $table->string('pan_number')->nullable()->after('financial_year');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tds_deductions');
        Schema::dropIfExists('tds_declarations');
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn(['tds_amount','professional_tax','pf_employee','pf_employer','esi_employee','esi_employer','financial_year','pan_number']);
        });
    }
};