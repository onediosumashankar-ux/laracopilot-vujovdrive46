<?php

namespace App\Services;

use App\Models\SalaryStructure;
use App\Models\SalaryComponent;
use App\Models\EmployeeSalaryStructure;
use App\Models\SalaryBreakdown;
use App\Models\Employee;

class SalaryStructureService
{
    /**
     * Compute monthly/annual breakdown for a given CTC and structure.
     * Returns array of component rows with amounts.
     */
    public function compute(SalaryStructure $structure, float $ctc): array
    {
        $components = $structure->activeComponents;
        $isAnnual   = $structure->type === 'annual';
        $annualCtc  = $isAnnual ? $ctc : $ctc * 12;

        $computed  = [];   // component_code => annual amount
        $earningsTotal = 0;
        $rows      = [];

        // First pass: compute basics (fixed + % CTC)
        foreach ($components as $comp) {
            $annual = $this->computeComponent($comp, $annualCtc, $computed);
            $computed[$comp->code] = $annual;
        }

        // Second pass: resolve formulas & percentages that depend on BASIC
        foreach ($components as $comp) {
            $annual = $this->computeComponent($comp, $annualCtc, $computed);
            if ($comp->max_limit && $annual > $comp->max_limit * 12) {
                $annual = $comp->max_limit * 12;
            }
            $computed[$comp->code] = round($annual, 2);

            $rows[] = [
                'id'               => $comp->id,
                'name'             => $comp->name,
                'code'             => $comp->code,
                'type'             => $comp->type,
                'calculation_type' => $comp->calculation_type,
                'value'            => $comp->value,
                'annual_amount'    => round($annual, 2),
                'monthly_amount'   => round($annual / 12, 2),
                'taxable'          => $comp->taxable,
                'pf_applicable'    => $comp->pf_applicable,
                'esi_applicable'   => $comp->esi_applicable,
            ];

            if ($comp->type === 'earning') {
                $earningsTotal += $annual;
            }
        }

        $earnings    = collect($rows)->where('type', 'earning');
        $deductions  = collect($rows)->where('type', 'deduction');
        $employer    = collect($rows)->where('type', 'employer_contribution');

        $grossAnnual   = $earnings->sum('annual_amount');
        $dedAnnual     = $deductions->sum('annual_amount');
        $netAnnual     = $grossAnnual - $dedAnnual;
        $taxableAnnual = $earnings->where('taxable', true)->sum('annual_amount');

        return [
            'components'          => $rows,
            'ctc_annual'          => round($annualCtc, 2),
            'ctc_monthly'         => round($annualCtc / 12, 2),
            'gross_annual'        => round($grossAnnual, 2),
            'gross_monthly'       => round($grossAnnual / 12, 2),
            'total_deductions_annual'  => round($dedAnnual, 2),
            'total_deductions_monthly' => round($dedAnnual / 12, 2),
            'net_annual'          => round($netAnnual, 2),
            'net_monthly'         => round($netAnnual / 12, 2),
            'taxable_annual'      => round($taxableAnnual, 2),
            'employer_contributions_annual' => round($employer->sum('annual_amount'), 2),
        ];
    }

    /**
     * Compute single component value.
     */
    private function computeComponent(SalaryComponent $comp, float $annualCtc, array $computed): float
    {
        return match ($comp->calculation_type) {
            'fixed'            => (float)$comp->value * 12,
            'percentage_ctc'   => $annualCtc * ((float)$comp->value / 100),
            'percentage_basic' => ($computed['BASIC'] ?? ($annualCtc * 0.5)) * ((float)$comp->value / 100),
            'percentage_gross' => ($computed['GROSS'] ?? $annualCtc) * ((float)$comp->value / 100),
            'formula'          => $this->evalFormula($comp->formula ?? '', $annualCtc, $computed),
            default            => 0,
        };
    }

    /**
     * Safe formula evaluator.
     * Variables: ctc, basic, gross, hra
     */
    private function evalFormula(string $formula, float $ctc, array $computed): float
    {
        if (empty($formula)) return 0;
        try {
            $formula = strtolower($formula);
            $vars = [
                'ctc'   => $ctc,
                'basic' => $computed['BASIC'] ?? ($ctc * 0.5),
                'hra'   => $computed['HRA'] ?? 0,
                'gross' => array_sum(array_values($computed)),
            ];
            foreach ($vars as $k => $v) {
                $formula = str_replace($k, $v, $formula);
            }
            // Only allow safe math expressions
            if (!preg_match('/^[0-9\s\.\+\-\*\/\(\)]+$/', $formula)) return 0;
            return (float) eval("return ({$formula});");
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Persist breakdown rows for an employee structure assignment.
     */
    public function saveBreakdown(EmployeeSalaryStructure $ess, array $computation): void
    {
        $ess->breakdowns()->delete();
        foreach ($computation['components'] as $row) {
            SalaryBreakdown::create([
                'employee_salary_structure_id' => $ess->id,
                'salary_component_id'          => $row['id'],
                'component_name'               => $row['name'],
                'component_code'               => $row['code'],
                'type'                         => $row['type'],
                'monthly_amount'               => $row['monthly_amount'],
                'annual_amount'                => $row['annual_amount'],
                'taxable'                      => $row['taxable'],
            ]);
        }
    }

    /**
     * Get the current salary structure assignment for an employee.
     */
    public function getCurrentAssignment(int $employeeId): ?EmployeeSalaryStructure
    {
        return EmployeeSalaryStructure::with(['salaryStructure.components', 'breakdowns'])
            ->where('employee_id', $employeeId)
            ->where('is_current', true)
            ->latest()
            ->first();
    }

    /**
     * Get breakdown values as key => monthly_amount map.
     */
    public function getBreakdownMap(EmployeeSalaryStructure $ess): array
    {
        return $ess->breakdowns->pluck('monthly_amount', 'component_code')->toArray();
    }
}