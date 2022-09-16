<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\User;
use App\Models\Branch;
use App\Models\Refund;
use App\Models\Transfer;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ReportFormRequest;

class ReportController extends Controller
{
    public function create()
    {
        $this->authorize('generate reports');
        return view('report.create');
    }

    public function print(ReportFormRequest $request)
    {
        $this->authorize('generate reports');
        $from = $request->from;
        $to = $request->to;
        $dates = [];
        $period = CarbonPeriod::create($from, $to);
        foreach ($period as $date) {
            array_push($dates, $date->format('Y-m-d'));
        }

        // cashier summary report
        if ($request->report_type == 1) {
            $reports = [];

            foreach ($dates as $date) {
                $sales = Sale::join('users', 'sales.approved_by', 'users.id')
                    ->join('customers', 'sales.customer_id', 'customers.id')
                    ->where('sales.branch_id', auth()->user()->branch_id)
                    ->where('end_of_day_at', 'LIKE', $date . '%')
                    ->select(
                        'sales.sale_number',
                        'sales.created_at',
                        'sales.status',
                        'sales.net_total',
                        'users.name AS user_name',
                        'customers.name AS customer_name'
                    )->get()->groupBy('user_name');

                $refunds = Refund::query()
                    ->join('users', 'refunds.user_id', 'users.id')
                    ->join('sales', 'refunds.sale_id', 'sales.id')
                    ->join('customers', 'sales.customer_id', 'customers.id')
                    ->where('refunds.branch_id', auth()->user()->branch_id)
                    ->where('refunds.created_at', 'LIKE', $date . '%')
                    ->select(
                        DB::raw("CONCAT(refunds.refund_number, ' (', sales.sale_number, ')') AS sale_number"),
                        'refunds.created_at',
                        'refunds.status',
                        'refunds.refund_total_for_reports AS net_total',
                        'users.name AS user_name',
                        'customers.name AS customer_name'
                    )->get()->groupBy('user_name');

                $results = collect();

                $sales->each(function ($item, $name) use ($refunds, $results) {
                    $push = $refunds->has($name) ? $item->mergeRecursive($refunds->get($name)) : $item;
                    $results[$name] = $push;
                });

                $merged = $results->union($refunds);
                $reports[$date] = $merged;
            }
            
            return view('report.cashiersummary', compact('reports', 'from', 'to'));
        }
        elseif ($request->report_type == 2) { // sales details report
            $reports = [];

            foreach ($dates as $date) {
                $sales = Sale::where('end_of_day_at', 'LIKE', $date . '%')
                   ->where('branch_id', auth()->user()->branch_id)
                    ->where('status', '!=', 'void')
                    ->get();

                $reports[$date] = $sales;
            }

            return view('report.saledetail', compact('reports', 'from', 'to'));
        }
        elseif ($request->report_type == 3) { // void sales summary report
            $reports = [];

            foreach ($dates as $date) {
                $sales = Sale::where('end_of_day_at', 'LIKE', $date . '%')
                    ->where('branch_id', auth()->user()->branch_id)
                    ->where('status', 'void')
                    ->get();
                
                $reports[$date] = $sales;
            }

            return view('report.voidsale', compact('reports', 'from', 'to'));
        }
        elseif ($request->report_type == 4) {
            foreach ($dates as $date) {
                $transfers = Transfer::whereDate('transfers.created_at', $date)
                    ->join('branches', 'receiving_branch_id', 'branches.id')
                    ->where('sending_branch_id', auth()->user()->branch_id)
                    ->get()->keyBy('address');
                // dd($transfers);
                $reports[$date] = $transfers;
            }
            // dd($reports);
            // dd($reports["2021-03-06"]["Mandaue City"]->first()->items);
            return view('report.transferdetail', compact('reports', 'from', 'to'));
        }
    }
}
