<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Holiday;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class PayrollCalculationService
{
    public function calculate(Employee $employee, int $month, int $year): array
    {
        $tenantId  = $employee->tenant_id;
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate   = Carbon::create($year, $month, 1)->endOfMonth();

        // Working days
        $workingDays = $this->countWorkingDays($startDate, $endDate);

        // Holidays
        $holidays = Holiday::where('tenant_id', $tenantId)
            ->where(function ($q) use ($startDate, $endDate, $month) {
                $q->whereBetween('date', [$startDate, $endDate])
                  ->orWhere(function ($q2) use ($month) {
                      $q2->where('recurring', true)->whereMonth('date', $month);
                  });
            })->get();

        $holidayDates = $holidays->map(fn($h) => $h->date->format('Y-m-d'))->toArray();
        $holidayDays  = collect($holidayDates)
            ->filter(fn($d) => !Carbon::parse($d)->isWeekend())
            ->count();

        $effectiveWorkingDays = $workingDays - $holidayDays;

        // Approved Leaves
        $approvedLeaves = LeaveRequest::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(fn($q2) => $q2->where('start_date', '<=', $startDate)->where('end_date', '>=', $endDate));
            })->get();

        $leaveDays = 0; $paidLeaveDays = 0; $unpaidLeaveDays = 0;
        foreach ($approvedLeaves as $leave) {
            $ls  = $leave->start_date->max($startDate);
            $le  = $leave->end_date->min($endDate);
            $dim = $this->countWorkingDays($ls, $le, $holidayDates);
            $leaveDays += $dim;
            if ($leave->leaveType && $leave->leaveType->paid) {
                $paidLeaveDays += $dim;
            } else {
                $unpaidLeaveDays += $dim;
            }
        }

        // Attendance
        $attendances  = Attendance::where('employee_id', $employee->id)
            ->whereBetween('check_in', [$startDate, $endDate->copy()->endOfDay()])
            ->get();

        $presentDays = $attendances->where('status', 'present')->count();
        $halfDays    = $attendances->where('status', 'half_day')->count();
        $lateDays    = $attendances->where('is_late', true)->count();
        $absentDays  = max(0, $effectiveWorkingDays - $presentDays - ceil($halfDays / 2) - $leaveDays);

        // Salary
        $monthlySalary = $employee->salary / 12;
        $perDaySalary  = $effectiveWorkingDays > 0 ? $monthlySalary / $effectiveWorkingDays : $monthlySalary / 22;

        $absenceDeduction     = $perDaySalary * $absentDays;
        $lateDeduction        = ($perDaySalary * 0.5) * $lateDays;
        $halfDayDeduction     = ($perDaySalary * 0.5) * $halfDays;
        $unpaidLeaveDeduction = $perDaySalary * $unpaidLeaveDays;

        // ── TDS & Statutory via TdsCalculationService ─────────────────────
        $tdsService = new TdsCalculationService();
        $tds = $tdsService->computeMonthlyTds($employee, $month, $year);

        $basicSalary    = $monthlySalary;
        $totalDeductions = $absenceDeduction + $lateDeduction + $halfDayDeduction
            + $unpaidLeaveDeduction
            + $tds['monthly_tds']
            + $tds['professional_tax']
            + $tds['pf_employee']
            + $tds['esi_employee'];

        $grossSalary = $basicSalary;
        $netSalary   = max(0, $grossSalary - $totalDeductions);

        return [
            'pay_period_start'        => $startDate->format('Y-m-d'),
            'pay_period_end'          => $endDate->format('Y-m-d'),
            'working_days'            => $workingDays,
            'holiday_days'            => $holidayDays,
            'effective_working_days'  => $effectiveWorkingDays,
            'present_days'            => $presentDays,
            'absent_days'             => $absentDays,
            'late_days'               => $lateDays,
            'half_days'               => $halfDays,
            'leave_days'              => $leaveDays,
            'paid_leave_days'         => $paidLeaveDays,
            'unpaid_leave_days'       => $unpaidLeaveDays,
            'holidays'                => $holidays,
            'approved_leaves'         => $approvedLeaves,
            'basic_salary'            => round($basicSalary, 2),
            'per_day_salary'          => round($perDaySalary, 2),
            'allowances'              => 0,
            'bonus'                   => 0,
            'overtime_pay'            => 0,
            'gross_salary'            => round($grossSalary, 2),
            'absence_deduction'       => round($absenceDeduction, 2),
            'late_deduction'          => round($lateDeduction, 2),
            'half_day_deduction'      => round($halfDayDeduction, 2),
            'unpaid_leave_deduction'  => round($unpaidLeaveDeduction, 2),
            // TDS & Statutory
            'tds_amount'              => $tds['monthly_tds'],
            'professional_tax'        => $tds['professional_tax'],
            'pf_employee'             => $tds['pf_employee'],
            'pf_employer'             => $tds['pf_employer'],
            'esi_employee'            => $tds['esi_employee'],
            'esi_employer'            => $tds['esi_employer'],
            'financial_year'          => $tds['financial_year'],
            'tds_declaration_exists'  => $tds['declaration_exists'],
            // Totals
            'tax'                     => $tds['monthly_tds'], // backward compat
            'deductions'              => round($totalDeductions, 2),
            'net_salary'              => round($netSalary, 2),
        ];
    }

    private function countWorkingDays(Carbon $start, Carbon $end, array $excludeDates = []): int
    {
        $count  = 0;
        $period = CarbonPeriod::create($start, $end);
        foreach ($period as $date) {
            if ($date->isWeekend()) continue;
            if (in_array($date->format('Y-m-d'), $excludeDates)) continue;
            $count++;
        }
        return $count;
    }
}