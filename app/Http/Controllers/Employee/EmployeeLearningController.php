<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\TrainingProgram;
use App\Models\TrainingSchedule;
use App\Models\TrainingEnrollment;
use Illuminate\Http\Request;

class EmployeeLearningController extends Controller
{
    private function authCheck()
    {
        if (!session('hrms_logged_in') || session('hrms_role') !== 'employee') {
            return redirect()->route('login');
        }
        return null;
    }

    private function getEmployee()
    {
        return Employee::where('tenant_id', session('hrms_tenant_id'))
            ->where('email', session('hrms_user_email'))
            ->first();
    }

    public function index()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $employee = $this->getEmployee();

        $programs = TrainingProgram::where('tenant_id', session('hrms_tenant_id'))
            ->where('status', 'active')
            ->with('openSchedules')
            ->withCount('schedules')
            ->get();

        $myEnrollments = TrainingEnrollment::with(['trainingProgram', 'trainingSchedule'])
            ->where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $enrolledProgramIds = $myEnrollments->pluck('training_program_id')->toArray();

        return view('employee.learning.index', compact('programs', 'myEnrollments', 'enrolledProgramIds'));
    }

    /**
     * Show available schedule slots for a program before enrolling.
     */
    public function showSchedules($programId)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $employee = $this->getEmployee();

        $program = TrainingProgram::where('tenant_id', session('hrms_tenant_id'))
            ->where('status', 'active')
            ->with('schedules')
            ->findOrFail($programId);

        $myEnrollment = TrainingEnrollment::where('employee_id', $employee->id)
            ->where('training_program_id', $programId)
            ->with('trainingSchedule')
            ->first();

        return view('employee.learning.schedules', compact('program', 'myEnrollment'));
    }

    /**
     * Enroll into a specific schedule slot.
     */
    public function enroll(Request $request, $programId)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $employee = $this->getEmployee();

        $request->validate([
            'training_schedule_id' => 'required|exists:training_schedules,id',
        ]);

        $schedule = TrainingSchedule::where('training_program_id', $programId)
            ->where('status', 'open')
            ->findOrFail($request->training_schedule_id);

        if ($schedule->is_full) {
            return redirect()->back()->withErrors(['error' => 'This schedule slot is full. Please choose another.']);
        }

        // Check already enrolled
        $existing = TrainingEnrollment::where('employee_id', $employee->id)
            ->where('training_program_id', $programId)
            ->first();

        if ($existing) {
            return redirect()->back()->withErrors(['error' => 'You are already enrolled in this program.']);
        }

        TrainingEnrollment::create([
            'employee_id'          => $employee->id,
            'training_program_id'  => $programId,
            'training_schedule_id' => $schedule->id,
            'status'               => 'enrolled',
            'attendance_status'    => 'not_started',
            'reschedule_count'     => 0,
        ]);

        // Increment booked seats
        $schedule->increment('booked_seats');
        if ($schedule->booked_seats >= $schedule->max_seats) {
            $schedule->update(['status' => 'full']);
        }

        return redirect()->route('employee.learning.my')
            ->with('success', 'Successfully enrolled in "' . $schedule->label . '". See you there!');
    }

    /**
     * My enrollments page.
     */
    public function myEnrollments()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $employee = $this->getEmployee();

        $enrollments = TrainingEnrollment::with([
            'trainingProgram',
            'trainingSchedule',
            'previousSchedule',
        ])
            ->where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('employee.learning.my-enrollments', compact('enrollments'));
    }

    /**
     * Show reschedule options for an existing enrollment.
     */
    public function showReschedule($enrollmentId)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $employee   = $this->getEmployee();
        $enrollment = TrainingEnrollment::with(['trainingProgram.schedules', 'trainingSchedule'])
            ->where('employee_id', $employee->id)
            ->findOrFail($enrollmentId);

        if (in_array($enrollment->status, ['completed', 'dropped'])) {
            return redirect()->route('employee.learning.my')
                ->withErrors(['error' => 'Cannot reschedule a completed or dropped enrollment.']);
        }

        // Available schedules excluding current one
        $availableSchedules = TrainingSchedule::where('training_program_id', $enrollment->training_program_id)
            ->where('id', '!=', $enrollment->training_schedule_id)
            ->where('status', 'open')
            ->get();

        return view('employee.learning.reschedule', compact('enrollment', 'availableSchedules'));
    }

    /**
     * Process the reschedule request.
     */
    public function reschedule(Request $request, $enrollmentId)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $employee   = $this->getEmployee();
        $enrollment = TrainingEnrollment::where('employee_id', $employee->id)->findOrFail($enrollmentId);

        $request->validate([
            'training_schedule_id' => 'required|exists:training_schedules,id',
            'reschedule_reason'    => 'required|string|max:500',
        ]);

        $newSchedule = TrainingSchedule::where('training_program_id', $enrollment->training_program_id)
            ->where('status', 'open')
            ->findOrFail($request->training_schedule_id);

        if ($newSchedule->is_full) {
            return redirect()->back()->withErrors(['error' => 'Selected slot is full. Please choose another.']);
        }

        $oldScheduleId = $enrollment->training_schedule_id;

        // Free up old slot
        if ($oldScheduleId) {
            $oldSchedule = TrainingSchedule::find($oldScheduleId);
            if ($oldSchedule) {
                $oldSchedule->decrement('booked_seats');
                if ($oldSchedule->status === 'full') {
                    $oldSchedule->update(['status' => 'open']);
                }
            }
        }

        // Book new slot
        $newSchedule->increment('booked_seats');
        if ($newSchedule->booked_seats >= $newSchedule->max_seats) {
            $newSchedule->update(['status' => 'full']);
        }

        $enrollment->update([
            'training_schedule_id' => $newSchedule->id,
            'previous_schedule_id' => $oldScheduleId,
            'reschedule_count'     => $enrollment->reschedule_count + 1,
            'rescheduled_at'       => now(),
            'reschedule_reason'    => $request->reschedule_reason,
        ]);

        return redirect()->route('employee.learning.my')
            ->with('success', 'Schedule changed to "' . $newSchedule->label . '". Good luck!');
    }

    /**
     * Cancel (drop) enrollment.
     */
    public function cancelEnrollment($enrollmentId)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $employee   = $this->getEmployee();
        $enrollment = TrainingEnrollment::where('employee_id', $employee->id)->findOrFail($enrollmentId);

        if ($enrollment->status === 'completed') {
            return redirect()->back()->withErrors(['error' => 'Cannot cancel a completed training.']);
        }

        // Free up seat
        if ($enrollment->training_schedule_id) {
            $schedule = TrainingSchedule::find($enrollment->training_schedule_id);
            if ($schedule) {
                $schedule->decrement('booked_seats');
                if ($schedule->status === 'full') {
                    $schedule->update(['status' => 'open']);
                }
            }
        }

        $enrollment->update(['status' => 'dropped']);

        return redirect()->route('employee.learning.my')
            ->with('success', 'Enrollment cancelled. You can re-enroll anytime.');
    }
}