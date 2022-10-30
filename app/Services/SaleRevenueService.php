<?php

namespace App\Services;

use App\Models\SaleRevenue;
use Illuminate\Support\Facades\DB;

class SaleRevenueService
{
    public function store(?array $checks, ?array $expenses, ?array $cashes, ?string $notes): void
    {
        DB::transaction(function () use ($checks, $expenses, $cashes, $notes) {
            $saleRevenue = SaleRevenue::create([
                'user_id' => auth()->user()->id,
                'branch_id' => auth()->user()->branch_id,
                'notes' => $notes,
            ]);

            if ($checks) {
                $saleRevenue->saleRevenueChecks()->createMany($checks);
            }

            if ($expenses) {
                $saleRevenue->saleRevenueExpenses()->createMany($expenses);
            }
            
            if ($cashes) {
                $saleRevenueCashes = collect($cashes)->filter(function ($value, $key) {
                    return $value['pieces'] > 0;
                });

                if ($saleRevenueCashes->isNotEmpty()) {
                    $saleRevenue->saleRevenueCashes()->createMany($saleRevenueCashes);
                }
            }
        });
    }
}