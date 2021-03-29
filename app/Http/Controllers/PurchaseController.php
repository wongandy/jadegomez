<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\BranchItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\PurchaseFormRequest;

class PurchaseController extends Controller
{
    public function index()
    {
        // $a = Item::find(1);
        // $a->dynamic_cost_price = 31;
        // $a->save();
        // $test=DB::select("SELECT SUM(total_cost_price) / SUM(qty) AS dynamic_cost_price FROM (SELECT COUNT(item_id) as qty, (COUNT(item_id) * cost_price) AS total_cost_price FROM `item_purchase` where status = 'available' AND item_id = 1 GROUP BY purchase_id) as T");
        
        // foreach ($test as $t) {
        //     $item = Item::find(1);
        //     $item->dynamic_cost_price = $t->dynamic_cost_price;
        //     $item->save();
        // }
        // dd($test);
        $this->authorize('view purchases');
        $purchases = Purchase::with('items', 'supplier', 'user')->where('branch_id', auth()->user()->branch_id)->orderBy('id', 'DESC')->get();
        // $purchases = Purchase::get();
        
        // dd($purchases->first()->items()->where('status', '!=', 'available')->count());
        return view('purchase.index', compact('purchases'));
    }

    public function create(Supplier $supplier)
    {
        $this->authorize('create purchases');

        $number = Purchase::where('branch_id', auth()->user()->branch_id)->max('number') + 1;
        $purchase_number = "PO-" . str_pad($number, 8, "0", STR_PAD_LEFT);
        $items = Item::with('purchases')->get();
        return view('purchase.create', compact('items', 'supplier', 'purchase_number'));
    }
   
    public function store(PurchaseFormRequest $request)
    {
        $this->authorize('create purchases');

        $purchase = [];
        $f = 0;
        
        foreach ($request->items as $item) {
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
            'purchase_number' => $request->purchase_number
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
        $purchase = Purchase::where('id', $purchase->id)->first();
        $purchase->update(['status' => 'void']);
        // foreach ($purchase->items as $item) {
        //     echo $item->quantity . ' ' . $item->name . '<br>';
        // }

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

        // dd($purchase->items);
        $purchase->items()->update(['status' => 'void']);

        // update the cost price of each items as it is dynamic
        foreach ($purchase->items as $items) {
            $dynamic_cost_price = DB::select("SELECT ROUND(SUM(total_cost_price) / SUM(qty), 2) AS dynamic_cost_price FROM (SELECT COUNT(item_id) as qty, (COUNT(item_id) * cost_price) AS total_cost_price FROM `item_purchase` where status = 'available' AND item_id = " . $items->id . " GROUP BY purchase_id) as T");
            $item = Item::find($items->id);
            $item->dynamic_cost_price = $dynamic_cost_price[0]->dynamic_cost_price;
            $item->save();
        }

        // $purchase->updateExistingPivot(1, [
        //     'status' => 'void'
        // ]);
        // return redirect()->route('purchase.index');
        return redirect()->route('purchase.index')->with('message', 'Purchase ' . $purchase->purchase_number .' has been voided!');
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
}
