<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Sale;
use App\Models\Defective;
use App\Models\ItemPurchase;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;

class DefectiveController extends Controller
{
    public function index()
    {
        return view('defective.index');
    }

    public function create(Sale $sale)
    {
        $this->authorize('create defective item');
        
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
                    }
                );
            }
        ])->whereId($sale->id)->first();

        return view('defective.create', compact('sale'));
    }

    public function store(Request $request)
    {
        $this->authorize('create defective item');

        $itemPurchaseIds = collect($request->items)->pluck('item_purchase_id')->flatten();
        $branch_id = $request->user()->branch_id;
        ItemPurchase::whereIn('id', $itemPurchaseIds)->update([
            'status' => 'defective',
            'branch_id' => $branch_id
        ]);

        $number = Defective::where('branch_id', $branch_id)->max('number') + 1;
        $defective_number = "CDR-" . str_pad($number, 8, "0", STR_PAD_LEFT);
        $f = 0;

        foreach ($request->items as $item) {
            $total_purchase_ids = count($item['item_purchase_id']);

            for ($i = 0; $i < $total_purchase_ids; $i++) {
                $defectives[$f]['item_id'] = $item['item_id'];
                $defectives[$f]['branch_id'] = $branch_id;
                $defectives[$f]['sale_id'] = $item['sale_id'];
                $defectives[$f]['item_purchase_id'] = $item['item_purchase_id'][$i];
                $defectives[$f]['created_at'] = date('Y-m-d H:i:s');
                $defectives[$f]['updated_at'] = date('Y-m-d H:i:s');
                $f++;
            }
        }

        $defective = Defective::create([
            'branch_id' => $branch_id,
            'user_id' => $request->user()->id,
            'sale_id' => $request->sale_id,
            'number' => $number,
            'defective_number' => $defective_number,
            'status' => 'paid'
        ]);
        
        $itemPurchaseIds = collect($request->replacements)->pluck('item_purchase_id')->flatten();

        ItemPurchase::whereIn('id', $itemPurchaseIds)->update([
            'status' => 'paid',
            'branch_id' => $branch_id
        ]);

        $x = 0;

        foreach ($request->replacements as $replacement) {
            $total_purchase_ids = count($replacement['item_purchase_id']);

            for ($i = 0; $i < $total_purchase_ids; $i++) {
                $replacements[$x]['item_id'] = $replacement['item_id'];
                $replacements[$x]['defective_id'] = $defective->id;
                $replacements[$x]['branch_id'] = $branch_id;
                $replacements[$x]['item_purchase_id'] = $replacement['item_purchase_id'][$i];
                $replacements[$x]['created_at'] = date('Y-m-d H:i:s');
                $replacements[$x]['updated_at'] = date('Y-m-d H:i:s');
                $x++;
            }
        }

        $defective->declaredDefective()->attach($defectives);
        $defective->defectiveReplacement()->attach($replacements);

        return redirect()->route('defective.index')->with('message', 'Create defective item successful!');
    }
    
    public function getAllDefectives(Request $request) {
        if ($request->ajax()) {
            $defectives = Defective::with(
                [
                    'sale.items',
                    'sale.customer:id,name',
                    'sale.branch:id,address',
                    'sale.user:id,name',
                    'user:id,name',
                    'declaredDefective'
                ])
                ->select(['defectives.*'])
                ->where('defectives.branch_id', '=', auth()->user()->branch_id)
                ->orderByDesc('created_at');

            return Datatables::of($defectives)
                ->editColumn('defectiveItems', function($defectives) {
                    return $defectives->declaredDefective->pluck('detail')->implode('');
                })
                ->editColumn('replacedItems', function($defectives) {
                    return $defectives->defectiveReplacement->pluck('detail')->implode('');
                })
                ->editColumn('status', function($defectives) {
                    if ($defectives->status == 'void') {
                        return "<span class='badge badge-danger'>$defectives->status</span>";
                    }
                    else {
                        return "<span class='badge badge-success'>$defectives->status</span>";
                    }
                })
                ->addColumn('action', function($defectives){
                    $actions = '';

                    if (auth()->user()->can('void defective item')) {
                        if ($defectives->status != 'void') {
                            $actions .= "<form action='" . route('defective.void', $defectives->id) . "' class='void_defective_form' method='POST' style='display: inline-block; margin-bottom: 4px;'>
                            <input type='hidden' name='_token' value='" . csrf_token() . "'>
                            <input type='hidden' name='_method' value='PUT'>
                            <button type='submit' class='btn btn-danger'><i class='fas fa-fw fa-times'></i> Void</button>
                            </form>";
                        }
                    }
                    
                    if ($defectives->status != 'void') {
                        $actions .= "<a href='" . route('defective.print', $defectives->id) . "' class='btn btn-info' style='margin-bottom: 4px;'><i class='fas fa-fw fa-print'></i> Print CDR</a>";
                    }

                    return $actions;
                })
                ->rawColumns(['sale.details', 'defectiveItems', 'replacedItems', 'action', 'status'])
                ->make(true);
                
        }
    }

    public function getItemsWithSerialNumberForReplacement($item_id)
    {
        DB::statement('SET SESSION group_concat_max_len = 1000000');

        $data = Item::select(
                    'items.id',
                    'items.name',
                    'items.upc',
                    'items.with_serial_number',
                    'items.selling_price',
                    DB::raw("CONCAT('{\"', GROUP_CONCAT(item_purchase.id, '\" : \"',serial_number SEPARATOR '\",\"'),'\"}') AS serial_numbers"),
                    DB::raw("COUNT('serial_numbers') AS remainingQuantity")
                )
                ->join('item_purchase', 'item_purchase.item_id', '=', 'items.id')
                ->where('item_purchase.branch_id', auth()->user()->branch_id)
                ->where('item_purchase.status', 'available')
                ->where('items.id', $item_id)
                ->groupBy('item_purchase.item_id')
                ->first();
        
        return $data;
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

    public function void(Defective $defective)
    {
        $this->authorize('void defective item');

        $defective_item_purchase_ids = $defective->item->pluck('pivot.item_purchase_id');

        // if (ItemPurchase::whereIn('id', $defective_item_purchase_ids)->where('status', '!=', 'available')->exists()) {
        //     return redirect()->route('defective.index')
        //         ->with('type', 'danger')
        //         ->with('message', 'Defective items cannot be voided! One or more of the items has already been sold!');
        // }

        $defective->update([
            'status' => 'void'
        ]);

        ItemPurchase::whereIn('id', $defective_item_purchase_ids)->update([
            'status' => 'paid',
            'branch_id' => $defective->sale->branch_id
        ]);

        $replacement_item_purchase_ids = $defective->itemDefectiveReplacements->pluck('pivot.item_purchase_id');

        ItemPurchase::whereIn('id', $replacement_item_purchase_ids)->update([
            'status' => 'available'
        ]);

        return redirect()->route('defective.index')->with('message', 'Defective item has been voided!');
    }

    public function print(Defective $defective)
    {
        if ($defective->status == 'void') {
            abort(403);
        }

        return view('defective.print', compact('defective'));
    }
}
