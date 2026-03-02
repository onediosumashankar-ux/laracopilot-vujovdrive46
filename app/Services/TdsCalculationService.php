<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\TdsDeclaration;

class TdsCalculationService
{
    // ── Indian Tax Slabs FY 2024-25 ───────────────────────────────────────

    /**
     * NEW REGIME slabs (default from FY 2023-24 onwards)
     * Section 115BAC
     */
    private array $newRegimeSlabs = [
        // [upto, rate]
        [300000,   0.00],
        [600000,   0.05],
        [900000,   0.10],
        [1200000,  0.15],
        [1500000,  0.20],
        [PHP_INT_MAX, 0.30],
    ];

    /**
     * OLD REGIME slabs
     */
    private array $oldRegimeSlabs = [
        [250000,   0.00],
        [500000,   0.05],
        [1000000,  0.20],
        [PHP_INT_MAX, 0.30],
    ];

    // Senior citizen (60-80): 0% upto 3L old regime
    private array $seniorOldSlabs = [
        [300000,   0.00],
        [500000,   0.05],
        [1000000,  0.20],
        [PHP_INT_MAX, 0.30],
    ];

    // Super senior (80+): 0% upto 5L old regime
    private array $superSeniorOldSlabs = [
        [500000,   0.00],
        [1000000,  0.20],
        [PHP_INT_MAX, 0.30],
    ];

