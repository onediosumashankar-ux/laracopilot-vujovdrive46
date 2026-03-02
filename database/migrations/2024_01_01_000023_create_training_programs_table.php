<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('training_programs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('title');
            $table->text('description');
            $table->enum('category', ['technical', 'soft_skills', 'compliance', 'leadership', 'onboarding', 'other']);
            $table->enum('delivery_mode', ['online', 'classroom', 'blended', 'self_paced']);
            $table->integer('duration_hours');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('instructor')->nullable();
            $table->integer('max_participants')->nullable();
            $table->enum('status', ['draft', 'active', 'completed', 'cancelled'])->default('draft');
            $table->timestamps();
        });

        Schema::create('training_enrollments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('training_program_id');
            $table->enum('status', ['enrolled', 'in_progress', 'completed', 'dropped'])->default('enrolled');
            $table->datetime('completed_at')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->timestamps();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('training_program_id')->references('id')->on('training_programs')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('training_enrollments');
        Schema::dropIfExists('training_programs');
    }
};