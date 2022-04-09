<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\BranchItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\PurchaseFormRequest;
use Yajra\Datatables\Datatables;

class PurchaseController extends Controller
{
    public function index()
    {
        $this->authorize('view purchases');
        return view('purchase.index');
    }

    public function create(Supplier $supplier)
    {
        $this->authorize('create purchases');
        $number = Purchase::where('branch_id', auth()->user()->branch_id)->max('number') + 1;
        $purchase_number = "PO-" . str_pad($number, 8, "0", STR_PAD_LEFT);
        $items = Item::with(['purchases' => function ($query) use ($supplier) {
            return $query->select('purchases.id', 'supplier_id')->where('supplier_id', $supplier->id)->where('purchases.status', '!=', 'void')->where('purchases.branch_id', auth()->user()->branch_id);
        }])->get();
        
        return view('purchase.create', compact('items', 'supplier', 'purchase_number'));
    }
   
    public function store(PurchaseFormRequest $request)
    {
        $this->authorize('create purchases');

        $purchase = [];
        $f = 0;
        $details = '';
        
        foreach ($request->items as $item) {
            $details .= $item['quantity'] . ' x ' . $item['name'] . ' at ' . number_format($item['cost_price'], 2, '.', ',')  . '<br>';

            if ($item['with_serial_number']) {
                $details .= '(' . implode(', ', $item['serial_number']) . ')<br><br>';
            }
            else {
                $details .= '<br>';
            }

            for ($i = 0; $i < $item['quantity']; $i++) {
                $purchase[$f]['item_id'] = $item['item_id'];
                $purchase[$f]['branch_id'] = $request->user()->branch_id;
                $purchase[$f]['cost_price'] = $item['cost_price'];
                $purchase[$f]['created_at'] = date('Y-m-d H:i:s');
                $purchase[$f]['updated_at'] = date('Y-m-d H:i:s');
                
                if ($item['with_serial_number']) {
                    $purchase[$f]['serial_number'] = $item['serial_number'][$i];
                }
                else {
                    $purchase[$f]['serial_number'] = NULL;
                }

                $f++;
            }

            // save to db the total quantity of each items for each branch
            $branchItem = BranchItem::where(['branch_id' => auth()->user()->branch_id, 'item_id' => $item['item_id']])->first();

            if ($branchItem !== null) {
                $branchItem->increment('quantity', $item['quantity']);
            }
            else {
                $branchItem = BranchItem::create([
                    'branch_id' => auth()->user()->branch_id,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity']
                ]);
            }
        }

        $number = Purchase::where('branch_id', auth()->user()->branch_id)->max('number') + 1;

        Purchase::create([
            'supplier_id' => $request->supplier_id,
            'branch_id' => $request->user()->branch_id,
            'number' => $number,
            'user_id' => $request->user()->id,
            'purchase_number' => $request->purchase_number,
            'details' => $details
        ])->items()->attach($purchase);

        // update the cost price of each items as it is dynamic
        foreach ($request->items as $items) {
            $dynamic_cost_price = DB::select("SELECT ROUND(SUM(total_cost_price) / SUM(qty), 2) AS dynamic_cost_price FROM (SELECT COUNT(item_id) as qty, (COUNT(item_id) * cost_price) AS total_cost_price FROM `item_purchase` where status = 'available' AND item_id = " . $items['item_id'] . " GROUP BY purchase_id) as T");
            $item = Item::find($items['item_id']);
            $item->dynamic_cost_price = $dynamic_cost_price[0]->dynamic_cost_price;
            $item->save();
        }

        $request->session()->flash('message', 'Create purchase successful!');
    }

    public function void(Purchase $purchase)
    {
        $sold = Purchase::select(DB::raw('SUM(item_purchase.status != "available") AS with_item_sold'))
            ->where('purchases.purchase_number', $purchase->purchase_number)
            ->where('purchases.id', $purchase->id)
            ->where('item_purchase.status', '!=', 'available')
            ->join('item_purchase', 'item_purchase.purchase_id', '=', 'purchases.id')
            ->get();

        if (! $sold[0]->with_item_sold) {
            $purchase = Purchase::where('id', $purchase->id)->first();
            $purchase->update(['status' => 'void']);

            foreach ($purchase->items as $item) {
                $branchItem = BranchItem::where(['branch_id' => auth()->user()->branch_id, 'item_id' => $item->id])->first();

                if ($branchItem !== null) {
                    $branchItem->decrement('quantity', $item->quantity);
                }
                else {
                    $branchItem = BranchItem::create([
                        'branch_id' => auth()->user()->branch_id,
                        'item_id' => $item->id,
                        'quantity' => $item->quantity
                    ]);
                }
            }

            $purchase->items()->update(['status' => 'void']);

            // update the cost price of each items as it is dynamic
            foreach ($purchase->items as $items) {
                $dynamic_cost_price = DB::select("SELECT ROUND(SUM(total_cost_price) / SUM(qty), 2) AS dynamic_cost_price FROM (SELECT COUNT(item_id) as qty, (COUNT(item_id) * cost_price) AS total_cost_price FROM `item_purchase` where status = 'available' AND item_id = " . $items->id . " GROUP BY purchase_id) as T");
                $item = Item::find($items->id);
                $item->dynamic_cost_price = $dynamic_cost_price[0]->dynamic_cost_price;
                $item->save();
            }

            return redirect()->route('purchase.index')
                ->with('message', 'Purchase ' . $purchase->purchase_number .' has been voided!');
        }
        else {
            return redirect()->route('purchase.index')
                ->with('type', 'danger')
                ->with('message', 'Purchase ' . $purchase->purchase_number .' cannot be voided! One or more of the items has already been sold! ');
        }

        
    }

    public function supplier()
    {
        $this->authorize('create purchases');
        $suppliers = Supplier::get();
        return view('purchase.supplier', compact('suppliers'));
    }

    public function supplierSelected(Request $request)
    {
        $this->authorize('create purchases');
        return redirect()->route('purchase.create', $request->supplier_id);
    }

    public function getAllPurchases(Request $request) {
        if ($request->ajax()) {
            $purchases = Purchase::with('supplier', 'user')
                ->select('purchases.*')
                ->where('purchases.branch_id', auth()->user()->branch_id);

            return Datatables::of($purchases)
                ->addIndexColumn()
                ->editColumn('details', function($purchases) {
                    return $purchases->details;
                })
                ->editColumn('status', function($purchases) {
                    if ($purchases->status == 'void') {
                        return "<span class='badge badge-danger'>$purchases->status</span>";
                    }
                    else {
                        return "<span class='badge badge-success'>$purchases->status</span>";
                    }
                })
                ->addColumn('action', function($purchases){
                    $actions = '';
                    
                    if (auth()->user()->can('delete purchases')) {
                        if ($purchases->status != 'void') {
                            $actions .= "<form action='" . route('purchase.void', $purchases->id) . "' class='void_purchase_form' method='POST' style='display: inline-block; margin-bottom: 2px;'>
                                            <input type='hidden' name='_token' value='" . csrf_token() . "'>
                                            <input type='hidden' name='_method' value='PUT'>
                                            <button type='submit' class='btn btn-danger'><i class='fas fa-fw fa-times'></i> Void</button>
                                        </form>";

                            return $actions;
                        }
                        return '';
                    }
                    return '';
                })
                ->rawColumns(['details', 'status', 'action'])
                ->make(true);
        }
    }
}
