<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Tenant;
use Illuminate\Http\Request;

class EmployeeAttendanceController extends Controller
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
        return Employee::where('tenant_id', session('hrms_tenant_id'))->where('email', session('hrms_user_email'))->first();
    }

    public function index()
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $employee = $this->getEmployee();
        $attendances = Attendance::where('employee_id', $employee->id)->orderBy('check_in', 'desc')->paginate(20);
        $todayAttendance = Attendance::where('employee_id', $employee->id)->whereDate('check_in', today())->first();
        $tenant = Tenant::find(session('hrms_tenant_id'));
        return view('employee.attendance.index', compact('attendances', 'todayAttendance', 'tenant'));
    }

    public function checkIn(Request $request)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $employee = $this->getEmployee();
        $tenant = Tenant::find(session('hrms_tenant_id'));

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Geofence check - within 100m of office
        if ($tenant->office_lat && $tenant->office_lng) {
            $distance = $this->calculateDistance(
                $request->latitude, $request->longitude,
                $tenant->office_lat, $tenant->office_lng
            );
            if ($distance > 100) {
                return redirect()->back()->withErrors(['location' => 'You must be within 100 meters of the office to check in. Current distance: ' . round($distance) . 'm']);
            }
        }

        $existing = Attendance::where('employee_id', $employee->id)->whereDate('check_in', today())->first();
        if ($existing) {
            return redirect()->back()->withErrors(['location' => 'You have already checked in today.']);
        }

        Attendance::create([
            'tenant_id' => session('hrms_tenant_id'),
            'employee_id' => $employee->id,
            'check_in' => now(),
            'check_in_lat' => $request->latitude,
            'check_in_lng' => $request->longitude,
            'is_late' => now()->format('H:i') > '09:15',
            'status' => 'present',
        ]);

        return redirect()->back()->with('success', 'Check-in recorded successfully at ' . now()->format('h:i A'));
    }

    public function checkOut(Request $request)
    {
        if ($redirect = $this->authCheck()) return $redirect;
        $employee = $this->getEmployee();
        $attendance = Attendance::where('employee_id', $employee->id)->whereDate('check_in', today())->whereNull('check_out')->first();

        if (!$attendance) {
            return redirect()->back()->withErrors(['location' => 'No active check-in found for today.']);
        }

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $hoursWorked = $attendance->check_in->diffInMinutes(now()) / 60;

        $attendance->update([
            'check_out' => now(),
            'check_out_lat' => $request->latitude,
            'check_out_lng' => $request->longitude,
            'hours_worked' => round($hoursWorked, 2),
        ]);

        return redirect()->back()->with('success', 'Check-out recorded. Hours worked: ' . round($hoursWorked, 1) . 'h');
    }

    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371000; // meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}