    /**
     * Full TDS computation from TdsDeclaration data.
     */
    public function computeFromDeclaration(array $data, Employee $employee): array
    {
        $regime       = $data['tax_regime'] ?? 'new';
        $isSenior     = (bool)($data['is_senior_citizen'] ?? false);
        $isSuper      = (bool)($data['is_super_senior'] ?? false);
        $annualSalary = $employee->salary; // already annual

        // ── Gross Annual Income ──────────────────────────────────────────
        $gross = $annualSalary
            + ($data['allowances_annual'] ?? 0)
            + ($data['bonus_annual'] ?? 0);

        // ── Standard Deduction ──────────────────────────────────────────
        // Rs. 50,000 standard deduction available in both regimes
        $standardDeduction = min(50000, $gross);

        // ── Exemptions (OLD REGIME ONLY) ─────────────────────────────────
        $totalExemptions = 0;
        if ($regime === 'old') {
            // HRA Exemption: minimum of 3 conditions
            $hraActual     = (float)($data['hra_actual'] ?? 0);
            $basicAnnual   = (float)($data['basic_salary_annual'] ?? ($annualSalary * 0.5));
            $rentPaid      = (float)($data['rent_paid_annual'] ?? 0);
            $metro         = (bool)($data['metro_city'] ?? false);

            if ($rentPaid > 0 && $hraActual > 0) {
                $hraCondition1 = $hraActual;
                $hraCondition2 = $metro ? ($basicAnnual * 0.50) : ($basicAnnual * 0.40);
                $hraCondition3 = max(0, $rentPaid - ($basicAnnual * 0.10));
                $hraExemption  = min($hraCondition1, $hraCondition2, $hraCondition3);
            } else {
                $hraExemption = 0;
            }

            $ltaExemption = (float)($data['lta_exemption'] ?? 0);
            $totalExemptions = $hraExemption + $ltaExemption;
        }

        // ── Deductions (OLD REGIME ONLY) ─────────────────────────────────
        $totalDeductions = 0;
        if ($regime === 'old') {
            $sec80c     = min((float)($data['section_80c'] ?? 0), 150000);
            $sec80ccd1b = min((float)($data['section_80ccd1b'] ?? 0), 50000);
            $sec80d     = min((float)($data['section_80d'] ?? 0), $isSenior ? 50000 : 25000);
            $sec80dd    = (float)($data['section_80dd'] ?? 0);
            $sec80e     = (float)($data['section_80e'] ?? 0);   // no limit
            $sec80g     = (float)($data['section_80g'] ?? 0);
            $sec80tta   = min((float)($data['section_80tta'] ?? 0), 10000);
            $sec24b     = min((float)($data['section_24b'] ?? 0), 200000);
            $otherDed   = (float)($data['other_deductions'] ?? 0);

            $totalDeductions = $sec80c + $sec80ccd1b + $sec80d + $sec80dd
                + $sec80e + $sec80g + $sec80tta + $sec24b + $otherDed;
        }

        // ── Taxable Income ───────────────────────────────────────────────
        $taxableIncome = max(0,
            $gross - $standardDeduction - $totalExemptions - $totalDeductions
        );

        // ── Rebate u/s 87A ───────────────────────────────────────────────
        // New regime: rebate up to Rs 25,000 if taxable income <= 7,00,000
        // Old regime: rebate up to Rs 12,500 if taxable income <= 5,00,000
        $rebate87A = 0;

        // ── Tax Calculation ──────────────────────────────────────────────
        if ($regime === 'new') {
            $slabs   = $this->newRegimeSlabs;
            $annualTax = $this->calculateSlab($taxableIncome, $slabs);
            if ($taxableIncome <= 700000) {
                $rebate87A = min($annualTax, 25000);
            }
        } else {
            if ($isSuper) {
                $slabs = $this->superSeniorOldSlabs;
            } elseif ($isSenior) {
                $slabs = $this->seniorOldSlabs;
            } else {
                $slabs = $this->oldRegimeSlabs;
            }
            $annualTax = $this->calculateSlab($taxableIncome, $slabs);
            if ($taxableIncome <= 500000) {
                $rebate87A = min($annualTax, 12500);
            }
        }

        $taxAfterRebate = max(0, $annualTax - $rebate87A);

        // ── Surcharge ────────────────────────────────────────────────────
        $surcharge = $this->calculateSurcharge($taxableIncome, $taxAfterRebate);

        // ── Health & Education Cess 4% ───────────────────────────────────
        $cess = ($taxAfterRebate + $surcharge) * 0.04;

        // ── Total Tax Liability ──────────────────────────────────────────
        $totalTax = $taxAfterRebate + $surcharge + $cess;

        // ── Monthly TDS ──────────────────────────────────────────────────
        $monthlyTds = round($totalTax / 12, 2);

        // ── Professional Tax (State-wise, using Maharashtra as default) ──
        $professionalTax = $this->calculateProfessionalTax($annualSalary / 12);

        // ── PF Calculation ───────────────────────────────────────────────
        // Employee PF: 12% of Basic (max 1800/month if basic > 15000)
        $monthlyBasic  = $annualSalary / 12 * 0.5; // assuming 50% of CTC is basic
        $pfEmployee    = min(round($monthlyBasic * 0.12, 2), 1800);
        $pfEmployer    = $pfEmployee; // employer matches

        // ── ESI (if gross salary <= 21000/month) ─────────────────────────
        $monthlyGross = $annualSalary / 12;
        $esiEmployee  = 0;
        $esiEmployer  = 0;
        if ($monthlyGross <= 21000) {
            $esiEmployee = round($monthlyGross * 0.0075, 2); // 0.75%
            $esiEmployer = round($monthlyGross * 0.0325, 2); // 3.25%
        }

        return [
            // Inputs
            'tax_regime'              => $regime,
            'gross_annual_income'     => round($gross, 2),
            'standard_deduction'      => $standardDeduction,
            'total_exemptions'        => round($totalExemptions, 2),
            'total_deductions'        => round($totalDeductions, 2),

            // Breakdown
            'hra_exemption_computed'  => isset($hraExemption) ? round($hraExemption, 2) : 0,
            'sec80c_applied'          => isset($sec80c) ? $sec80c : 0,
            'sec80d_applied'          => isset($sec80d) ? $sec80d : 0,
            'sec24b_applied'          => isset($sec24b) ? $sec24b : 0,

            // Tax
            'taxable_income'          => round($taxableIncome, 2),
            'annual_tax_before_rebate'=> round($annualTax, 2),
            'rebate_87a'              => round($rebate87A, 2),
            'annual_tax'              => round($taxAfterRebate, 2),
            'surcharge'               => round($surcharge, 2),
            'health_education_cess'   => round($cess, 2),
            'total_tax_liability'     => round($totalTax, 2),
            'monthly_tds'             => $monthlyTds,

            // Statutory
            'professional_tax_monthly'=> $professionalTax,
            'pf_employee_monthly'     => $pfEmployee,
            'pf_employer_monthly'     => $pfEmployer,
            'esi_employee_monthly'    => $esiEmployee,
            'esi_employer_monthly'    => $esiEmployer,

            // Total monthly deductions
            'total_monthly_statutory' => round($monthlyTds + $professionalTax + $pfEmployee + $esiEmployee, 2),
        ];
    }

