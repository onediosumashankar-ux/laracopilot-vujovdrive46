<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\TrainingProgram;
use App\Models\TrainingEnrollment;
use App\Models\WellnessSurvey;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;

class EmployeeSelfServiceController extends Controller
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

    public function dashboard()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $employee = $this->getEmployee();
        if (!$employee) return redirect()->route('login');
        $recentAttendance = Attendance::where('employee_id', $employee->id)->orderBy('check_in', 'desc')->take(5)->get();
        $pendingLeaves = LeaveRequest::where('employee_id', $employee->id)->where('status', 'pending')->count();
        $recentPayslips = Payroll::where('employee_id', $employee->id)->orderBy('created_at', 'desc')->take(3)->get();
        $todayAttendance = Attendance::where('employee_id', $employee->id)->whereDate('check_in', today())->first();
        $myTrainings = TrainingEnrollment::with('trainingProgram')->where('employee_id', $employee->id)->take(5)->get();
        return view('employee.dashboard', compact('employee', 'recentAttendance', 'pendingLeaves', 'recentPayslips', 'todayAttendance', 'myTrainings'));
    }

    public function profile()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $employee = $this->getEmployee();
        return view('employee.profile', compact('employee'));
    }

    public function updateProfile(Request $request)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $employee = $this->getEmployee();
        $validated = $request->validate([
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:255',
        ]);
        $employee->update($validated);
        return redirect()->route('employee.profile')->with('success', 'Profile updated.');
    }

    public function learning()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $employee = $this->getEmployee();
        $programs = TrainingProgram::where('tenant_id', session('hrms_tenant_id'))->where('status', 'active')->get();
        $enrolledIds = TrainingEnrollment::where('employee_id', $employee->id)->pluck('training_program_id')->toArray();
        return view('employee.learning', compact('programs', 'enrolledIds'));
    }

    public function enroll($id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $employee = $this->getEmployee();
        $already = TrainingEnrollment::where('employee_id', $employee->id)->where('training_program_id', $id)->exists();
        if (!$already) {
            TrainingEnrollment::create(['employee_id' => $employee->id, 'training_program_id' => $id, 'status' => 'enrolled']);
        }
        return redirect()->route('employee.learning')->with('success', 'Enrolled in training program.');
    }

    public function wellness()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $employee = $this->getEmployee();
        $surveys = WellnessSurvey::where('tenant_id', session('hrms_tenant_id'))->where('status', 'active')->get();
        $respondedIds = SurveyResponse::where('employee_id', $employee->id)->pluck('wellness_survey_id')->toArray();
        return view('employee.wellness', compact('surveys', 'respondedIds'));
    }

    public function submitSurvey(Request $request, $id)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $employee = $this->getEmployee();
        $already = SurveyResponse::where('employee_id', $employee->id)->where('wellness_survey_id', $id)->exists();
        if (!$already) {
            SurveyResponse::create([
                'employee_id' => $employee->id,
                'wellness_survey_id' => $id,
                'responses' => json_encode($request->except('_token')),
                'submitted_at' => now(),
            ]);
        }
        return redirect()->route('employee.wellness')->with('success', 'Survey submitted. Thank you!');
    }
}