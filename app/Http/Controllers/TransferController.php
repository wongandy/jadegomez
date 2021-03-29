<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Branch;
use App\Models\Transfer;
use App\Models\BranchItem;
use App\Models\ItemPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    public function index()
    {
        $this->authorize('view transfers');
        // $purchases = Purchase::with('items', 'supplier', 'user')->where('branch_id', auth()->user()->branch_id)->orderBy('id')->get();
        // return view('purchase.index', compact('purchases'));
        $transfers = Transfer::with('items', 'user', 'receivingBranch')->where('sending_branch_id', auth()->user()->branch_id)->orWhere('receiving_branch_id', auth()->user()->branch_id)->orderByDesc('id')->get();
        // dd($transfers);
        // dd($transfers->first()->items);
        return view('transfer.index', compact('transfers'));
    }

    public function create()
    {
        $this->authorize('create transfers');

        $number = Transfer::where('sending_branch_id', auth()->user()->branch_id)->max('number') + 1;
        $transfer_number = "TR-" . str_pad($number, 8, "0", STR_PAD_LEFT);

        $branch_id = auth()->user()->branch_id;
        $branches = Branch::where('id', '!=', $branch_id)->get();
        $items = Item::select(
            'items.id', 
            'name',
            'upc',
            'with_serial_number', 
            'selling_price',
            DB::raw("(SELECT CONCAT('[\"', GROUP_CONCAT(serial_number SEPARATOR '\",\"'),'\"]') FROM item_purchase WHERE item_id = items.id AND branch_id = " . $branch_id . " AND status = 'available') AS serial_numbers"),
            DB::raw("(SELECT COUNT(*) FROM item_purchase WHERE item_id = items.id AND branch_id = " . $branch_id . " AND status = 'available') AS on_hand"))->get();
        // dd($items);
        return view('transfer.create', compact('items', 'branches', 'transfer_number'));
        // return view('transfer.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create transfers');
        
        // save to db the total quantity of each items for each branch
        foreach ($request->items as $item) {
            $branchItemFrom = BranchItem::where(['branch_id' => auth()->user()->branch_id, 'item_id' => $item['item_id']])->first()->decrement('quantity', $item['quantity']);
            // $branchItemTo = BranchItem::where(['branch_id' => $request->to_branch_id, 'item_id' => $item['item_id']])->first();

            // if ($branchItemTo !== null) {
            //     $branchItemTo->increment('quantity', $item['quantity']);
            // }
            // else {
            //     $branchItemTo = BranchItem::create([
            //         'branch_id' => $request->to_branch_id,
            //         'item_id' => $item['item_id'],
            //         'quantity' => $item['quantity']
            //     ]);
            // }
        }

        $transfers = [];
        $f = 0;

        foreach ($request->items as $item) {
            if ($item['with_serial_number']) {
                $itemPurchases = ItemPurchase::where('item_id', $item['item_id'])->where('branch_id', $request->user()->branch_id)->whereIn('serial_number', $item['serial_number'])->where('status', 'available')->get();
            }
            else {
                $itemPurchases = ItemPurchase::where('item_id', $item['item_id'])->where('branch_id', $request->user()->branch_id)->where('status', 'available')->limit($item['quantity'])->get();
            }
            
            foreach ($itemPurchases as $itemPurchase) {
                $itemPurchase->update([
                    'branch_id' => $request->to_branch_id,
                    'status' => 'in transit'
                ]);
            }

            for ($i = 0; $i < $item['quantity']; $i++) {
                $transfers[$f]['item_id'] = $item['item_id'];
                $transfers[$f]['item_purchase_id'] = $itemPurchases[$i]->id;
                $transfers[$f]['created_at'] = date('Y-m-d H:i:s');
                $transfers[$f]['updated_at'] = date('Y-m-d H:i:s');
                $f++;
            }    
        }

        $number = Transfer::where('sending_branch_id', auth()->user()->branch_id)->max('number') + 1;

        $transfer = Transfer::create([
            'sending_branch_id' => $request->user()->branch_id,
            'user_id' => $request->user()->id,
            'receiving_branch_id' => $request->to_branch_id,
            'number' => $number,
            'transfer_number' => $request->transfer_number,
            'notes' => $request->notes
        ]);

        $transfer->items()->attach($transfers);
        return redirect()->route('transfer.print', $transfer->id);
    }

    public function supplier()
    {
        $this->authorize('create purchases');
        $suppliers = Supplier::get();
        return view('purchase.supplier', compact('suppliers'));
    }

    // public function supplierSelected(Request $request)
    // {
    //     $this->authorize('create purchases');
    //     return redirect()->route('purchase.create', $request->supplier_id);
    // }

    public function updateStatus(Transfer $transfer)
    {
        $transfers = Transfer::find($transfer->id);
        
        $transfers->status = "received";
        $transfers->received_by = auth()->user()->id;
        $transfers->save();

        $itemIds = [];

        foreach ($transfers->test as $transfer) {
            array_push($itemIds, $transfer->pivot->item_purchase_id);
        }

        foreach ($transfers->items as $item) {
            $branchItemTo = BranchItem::where(['branch_id' => auth()->user()->branch_id, 'item_id' => $item->id])->first();
    
                if ($branchItemTo !== null) {
                    $branchItemTo->increment('quantity', $item->quantity);
                }
                else {
                    $branchItemTo = BranchItem::create([
                        'branch_id' => auth()->user()->branch_id,
                        'item_id' => $item->id,
                        'quantity' => $item->quantity
                    ]);
                }
        }

        
        ItemPurchase::whereIn('id', $itemIds)->update(['status' => 'available']);
        
    
        return redirect()->route('transfer.index')->with('message', 'Transfer received successful!');
    }

    public function print(Transfer $transfer)
    {
        if (auth()->user()->branch_id != $transfer->sending_branch_id) {
            abort(403);
        }

        $transfer = Transfer::where('id', $transfer->id)->first();
        return view('transfer.print', compact('transfer'));
    }

    public function void(Transfer $transfer)
    {
        // dd($transfer);

        if ($transfer->status == 'received') {
            return redirect()->route('transfer.index')->with('message', 'Transfer ' . $transfer->purchase_number .' has already been received and cannot be voided anymore!');
        }

        $transfer = Transfer::where('id', $transfer->id)->first();
        // foreach ($transfer->test2 as $item) {
        //     echo $item->pivot->item_purchase_id . '<br>';
        // }
        $itemPurchaseIds = $transfer->itemPurchaseId()->pluck('item_purchase_id');
        ItemPurchase::whereIn('id', $itemPurchaseIds)->update([
            'branch_id' => auth()->user()->branch_id, 
            'status' => 'available'
        ]);
        // dd();
        // dd($transfer->items->first->pivot->item_purchase_id);
        // dd($transfer->test2);
        $transfer->update(['status' => 'void']);
        // foreach ($purchase->items as $item) {
        //     echo $item->quantity . ' ' . $item->name . '<br>';
        // }

        foreach ($transfer->items as $item) {
            $branchItem = BranchItem::where(['branch_id' => $transfer->sending_branch_id, 'item_id' => $item->id])->first();

            if ($branchItem !== null) {
                $branchItem->increment('quantity', $item->quantity);
            }
            else {
                $branchItem = BranchItem::create([
                    'branch_id' => $transfer->sending_branch_id,
                    'item_id' => $item->id,
                    'quantity' => $item->quantity
                ]);
            }
        }

        return redirect()->route('transfer.index')->with('message', 'Transfer ' . $transfer->purchase_number .' has been voided!');
    }
}
