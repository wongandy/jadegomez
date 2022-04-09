<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Branch;
use App\Models\Transfer;
use App\Models\BranchItem;
use App\Models\ItemPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class TransferController extends Controller
{
    public function index()
    {
        $this->authorize('view transfers');
        return view('transfer.index');
    }

    public function create()
    {
        $this->authorize('create transfers');
        DB::statement('SET SESSION group_concat_max_len = 1000000');
        $number = Transfer::where('sending_branch_id', auth()->user()->branch_id)->max('number') + 1;
        $transfer_number = "TR-" . str_pad($number, 8, "0", STR_PAD_LEFT);
        $branch_id = auth()->user()->branch_id;
        $branches = Branch::where('id', '!=', $branch_id)->get();
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

        return view('transfer.create', compact('items', 'branches', 'transfer_number'));
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
        $details = '';

        foreach ($request->items as $item) {
            $details .= $item['quantity'] . ' x ' . $item['name'] . '<br>';

            if ($item['with_serial_number']) {
                $itemPurchases = ItemPurchase::where('item_id', $item['item_id'])->where('branch_id', $request->user()->branch_id)->whereIn('serial_number', $item['serial_number'])->where('status', 'available')->get();
                $details .= '(' . $itemPurchases->implode('serial_number', ', ') . ')' . '<br><br>';
            }
            else {
                $itemPurchases = ItemPurchase::where('item_id', $item['item_id'])->where('branch_id', $request->user()->branch_id)->where('status', 'available')->limit($item['quantity'])->get();
                $details .= '<br>';
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
        $transfer_number = "TR-" . str_pad($number, 8, "0", STR_PAD_LEFT);

        $transfer = Transfer::create([
            'sending_branch_id' => $request->user()->branch_id,
            'user_id' => $request->user()->id,
            'receiving_branch_id' => $request->to_branch_id,
            'number' => $number,
            'transfer_number' => $transfer_number,
            'details' => $details,
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

        if ($transfer->status == 'void') {
            return redirect()->route('transfer.index')->with('message', 'Transfer has already been voided');
        }
        
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
        if ($transfer->status == 'received') {
            return redirect()->route('transfer.index')->with('message', 'Transfer ' . $transfer->purchase_number .' has already been received and cannot be voided anymore!');
        }

        $transfer = Transfer::where('id', $transfer->id)->first();
        $itemPurchaseIds = $transfer->itemPurchaseId()->pluck('item_purchase_id');
        ItemPurchase::whereIn('id', $itemPurchaseIds)->update([
            'branch_id' => auth()->user()->branch_id, 
            'status' => 'available'
        ]);

        $transfer->update(['status' => 'void']);

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

    public function getAllTransfers(Request $request) {
        if ($request->ajax()) {
            $transfers = Transfer::with('user', 'receivedByUser', 'sendingBranch', 'receivingBranch')
                ->select('transfers.*')
                ->where(function ($query) {
                    $query->where('sending_branch_id', auth()->user()->branch_id)
                        ->orWhere('receiving_branch_id', auth()->user()->branch_id);
                });

            return Datatables::of($transfers)
                ->addIndexColumn()
                ->editColumn('details', function($transfers) {
                    return $transfers->details;
                })
                // ->filterColumn('information', function($query, $keyword) {
                //     $query->where('status', 'like', "%{$keyword}%")
                //         ->orWhere('transfer_number', 'like', "%{$keyword}%");
                // })
                ->addColumn('information', function($transfers) {
                    if ($transfers->sending_branch_id == auth()->user()->branch_id) {
                        // return $transfers->transfer_number . ' - ' . $transfers->status;
                        return "<span style='color: red;'>Sent to " . $transfers->receivingBranch->address . " </span>";
                    }
                    else {
                        // return $transfers->status . ' - ' . $transfers->transfer_number;
                        return "<span style='color: green;'>Received from " . $transfers->sendingBranch->address . "</span>";
                    }
                })
                ->editColumn('receivedByUser', function($transfers) {
                    if ($transfers->receivedByUser) {
                        return $transfers->receivedByUser->name;
                    }
                    return '';
                })
                ->editColumn('status', function($transfers) {
                    if ($transfers->status == 'void') {
                        return "<span class='badge badge-danger'>$transfers->status</span>";
                    }
                    elseif ($transfers->status == 'pending') {
                        return "<span class='badge badge-warning'>$transfers->status</span>";
                    }
                    else {
                        return "<span class='badge badge-success'>$transfers->status</span>";
                    }
                })
                ->addColumn('action', function($transfers){
                    $actions = '';
                    
                    if (auth()->user()->can('approve transfers')) {
                        if ($transfers->status != 'received' && $transfers->status != 'void' && $transfers->receiving_branch_id == auth()->user()->branch_id) {
                            $actions .= "<form action='" . route('transfer.updatestatus', $transfers) . "' class='receive_transfer_form' method='POST' style='margin-bottom: 2px;'>
                                            <input type='hidden' name='_token' value='" . csrf_token() . "'>
                                            <button type='submit' class='btn btn-info'>Receive</button>
                                        </form>";
                        }
                    }

                    if (auth()->user()->can('delete transfers')) {
                        if ($transfers->status != 'received' && $transfers->status != 'void' && $transfers->sending_branch_id == auth()->user()->branch_id) {
                            $actions .= "<form action='" . route('transfer.void', $transfers->id) . "' class='void_transfer_form' method='POST' style='display: inline-block; margin-bottom: 2px;'>
                                            <input type='hidden' name='_token' value='" . csrf_token() . "'>
                                            <input type='hidden' name='_method' value='PUT'>
                                            <button type='submit' class='btn btn-danger'><i class='fas fa-fw fa-times'></i> Void</button>
                                        </form>";
                        }
                    }

                    if ($transfers->sending_branch_id == auth()->user()->branch_id && $transfers->status != 'void') {
                        $actions .= "<a target='_blank' href='" . route('transfer.print', $transfers->id) . "' class='btn btn-info' style='display: inline-block; margin-bottom: 2px;'><i class='fas fa-fw fa-print'></i> Print DR</a>";
                    }

                    return $actions;
                })
                ->rawColumns(['details', 'information', 'status', 'action'])
                ->make(true);
        }
    }
}
