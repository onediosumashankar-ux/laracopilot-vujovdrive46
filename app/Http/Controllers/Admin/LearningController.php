<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrainingProgram;
use App\Models\TrainingSchedule;
use App\Models\TrainingEnrollment;
use Illuminate\Http\Request;

class LearningController extends Controller
{
    private function authCheck()
    {
        if (!session('hrms_logged_in') || !in_array(session('hrms_role'), ['admin', 'hr'])) {
            return redirect()->route('login');
        }
        return null;
    }

    public function index()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $programs = TrainingProgram::where('tenant_id', session('hrms_tenant_id'))
            ->withCount('enrollments')
            ->with('schedules')
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        return view('admin.learning.index', compact('programs'));
    }

    public function create()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        return view('admin.learning.create');
    }

    public function store(Request $request)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'description'     => 'required|string',
            'category'        => 'required|in:technical,soft_skills,compliance,leadership,onboarding,other',
            'delivery_mode'   => 'required|in:online,classroom,blended,self_paced',
            'duration_hours'  => 'required|integer|min:1',
            'start_date'      => 'nullable|date',
            'end_date'        => 'nullable|date',
            'instructor'      => 'nullable|string|max:255',
            'max_participants' => 'nullable|integer|min:1',
            'status'          => 'required|in:draft,active,completed,cancelled',
        ]);
        $program = TrainingProgram::create(array_merge($validated, ['tenant_id' => session('hrms_tenant_id')]));
        return redirect()->route('admin.learning.show', $program->id)
            ->with('success', 'Training program created. Now add schedule slots.');
    }

    public function show($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $program = TrainingProgram::with(['enrollments.employee', 'enrollments.trainingSchedule', 'schedules'])
            ->where('tenant_id', session('hrms_tenant_id'))
            ->findOrFail($id);
        return view('admin.learning.show', compact('program'));
    }

    public function edit($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $program = TrainingProgram::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id);
        return view('admin.learning.edit', compact('program'));
    }

    public function update(Request $request, $id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $program = TrainingProgram::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id);
        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'description'     => 'required|string',
            'category'        => 'required|in:technical,soft_skills,compliance,leadership,onboarding,other',
            'delivery_mode'   => 'required|in:online,classroom,blended,self_paced',
            'duration_hours'  => 'required|integer|min:1',
            'start_date'      => 'nullable|date',
            'end_date'        => 'nullable|date',
            'instructor'      => 'nullable|string|max:255',
            'max_participants' => 'nullable|integer|min:1',
            'status'          => 'required|in:draft,active,completed,cancelled',
        ]);
        $program->update($validated);
        return redirect()->route('admin.learning.show', $id)->with('success', 'Program updated.');
    }

    public function destroy($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        TrainingProgram::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id)->delete();
        return redirect()->route('admin.learning.index')->with('success', 'Training program deleted.');
    }

    // ── Schedule Management ───────────────────────────────────────────────

    public function storeSchedule(Request $request, $programId)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $program = TrainingProgram::where('tenant_id', session('hrms_tenant_id'))->findOrFail($programId);

        $validated = $request->validate([
            'label'         => 'required|string|max:150',
            'delivery_mode' => 'required|in:online,classroom,blended,self_paced',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'start_time'    => 'required|date_format:H:i',
            'end_time'      => 'required|date_format:H:i|after:start_time',
            'days_of_week'  => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday,mon_wed_fri,tue_thu,weekdays,weekends',
            'venue'         => 'nullable|string|max:255',
            'instructor'    => 'nullable|string|max:255',
            'max_seats'     => 'required|integer|min:1|max:500',
            'status'        => 'required|in:open,full,cancelled',
            'notes'         => 'nullable|string',
        ]);

        TrainingSchedule::create(array_merge($validated, [
            'training_program_id' => $program->id,
            'booked_seats'        => 0,
        ]));

        return redirect()->route('admin.learning.show', $programId)
            ->with('success', 'Schedule slot added successfully.');
    }

    public function updateSchedule(Request $request, $programId, $scheduleId)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $schedule = TrainingSchedule::where('training_program_id', $programId)->findOrFail($scheduleId);

        $validated = $request->validate([
            'label'         => 'required|string|max:150',
            'delivery_mode' => 'required|in:online,classroom,blended,self_paced',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'start_time'    => 'required|date_format:H:i',
            'end_time'      => 'required|date_format:H:i',
            'days_of_week'  => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday,mon_wed_fri,tue_thu,weekdays,weekends',
            'venue'         => 'nullable|string|max:255',
            'instructor'    => 'nullable|string|max:255',
            'max_seats'     => 'required|integer|min:1|max:500',
            'status'        => 'required|in:open,full,cancelled,completed',
            'notes'         => 'nullable|string',
        ]);

        $schedule->update($validated);
        return redirect()->route('admin.learning.show', $programId)
            ->with('success', 'Schedule updated.');
    }

    public function destroySchedule($programId, $scheduleId)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $schedule = TrainingSchedule::where('training_program_id', $programId)->findOrFail($scheduleId);
        if ($schedule->booked_seats > 0) {
            return redirect()->back()->withErrors(['error' => 'Cannot delete a schedule that has enrolled employees.']);
        }
        $schedule->delete();
        return redirect()->route('admin.learning.show', $programId)->with('success', 'Schedule slot removed.');
    }

    public function enrollments($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $program = TrainingProgram::where('tenant_id', session('hrms_tenant_id'))->findOrFail($id);
        $enrollments = TrainingEnrollment::with(['employee', 'trainingSchedule', 'previousSchedule'])
            ->where('training_program_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('admin.learning.enrollments', compact('program', 'enrollments'));
    }

    public function updateEnrollmentStatus(Request $request, $enrollmentId)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $enrollment = TrainingEnrollment::findOrFail($enrollmentId);
        $enrollment->update([
            'status'            => $request->status,
            'attendance_status' => $request->attendance_status,
            'score'             => $request->score,
            'completed_at'      => $request->status === 'completed' ? now() : null,
        ]);
        return redirect()->back()->with('success', 'Enrollment updated.');
    }
}