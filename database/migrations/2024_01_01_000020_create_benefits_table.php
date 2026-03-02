<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('benefits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('name');
            $table->enum('type', ['health_insurance', 'dental', 'vision', 'life_insurance', 'retirement', 'transport', 'meal', 'housing', 'education', 'other']);
            $table->text('description')->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->enum('frequency', ['monthly', 'quarterly', 'annually', 'one_time']);
            $table->text('eligibility')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('benefits');
    }
};