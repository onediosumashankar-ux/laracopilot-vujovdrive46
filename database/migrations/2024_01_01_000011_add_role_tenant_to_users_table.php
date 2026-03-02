<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['superadmin', 'admin', 'hr', 'employee'])->default('employee')->after('email');
            $table->unsignedBigInteger('tenant_id')->nullable()->after('role');
            $table->unsignedBigInteger('employee_id')->nullable()->after('tenant_id');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'tenant_id', 'employee_id']);
        });
    }
};