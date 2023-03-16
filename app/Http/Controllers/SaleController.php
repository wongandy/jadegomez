<?php

namespace App\Http\Controllers;


use App\Models\Item;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\BranchItem;
use App\Models\ItemPurchase;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\SaleFormRequest;

class SaleController extends Controller
{
    public function index()
    {
        $this->authorize('view sales');
        $today_sales = Sale::with('items', 'customer', 'user', 'approvedByUser')
            ->where('branch_id', auth()->user()->branch_id)
            ->where(function($query) {
                $query->where('updated_at', '>=', date('Y-m-d') . ' 00:00:00')
               ->orWhere('status', '=', 'for approval');
           })
            ->orderByDesc('id')->get();
            
        return view('sale.index', compact(['today_sales']));
    }

    public function create()
    {
        $this->authorize('create sales');
        DB::statement('SET SESSION group_concat_max_len = 1000000');
        $number = Sale::where('branch_id', auth()->user()->branch_id)->max('number') + 1;
        $sale_number = "DR-" . str_pad($number, 8, "0", STR_PAD_LEFT);
        $items = DB::table('items')
                    ->join('item_purchase', 'item_purchase.item_id', '=', 'items.id')
                    ->select(
                        'items.id',
                        'items.name',
                        'items.upc',
                        'items.with_serial_number',
                        'items.selling_price',
                        DB::raw('COUNT(item_purchase.item_id) AS on_hand'),
                        DB::raw("CONCAT('[\"', GROUP_CONCAT(serial_number SEPARATOR '\",\"'),'\"]') AS serial_numbers")
                    )
                    ->where('item_purchase.branch_id', auth()->user()->branch_id)
                    ->where('item_purchase.status', 'available')
                    ->groupBy('item_purchase.item_id')
                    ->get();

        return view('sale.create', compact('items', 'sale_number'));
    }

    public function store(SaleFormRequest $request)
    {
        $this->authorize('create sales');
        DB::transaction(function() use ($request) {
            if ($request->customer == 'new') {
                $customer = Customer::create([
                    'name' => $request->customer_name,
                    'contact_number' => $request->contact_number
                ]);

                $customer_id = $customer->id;
            }
            else {
                $customer_id = $request->customer_id;
            }

            $branch_id = $request->user()->branch_id;

            $sale = [];
            $f = 0;
            $details = '';

            foreach ($request->items as $item) {
                $details .= $item['quantity'] . ' x ' . $item['name'] . ' at ' . number_format($item['selling_price'], 2, '.', ',') . '<br>';

                if ($item['with_serial_number']) {
                    $itemPurchases = ItemPurchase::where('item_id', $item['item_id'])->where('branch_id', $branch_id)->whereIn('serial_number', $item['serial_number'])->where('status', 'available')->get();
                    $details .= '(' . $itemPurchases->implode('serial_number', ', ') . ')' . '<br><br>';
                }
                else {
                    $itemPurchases = ItemPurchase::where('item_id', $item['item_id'])->where('branch_id', $branch_id)->where('status', 'available')->limit($item['quantity'])->get();
                    $details .= '<br>';
                }

                foreach ($itemPurchases as $itemPurchase) {
                    $itemPurchase->update(['status' => 'for approval']);
                }

                for ($i = 0; $i < $item['quantity']; $i++) {
                    $sale[$f]['item_id'] = $item['item_id'];
                    $sale[$f]['branch_id'] = $branch_id;
                    $sale[$f]['item_purchase_id'] = $itemPurchases[$i]->id;
                    $sale[$f]['sold_price'] = $item['selling_price'];
                    $sale[$f]['created_at'] = date('Y-m-d H:i:s');
                    $sale[$f]['updated_at'] = date('Y-m-d H:i:s');
                    $f++;
                }
            }

            $number = Sale::where('branch_id', auth()->user()->branch_id)->max('number') + 1;
            $sale_number = "DR-" . str_pad($number, 8, "0", STR_PAD_LEFT);

            Sale::create([
                'customer_id' => $customer_id,
                'branch_id' => $branch_id,
                'user_id' => $request->user()->id,
                'number' => $number,
                'sale_number' => $sale_number,
                'details' => $details,
                'gross_total' => $request->gross_total,
                'discount' => $request->discount,
                'net_total' => $request->net_total
            ])->items()->attach($sale);

            // update the cost price of each items as it is dynamic
            foreach ($request->items as $items) {
                $dynamic_cost_price = DB::select("SELECT ROUND(SUM(total_cost_price) / SUM(qty), 2) AS dynamic_cost_price FROM (SELECT COUNT(item_id) as qty, (COUNT(item_id) * cost_price) AS total_cost_price FROM `item_purchase` where status = 'available' AND item_id = " . $items['item_id'] . " GROUP BY purchase_id) as T");
                $item = Item::find($items['item_id']);
                $item->dynamic_cost_price = $dynamic_cost_price[0]->dynamic_cost_price;
                $item->save();
            }

            $request->session()->flash('message', 'Create sale successful!');
        });
    }

