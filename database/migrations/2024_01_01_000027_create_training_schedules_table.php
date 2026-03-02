<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Admin-defined schedule slots per training program
        Schema::create('training_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('training_program_id');
            $table->string('label');                          // e.g. "Morning Batch", "Weekend Slot A"
            $table->enum('delivery_mode', ['online', 'classroom', 'blended', 'self_paced']);
            $table->date('start_date');
            $table->date('end_date');
            $table->time('start_time');                       // e.g. 09:00
            $table->time('end_time');                         // e.g. 11:00
            $table->enum('days_of_week', [
                'monday', 'tuesday', 'wednesday', 'thursday',
                'friday', 'saturday', 'sunday', 'mon_wed_fri',
                'tue_thu', 'weekdays', 'weekends'
            ])->default('weekdays');
            $table->string('venue')->nullable();              // Room / Zoom link
            $table->string('instructor')->nullable();
            $table->integer('max_seats')->default(20);
            $table->integer('booked_seats')->default(0);
            $table->enum('status', ['open', 'full', 'cancelled', 'completed'])->default('open');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('training_program_id')
                  ->references('id')->on('training_programs')->onDelete('cascade');
        });

        // Add schedule_id + reschedule tracking to enrollments
        Schema::table('training_enrollments', function (Blueprint $table) {
            $table->unsignedBigInteger('training_schedule_id')->nullable()->after('training_program_id');
            $table->integer('reschedule_count')->default(0)->after('training_schedule_id');
            $table->unsignedBigInteger('previous_schedule_id')->nullable()->after('reschedule_count');
            $table->datetime('rescheduled_at')->nullable()->after('previous_schedule_id');
            $table->string('reschedule_reason')->nullable()->after('rescheduled_at');
            $table->enum('attendance_status', ['not_started', 'attending', 'completed', 'absent'])
                  ->default('not_started')->after('reschedule_reason');
        });
    }

    public function down()
    {
        Schema::table('training_enrollments', function (Blueprint $table) {
            $table->dropColumn([
                'training_schedule_id', 'reschedule_count',
                'previous_schedule_id', 'rescheduled_at',
                'reschedule_reason', 'attendance_status',
            ]);
        });
        Schema::dropIfExists('training_schedules');
    }
};