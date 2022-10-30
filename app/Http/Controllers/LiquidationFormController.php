<?php

namespace App\Http\Controllers;

use App\Models\SaleRevenue;
use Illuminate\Http\Request;
use App\Models\CashDenomination;
use App\Services\SaleRevenueService;
use Yajra\Datatables\Datatables;

class LiquidationFormController extends Controller
{
    public function index()
    {
        return view('liquidation-form.index');
    }

    public function create()
    {
        $this->authorize('create liquidation forms');

        return view('liquidation-form.create', [
            'cashDenominations' => CashDenomination::select('id', 'name', 'number')->get()
        ]);
    }

    public function store(Request $request, SaleRevenueService $saleRevenueService)
    {
        $this->authorize('create liquidation forms');
        
        $saleRevenueService->store($request->checks, $request->expenses, $request->cashes, $request->notes);

        return redirect()->route('liquidation.index')->with('message', 'Created liquidation form!');
    }

    public function getAllRecords(Request $request)
    {
        if ($request->ajax()) {
            $saleRevenue = SaleRevenue::with('user')
                ->select('*')
                ->where(function ($query) {
                    $query->where('sale_revenues.branch_id', auth()->user()->branch_id);
                });

            return Datatables::of($saleRevenue)
                ->addIndexColumn()
                ->make(true);
        }
    }
}