    public function review(Sale $sale)
    {
        if (auth()->user()->branch_id != $sale->branch_id || auth()->user()->cannot('approve sales')) {
            abort(403);
        }

        $sale = Sale::with('items', 'user')->where('id', $sale->id)->first();

        if ($sale->status == 'paid' || $sale->status == 'void') {
            abort(404);
        }

        return view('sale.review', compact('sale'));
    }

    public function updateStatus(Request $request, Sale $sale)
    {
        $status = $request->status;
        $sale = Sale::find($sale->id);
        $sale->status = $status;
        $sale->cash_tendered = $request->cash_tendered;
        $sale->approved_by = $request->user()->id;
        $sale->save();

        ItemPurchase::whereIn('id', $sale->updateSaleStatusToPaid->pluck('id'))->update(['status' => $status]);
        
        // if ($request->status == 'paid' || $request->status == 'unpaid') {
        //     return redirect()->route('sale.print', $sale->id);
        // }

        // return redirect()->route('sale.index')->with('message', 'Approve sale successful!');
        return redirect()->route('sale.print', $sale->id);
    }

    public function print(Sale $sale)
    {
        if (auth()->user()->branch_id != $sale->branch_id) {
            abort(403);
        }

        $sale = Sale::with('items', 'user')->where('id', $sale->id)->first();
        return view('sale.print', compact('sale'));
    }

    public function endOfDay()
    {
        Sale::where('branch_id', '=', auth()->user()->branch_id)
            ->where('end_of_day_at', '=', NULL)
            ->update([
                'end_of_day_by' => auth()->user()->id,
                'end_of_day_at' => date("Y-m-d H:i:s")
            ]);

        return redirect()->route('sale.index');
    }

    public function void(Sale $sale)
    {
        // dd($transfer);

        // if ($sale->status == 'paid' || $sale->status == 'unpaid') {
        //     return redirect()->route('sale.index')->with('message', 'Sale ' . $sale->sale_number .' has already been approved and cannot be voided anymore!');
        // }

        $sale = Sale::where('id', $sale->id)->first();
        $sale->update([
            'status' => 'void',
        ]);

        $itemPurchaseIds = $sale->itemPurchaseId()->pluck('item_purchase_id');

        ItemPurchase::whereIn('id', $itemPurchaseIds)->update([
            'status' => 'available'
        ]);
        

        foreach ($sale->items as $items) {
            $branchItem = BranchItem::where(['branch_id' => $sale->branch_id, 'item_id' => $items->id])->first();

            if ($branchItem !== null) {
                $branchItem->increment('quantity', $items->quantity);
            }
            else {
                $branchItem = BranchItem::create([
                    'branch_id' => $sale->branch_id,
                    'item_id' => $items->id,
                    'quantity' => $items->quantity
                ]);
            }

            // update the cost price of each items as it is dynamic
            $dynamic_cost_price = DB::select("SELECT ROUND(SUM(total_cost_price) / SUM(qty), 2) AS dynamic_cost_price FROM (SELECT COUNT(item_id) as qty, (COUNT(item_id) * cost_price) AS total_cost_price FROM `item_purchase` where status = 'available' AND item_id = " . $items->id . " GROUP BY purchase_id) as T");
            $item = Item::find($items->id);
            $item->dynamic_cost_price = $dynamic_cost_price[0]->dynamic_cost_price;
            $item->save();
        }

        return redirect()->route('sale.index')->with('message', 'Sale ' . $sale->sale_number .' has been voided!');
    }

