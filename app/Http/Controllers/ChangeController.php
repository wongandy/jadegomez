<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Sale;
use App\Models\Change;
use App\Models\ItemPurchase;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;

class ChangeController extends Controller
{
    public function index()
    {
        return view('change.index');
    }

    public function create(Sale $sale)
    {
        $this->authorize('create change item');
        
        if ($sale->status == 'void') {
            abort(404);
        }

        $sale = Sale::with([
            'customer',
            'item.allSoldItems' => function ($q) use ($sale) {
                $q->where('sale_id', '=', $sale->id);
            },
            'item.remainingSoldItems' => function ($q) use ($sale) {
                $q->where('sale_id', '=', $sale->id)
                    ->whereNotIn('item_purchase_id', function ($q) use ($sale) {
                        $q->select('item_purchase_id')
                            ->from('item_refund')
                            ->where('item_refund.sale_id', '=', $sale->id)
                            ->join('refunds', 'refunds.id', '=', 'item_refund.refund_id')
                            ->where('refunds.status', '!=', 'void');
                    })
                    ->whereNotIn('item_purchase_id', function ($q) use ($sale) {
                        $q->select('item_purchase_id')
                            ->from('item_defective')
                            ->where('item_defective.sale_id', '=', $sale->id)
                            ->join('defectives', 'defectives.id', '=', 'item_defective.defective_id')
                            ->where('defectives.status', '!=', 'void');
                    })
                    ->whereNotIn('item_purchase_id', function ($q) use ($sale) {
                        $q->select('item_purchase_id')
                            ->from('item_change')
                            ->where('item_change.sale_id', '=', $sale->id)
                            ->join('changes', 'changes.id', '=', 'item_change.change_id')
                            ->where('changes.status', '!=', 'void');
                    }
                );
            }
        ])->whereId($sale->id)->first();

        $items = DB::table('items')
                    ->join('item_purchase', 'item_purchase.item_id', '=', 'items.id')
                    ->select(
                        'items.id',
                        'items.name',
                        'items.upc',
                        'items.with_serial_number',
                        'items.selling_price',
                        DB::raw('COUNT(item_purchase.item_id) AS on_hand'),
                        DB::raw("CONCAT('{\"', GROUP_CONCAT(item_purchase.id, '\" : \"',serial_number SEPARATOR '\",\"'),'\"}') AS serial_numbers"),
                        // DB::raw("CONCAT('[\"', GROUP_CONCAT(serial_number SEPARATOR '\",\"'),'\"]') AS serial_numbers")
                    )
                    ->where('item_purchase.branch_id', auth()->user()->branch_id)
                    ->where('item_purchase.status', 'available')
                    ->groupBy('item_purchase.item_id')
                    ->get();
                        // dd($items);
        return view('change.create', compact('sale', 'items'));
    }

    public function store(Request $request)
    {
        $this->authorize('create change item');

        DB::transaction(function () use ($request) {
            $itemPurchaseIds = collect($request->items)->pluck('item_purchase_id')->flatten();
            $branch_id = $request->user()->branch_id;

            ItemPurchase::whereIn('id', $itemPurchaseIds)->update([
                'status' => 'available',
                'branch_id' => $branch_id
            ]);

            $number = Change::where('branch_id', $branch_id)->max('number') + 1;
            $change_number = "CDR-" . str_pad($number, 8, "0", STR_PAD_LEFT);
            $f = 0;

            foreach ($request->items as $item) {
                $total_purchase_ids = count($item['item_purchase_id']);

                for ($i = 0; $i < $total_purchase_ids; $i++) {
                    $changes[$f]['item_id'] = $item['item_id'];
                    $changes[$f]['branch_id'] = $branch_id;
                    $changes[$f]['sale_id'] = $item['sale_id'];
                    $changes[$f]['item_purchase_id'] = $item['item_purchase_id'][$i];
                    $changes[$f]['created_at'] = date('Y-m-d H:i:s');
                    $changes[$f]['updated_at'] = date('Y-m-d H:i:s');
                    $f++;
                }
            }

            if ($request->change_total > 0) {
                $change_total = $request->change_total;
            }
            else {
                $change_total = 0;
            }

            $change = Change::create([
                'branch_id' => $branch_id,
                'user_id' => $request->user()->id,
                'sale_id' => $request->sale_id,
                'number' => $number,
                'change_number' => $change_number,
                'status' => 'paid',
                'change_total' => $change_total
            ]);
            
            $change->items()->attach($changes);

            
            $itemPurchaseIds = collect($request->changes)->pluck('item_purchase_id')->flatten();
            ItemPurchase::whereIn('id', $itemPurchaseIds)->update([
                'status' => 'paid',
                'branch_id' => $branch_id
            ]);

            $x = 0;

            foreach ($request->changes as $changed) {
                $total_purchase_ids = count($changed['item_purchase_id']);

                for ($i = 0; $i < $total_purchase_ids; $i++) {
                    $changedItems[$x]['item_id'] = $changed['item_id'];
                    $changedItems[$x]['change_id'] = $change->id;
                    $changedItems[$x]['branch_id'] = $branch_id;
                    $changedItems[$x]['item_purchase_id'] = $changed['item_purchase_id'][$i];
                    $changedItems[$x]['created_at'] = date('Y-m-d H:i:s');
                    $changedItems[$x]['updated_at'] = date('Y-m-d H:i:s');
                    $x++;
                }
            }

            $change->replacements()->attach($changedItems);
        });

        return redirect()->route('change.index');
    }

