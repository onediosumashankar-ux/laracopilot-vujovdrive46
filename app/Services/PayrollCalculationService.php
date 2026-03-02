<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Holiday;
use App\Models\Payroll;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class PayrollCalculationService
{
    /**
     * Calculate complete payroll for an employee for a given month/year.
     * Handles: working days, holidays, leaves, absences, late deductions.
     */
    public function calculate(Employee $employee, int $month, int $year): array
    {
        $tenantId = $employee->tenant_id;
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        // ── 1. Working Days (exclude weekends) ────────────────────────────
        $workingDays = $this->countWorkingDays($startDate, $endDate);

        // ── 2. Holidays in this month ─────────────────────────────────────
        $holidays = Holiday::where('tenant_id', $tenantId)
            ->where(function ($q) use ($startDate, $endDate, $month) {
                $q->whereBetween('date', [$startDate, $endDate])
                  ->orWhere(function ($q2) use ($month) {
                      // recurring holidays (same month/day any year)
                      $q2->where('recurring', true)->whereMonth('date', $month);
                  });
            })
            ->get();

        $holidayDates = $holidays->map(fn($h) => $h->date->format('Y-m-d'))->toArray();
        // Remove weekends from holiday count (already excluded)
        $holidayDays = collect($holidayDates)
            ->filter(fn($d) => !Carbon::parse($d)->isWeekend())
            ->count();

        // Effective working days after removing holidays
        $effectiveWorkingDays = $workingDays - $holidayDays;

        // ── 3. Approved Leaves ────────────────────────────────────────────
        $approvedLeaves = LeaveRequest::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($q2) use ($startDate, $endDate) {
                      $q2->where('start_date', '<=', $startDate)->where('end_date', '>=', $endDate);
                  });
            })
            ->get();

        // Count leave days within this month only
        $leaveDays = 0;
        $paidLeaveDays = 0;
        $unpaidLeaveDays = 0;
        foreach ($approvedLeaves as $leave) {
            $leaveStart = $leave->start_date->max($startDate);
            $leaveEnd = $leave->end_date->min($endDate);
            $daysInMonth = $this->countWorkingDays($leaveStart, $leaveEnd, $holidayDates);
            $leaveDays += $daysInMonth;
            if ($leave->leaveType && $leave->leaveType->paid) {
                $paidLeaveDays += $daysInMonth;
            } else {
                $unpaidLeaveDays += $daysInMonth;
            }
        }

        // ── 4. Attendance Records ─────────────────────────────────────────
        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereBetween('check_in', [$startDate, $endDate->copy()->endOfDay()])
            ->get();

        $presentDays = $attendances->where('status', 'present')->count();
        $halfDays = $attendances->where('status', 'half_day')->count();
        $lateDays = $attendances->where('is_late', true)->count();

        // ── 5. Absent Days ────────────────────────────────────────────────
        // Absent = effective working days - present - half days - leave days
        $absentDays = max(0, $effectiveWorkingDays - $presentDays - ceil($halfDays / 2) - $leaveDays);

        // ── 6. Per-Day Salary & Deductions ───────────────────────────────
        $monthlySalary = $employee->salary / 12;
        $perDaySalary = $effectiveWorkingDays > 0
            ? $monthlySalary / $effectiveWorkingDays
            : $monthlySalary / 22; // fallback

        // Unpaid leave deduction
        $unpaidLeaveDeduction = $perDaySalary * $unpaidLeaveDays;

        // Absence deduction (full deduction for unexcused absent days)
        $absenceDeduction = $perDaySalary * $absentDays;

        // Late deduction: 0.5 day salary per late instance (configurable)
        $lateDeduction = ($perDaySalary * 0.5) * $lateDays;

        // Half-day deduction
        $halfDayDeduction = ($perDaySalary * 0.5) * $halfDays;

        // ── 7. Gross & Net Calculation ────────────────────────────────────
        $basicSalary = $monthlySalary;
        $totalDeductions = $absenceDeduction + $lateDeduction + $halfDayDeduction + $unpaidLeaveDeduction;
        $grossSalary = $basicSalary; // before deductions (add allowances here)
        $netBeforeTax = $grossSalary - $totalDeductions;
        $tax = $this->calculateTax($netBeforeTax);
        $netSalary = $netBeforeTax - $tax;

        return [
            // Period
            'pay_period_start'    => $startDate->format('Y-m-d'),
            'pay_period_end'      => $endDate->format('Y-m-d'),
            // Attendance summary
            'working_days'        => $workingDays,
            'holiday_days'        => $holidayDays,
            'effective_working_days' => $effectiveWorkingDays,
            'present_days'        => $presentDays,
            'absent_days'         => $absentDays,
            'late_days'           => $lateDays,
            'half_days'           => $halfDays,
            'leave_days'          => $leaveDays,
            'paid_leave_days'     => $paidLeaveDays,
            'unpaid_leave_days'   => $unpaidLeaveDays,
            'holidays'            => $holidays,
            'approved_leaves'     => $approvedLeaves,
            // Salary breakdown
            'basic_salary'        => round($basicSalary, 2),
            'per_day_salary'      => round($perDaySalary, 2),
            'allowances'          => 0,
            'bonus'               => 0,
            'overtime_pay'        => 0,
            'gross_salary'        => round($grossSalary, 2),
            // Deductions
            'absence_deduction'   => round($absenceDeduction, 2),
            'late_deduction'      => round($lateDeduction, 2),
            'half_day_deduction'  => round($halfDayDeduction, 2),
            'unpaid_leave_deduction' => round($unpaidLeaveDeduction, 2),
            'deductions'          => round($totalDeductions, 2),
            'tax'                 => round($tax, 2),
            'net_salary'          => round($netSalary, 2),
        ];
    }

    /**
     * Count working days (Mon–Fri) in a range, excluding given holiday dates.
     */
    private function countWorkingDays(Carbon $start, Carbon $end, array $excludeDates = []): int
    {
        $count = 0;
        $period = CarbonPeriod::create($start, $end);
        foreach ($period as $date) {
            if ($date->isWeekend()) continue;
            if (in_array($date->format('Y-m-d'), $excludeDates)) continue;
            $count++;
        }
        return $count;
    }

    /**
     * Simple progressive tax calculation.
     * Adjust brackets for your jurisdiction.
     */
    private function calculateTax(float $monthlyGross): float
    {
        $annualized = $monthlyGross * 12;
        $tax = 0;

        if ($annualized <= 12000) {
            $tax = 0;
        } elseif ($annualized <= 50000) {
            $tax = ($annualized - 12000) * 0.10;
        } elseif ($annualized <= 100000) {
            $tax = (38000 * 0.10) + (($annualized - 50000) * 0.20);
        } elseif ($annualized <= 200000) {
            $tax = (38000 * 0.10) + (50000 * 0.20) + (($annualized - 100000) * 0.25);
        } else {
            $tax = (38000 * 0.10) + (50000 * 0.20) + (100000 * 0.25) + (($annualized - 200000) * 0.30);
        }

        return $tax / 12; // monthly tax
    }
}