    public function getAllSales(Request $request) {
        if ($request->ajax()) {
            $sales = Sale::with('items', 'customer', 'user', 'branch', 'approvedByUser')
            ->select('sales.*')
            ->where('sales.branch_id', auth()->user()->branch_id);

            return Datatables::of($sales)
                ->addIndexColumn()
                ->editColumn('details', function($sales) {
                    return $sales->details;
                })
                ->editColumn('approvedByUser', function($sales) {
                    if ($sales->approvedByUser) {
                        return $sales->approvedByUser->name;
                    }
                    
                    return '';
                })
                ->editColumn('status', function($sales) {
                    if ($sales->status == 'void') {
                        return "<span class='badge badge-danger'>$sales->status</span>";
                    }
                    elseif ($sales->status == 'for approval' || $sales->status == 'unpaid') {
                        return "<span class='badge badge-warning'>$sales->status</span>";
                    }
                    else {
                        return "<span class='badge badge-success'>$sales->status</span>";
                    }
                })
                ->addColumn('action', function($sales){
                    $actions = '';
                    
                    if (auth()->user()->can('delete sales')) {
                        if ($sales->status != 'void') {
                            $actions .= "<form action='" . route('sale.void', $sales->id) . "' class='void_sale_form' method='POST' style='display: inline-block; margin-bottom: 4px;'>
                                            <input type='hidden' name='_token' value='" . csrf_token() . "'>
                                            <input type='hidden' name='_method' value='PUT'>
                                            <button type='submit' class='btn btn-danger'><i class='fas fa-fw fa-times'></i> Void</button>
                                        </form>";
                        }
                    }

                    if (auth()->user()->can('approve sales')) {
                        if ($sales->status == 'for approval' || $sales->status == 'unpaid') {
                            $actions .= "<a href='" . route('sale.review', $sales->id) . "' class='btn btn-info' style='margin-bottom: 4px;'><i class='fas fa-fw fa-binoculars'></i> Review</a>";
                        }
                    }
                    
                    if (auth()->user()->can('print unlimited sale DR')) {
                        if ($sales->status == 'paid' || $sales->status == 'unpaid') {
                            $actions .= "<a href='" . route('sale.print', $sales->id) . "' class='btn btn-info' style='margin-bottom: 4px;'><i class='fas fa-fw fa-print'></i> Print DR</a>";
                        }
                    }
                    
                    return $actions;
                })
                ->rawColumns(['details', 'status', 'action'])
                ->make(true);
        }
    }

    public function getAllBranchesSales($type = 'default', Request $request) {
        if ($request->ajax()) {
            $sales = Sale::with('items', 'customer', 'user', 'branch', 'approvedByUser')
            ->select('sales.*')
            ->where('status', '=', 'paid');

            return Datatables::of($sales)
                ->addIndexColumn()
                ->editColumn('details', function($sales) {
                        return $sales->details;
                    })
                    ->editColumn('approvedByUser', function($sales) {
                        if ($sales->approvedByUser) {
                            return $sales->approvedByUser->name;
                        }

                        return '';
                    })
                    ->editColumn('status', function($sales) {
                        if ($sales->status == 'void') {
                            return "<span class='badge badge-danger'>$sales->status</span>";
                        }
                        elseif ($sales->status == 'for approval' || $sales->status == 'unpaid') {
                            return "<span class='badge badge-warning'>$sales->status</span>";
                        }
                        else {
                            return "<span class='badge badge-success'>$sales->status</span>";
                        }
                    })
                    ->addColumn('action', function($sales) use ($type) {
                        $actions = '';

                        if ($type == 'refunds' && auth()->user()->can('create return item')) {
                            $actions .= "<a href='" . route('return.create', $sales->id) . "'class='btn btn-info' style='margin-bottom: 4px;'><i class='fas fa-fw fa-arrow-down'></i> Return Items</a>";
                        }
                        else if ($type == 'defectives' && auth()->user()->can('create defective item')) {
                            $actions .= "<a href='" . route('defective.create', $sales->id) . "'class='btn btn-info' style='margin-bottom: 4px;'><i class='fas fa-fw fa-arrow-down'></i> Defective Items</a>";
                        }
                        else if ($type == 'changes' && auth()->user()->can('create change item')) {
                            $actions .= "<a href='" . route('change.create', $sales->id) . "'class='btn btn-info' style='margin-bottom: 4px;'><i class='fas fa-fw fa-arrow-down'></i> Change Items</a>";
                        }

                        return $actions;
                    })
                    ->rawColumns(['details', 'status', 'action'])
                    ->make(true);
        }
    }
}
