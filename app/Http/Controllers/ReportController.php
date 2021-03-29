<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\User;
use App\Models\Branch;
use App\Models\Transfer;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ReportFormRequest;

class ReportController extends Controller
{
    public function index()
    {
        // dd(auth()->user());
        // $test = collect(['a', 'b']);
        // dd($test);
        // foreach ($test as $t) {
        //     echo $t;
        // }   
        // dd();
        $from = '2021-02-25';
        $to = '2021-02-25';
        $dates = [];
        $period = CarbonPeriod::create($from, $to);

        foreach ($period as $date) {
            array_push($dates, $date->format('Y-m-d'));
        }
        // dd($dates);

        $sales = Sale::whereIn('end_of_day_at', $dates)->where('branch_id', auth()->user()->branch_id)->get();
        dd($sales);
    }

    public function create()
    {
        $this->authorize('generate reports');
        return view('report.create');
    }

    public function print(ReportFormRequest $request)
    {
        $this->authorize('generate reports');
        // dd($request->all());
        $from = $request->from;
        $to = $request->to;
        $dates = [];
        $period = CarbonPeriod::create($from, $to);

        foreach ($period as $date) {
            array_push($dates, $date->format('Y-m-d'));
        }

        if ($request->report_type == 1) {
            $reports = [];

            // foreach ($dates as $date) {
            //     $sales = Sale::where('end_of_day_at', $date)
            //         ->join('users', 'sales.approved_by', '=', 'users.id')
            //         ->where('sales.branch_id', auth()->user()->branch_id)
            //         ->get()->groupBy('name');

            //     $reports[$date] = $sales;
            // }
            foreach ($dates as $date) {
                $sales = DB::table('sales')
                    ->join('users', 'sales.approved_by', 'users.id')
                    ->join('customers', 'sales.customer_id', 'customers.id')
                    ->where('sales.branch_id', auth()->user()->branch_id)
                    ->where('end_of_day_at', $date)
                    ->select('sales.sale_number', 'sales.created_at', 'sales.status', 'sales.net_total', 'users.name AS user_name', 'customers.name AS customer_name')
                    ->get()->groupBy('user_name');

                $reports[$date] = $sales;
            }
            // dd($sales);
            return view('report.cashiersummary', compact('reports', 'from', 'to'));
        }
        elseif ($request->report_type == 2) {
            $sales = Sale::whereIn('end_of_day_at', $dates)
                ->where('branch_id', auth()->user()->branch_id)
                ->where('status', '!=', 'void')
                ->get();
            // dd($sales);
            return view('report.saledetail', compact('sales', 'from', 'to'));
        }
        elseif ($request->report_type == 3) {
            $sales = Sale::whereIn('end_of_day_at', $dates)->where('branch_id', auth()->user()->branch_id)->where('status', 'void')->get();
            // dd($sales);
            return view('report.voidsale', compact('sales', 'from', 'to'));
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

    // public function print($from = "2021-01-24", $to = "2021-02-28")
    // {
    //     $dates = [];
    //     $period = CarbonPeriod::create($from, $to);

    //     foreach ($period as $date) {
    //         array_push($dates, $date->format('Y-m-d'));
    //     }

    //     $reports = [];

    //     foreach ($dates as $date) {
    //         $sales = Sale::where('end_of_day_at', $date)->join('users', 'sales.approved_by', 'users.id')->where('sales.branch_id', auth()->user()->branch_id)->get()->groupBy('name');
    //         $reports[$date] = $sales;
    //     }

    //     return view('report.print', compact('reports'));
    // }
}