    public function void(Change $change)
    {
        $this->authorize('void change item');

        DB::transaction(function () use ($change) {
            $change_item_purchase_ids = $change->items->pluck('pivot.item_purchase_id');
            
            $change->update([
                'status' => 'void',
                'change_total' => 0
            ]);
            
            ItemPurchase::whereIn('id', $change_item_purchase_ids)->update([
                'status' => 'paid',
                'branch_id' => $change->sale->branch_id
            ]);
            
            $replacement_item_purchase_ids = $change->replacements->pluck('pivot.item_purchase_id');
            
            ItemPurchase::whereIn('id', $replacement_item_purchase_ids)->update([
                'status' => 'available'
            ]);
        });

        return redirect()->route('change.index')->with('message', 'Changed item has been voided!');
    }

    public function getItemsWithOutSerialNumberForReplacement($item_id, $qty)
    {
        DB::statement('SET SESSION group_concat_max_len = 1000000');

        $data = Item::with(['purchases' => function ($query) use ($qty) { 
                        $query->select(
                            'item_purchase.id')
                            ->where('item_purchase.branch_id', auth()->user()->branch_id)
                            ->where('item_purchase.status', 'available')
                            ->limit($qty)
                            ->get(); 
                    }])->select(
                        'id',
                        'items.id',
                        'items.name',
                        'items.upc',
                        'items.with_serial_number',
                        'items.selling_price',
                    )
                    ->whereId($item_id)
                    ->first();
        
        return $data;
    }

    public function getAllChanges(Request $request) {
        if ($request->ajax()) {
            $changes = Change::with(
                [
                    'sale.items',
                    'sale.customer:id,name',
                    'sale.branch:id,address',
                    'sale.user:id,name',
                    'user:id,name',
                    'returnedItems',
                    'changedItems'
                ])
                ->select(['changes.*'])
                ->where('changes.branch_id', '=', auth()->user()->branch_id)
                ->orderByDesc('created_at');

            return Datatables::of($changes)
                ->editColumn('returnedItems', function($defectives) {
                    return $defectives->returnedItems->pluck('detail')->implode('');
                })
                ->editColumn('changedItems', function($defectives) {
                    return $defectives->changedItems->pluck('detail')->implode('');
                })
                ->editColumn('status', function($changes) {
                    if ($changes->status == 'void') {
                        return "<span class='badge badge-danger'>$changes->status</span>";
                    }
                    else {
                        return "<span class='badge badge-success'>$changes->status</span>";
                    }
                })
                ->addColumn('action', function($changes){
                    $actions = '';

                    if (auth()->user()->can('void change item')) {
                        if ($changes->status != 'void') {
                            $actions .= "<form action='" . route('change.void', $changes->id) . "' class='void_change_form' method='POST' style='display: inline-block; margin-bottom: 4px;'>
                            <input type='hidden' name='_token' value='" . csrf_token() . "'>
                            <input type='hidden' name='_method' value='PUT'>
                            <button type='submit' class='btn btn-danger'><i class='fas fa-fw fa-times'></i> Void</button>
                            </form>";
                        }
                    }
                    
                    if ($changes->status != 'void') {
                        $actions .= "<a href='" . route('change.print', $changes->id) . "' class='btn btn-info' style='margin-bottom: 4px;'><i class='fas fa-fw fa-print'></i> Print CDR</a>";
                    }

                    return $actions;
                })
                ->rawColumns(['sale.details', 'action', 'status', 'returnedItems', 'changedItems'])
                ->make(true);
                
        }
    }

    public function print(Change $change)
    {
        if ($change->status == 'void') {
            abort(403);
        }

        return view('change.print', compact('change'));
    }
}