    /**
     * Compute slab-based tax.
     */
    private function calculateSlab(float $income, array $slabs): float
    {
        $tax = 0;
        $prev = 0;
        foreach ($slabs as $slab) {
            [$limit, $rate] = $slab;
            if ($income <= $prev) break;
            $taxable = min($income, $limit) - $prev;
            $tax    += $taxable * $rate;
            $prev    = $limit;
            if ($income <= $limit) break;
        }
        return $tax;
    }

    /**
     * Surcharge on income tax.
     */
    private function calculateSurcharge(float $taxableIncome, float $tax): float
    {
        if ($taxableIncome > 50000000) return $tax * 0.37;  // 3.7 Cr+
        if ($taxableIncome > 20000000) return $tax * 0.25;  // 2 Cr+
        if ($taxableIncome > 10000000) return $tax * 0.15;  // 1 Cr+
        if ($taxableIncome > 5000000)  return $tax * 0.10;  // 50 L+
        return 0;
    }

    /**
     * Professional Tax – Maharashtra slabs (most common).
     * Adjust per state.
     */
    private function calculateProfessionalTax(float $monthlySalary): float
    {
        if ($monthlySalary <= 7500)  return 0;
        if ($monthlySalary <= 10000) return 175;
        return 200; // max 2400/year
    }

    /**
     * Get financial year string from month & year.
     */
    public function getFinancialYear(int $month, int $year): string
    {
        if ($month >= 4) {
            return $year . '-' . substr($year + 1, 2);
        }
        return ($year - 1) . '-' . substr($year, 2);
    }

    /**
     * Compute monthly TDS for payslip integration.
     */
    public function computeMonthlyTds(Employee $employee, int $month, int $year): array
    {
        $fy = $this->getFinancialYear($month, $year);
        $declaration = \App\Models\TdsDeclaration::where('employee_id', $employee->id)
            ->where('financial_year', $fy)
            ->first();

        if ($declaration) {
            return [
                'monthly_tds'          => $declaration->monthly_tds,
                'professional_tax'     => $this->calculateProfessionalTax($employee->salary / 12),
                'pf_employee'          => min(round(($employee->salary / 12 * 0.5) * 0.12, 2), 1800),
                'pf_employer'          => min(round(($employee->salary / 12 * 0.5) * 0.12, 2), 1800),
                'esi_employee'         => $employee->salary / 12 <= 21000 ? round(($employee->salary / 12) * 0.0075, 2) : 0,
                'esi_employer'         => $employee->salary / 12 <= 21000 ? round(($employee->salary / 12) * 0.0325, 2) : 0,
                'financial_year'       => $fy,
                'declaration_exists'   => true,
            ];
        }

        // Default: compute basic TDS with new regime, no declarations
        $basic = $this->computeFromDeclaration(['tax_regime' => 'new'], $employee);
        return [
            'monthly_tds'        => $basic['monthly_tds'],
            'professional_tax'   => $basic['professional_tax_monthly'],
            'pf_employee'        => $basic['pf_employee_monthly'],
            'pf_employer'        => $basic['pf_employer_monthly'],
            'esi_employee'       => $basic['esi_employee_monthly'],
            'esi_employer'       => $basic['esi_employer_monthly'],
            'financial_year'     => $fy,
            'declaration_exists' => false,
        ];
    }
}