<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('name');
            $table->date('date');
            $table->enum('type', ['public', 'company', 'optional'])->default('public');
            $table->text('description')->nullable();
            $table->boolean('recurring')->default(false); // repeat every year
            $table->timestamps();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        // Add payroll calculation columns
        Schema::table('payrolls', function (Blueprint $table) {
            $table->integer('working_days')->default(0)->after('pay_period_end');
            $table->integer('present_days')->default(0)->after('working_days');
            $table->integer('absent_days')->default(0)->after('present_days');
            $table->integer('late_days')->default(0)->after('absent_days');
            $table->integer('holiday_days')->default(0)->after('late_days');
            $table->integer('leave_days')->default(0)->after('holiday_days');
            $table->integer('half_days')->default(0)->after('leave_days');
            $table->decimal('per_day_salary', 12, 2)->default(0)->after('half_days');
            $table->decimal('absence_deduction', 12, 2)->default(0)->after('per_day_salary');
            $table->decimal('late_deduction', 12, 2)->default(0)->after('absence_deduction');
            $table->decimal('leave_encashment', 12, 2)->default(0)->after('late_deduction');
        });
    }

    public function down()
    {
        Schema::dropIfExists('holidays');
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn(['working_days','present_days','absent_days','late_days','holiday_days','leave_days','half_days','per_day_salary','absence_deduction','late_deduction','leave_encashment']);
        });
    }
};