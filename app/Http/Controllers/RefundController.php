<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Refund;
use App\Models\ItemPurchase;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class RefundController extends Controller
{
    public function index()
    {
        return view('refund.index');
    }

    public function create(Sale $sale)
    {
        $this->authorize('create return item');
        
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

        return view('refund.create', compact('sale'));
    }

    public function store(Request $request)
    {
        $this->authorize('create return item');
        $itemPurchaseIds = collect($request->items)->pluck('item_purchase_id')->flatten();
        $branch_id = $request->user()->branch_id;

        ItemPurchase::whereIn('id', $itemPurchaseIds)->update([
            'status' => 'available',
            'branch_id' => $branch_id
        ]);

        $number = Refund::where('branch_id', $branch_id)->max('number') + 1;
        $refund_number = "CDR-" . str_pad($number, 8, "0", STR_PAD_LEFT);
        $f = 0;

        foreach ($request->items as $item) {
            $total_purchase_ids = count($item['item_purchase_id']);

            for ($i = 0; $i < $total_purchase_ids; $i++) {
                $refund[$f]['item_id'] = $item['item_id'];
                $refund[$f]['branch_id'] = $branch_id;
                $refund[$f]['sale_id'] = $item['sale_id'];
                $refund[$f]['item_purchase_id'] = $item['item_purchase_id'][$i];
                $refund[$f]['sold_price'] = $item['sold_price'];
                $refund[$f]['created_at'] = date('Y-m-d H:i:s');
                $refund[$f]['updated_at'] = date('Y-m-d H:i:s');
                $f++;
            }
        }

        Refund::create([
            'branch_id' => $branch_id,
            'user_id' => $request->user()->id,
            'sale_id' => $request->sale_id,
            'number' => $number,
            'refund_number' => $refund_number,
            'status' => 'paid',
            'refund_total' => $request->refund_total,
            'refund_total_for_reports' => 0 - $request->refund_total,
        ])->refunded()->attach($refund);

        return redirect()->route('return.index')->with('message', 'Return item successful!');
    }
    
    public function getAllReturns(Request $request) {
        if ($request->ajax()) {
            $refunds = Refund::with(
                [
                    'sale.items',
                    'sale.customer:id,name',
                    'sale.branch:id,address',
                    'sale.user:id,name',
                    'user:id,name',
                    'refunded'
                ])
                ->select(['refunds.*'])
                ->where('refunds.branch_id', '=', auth()->user()->branch_id)
                ->orderByDesc('created_at');

            return Datatables::of($refunds)
                ->editColumn('detail', function($refunds) {
                    return $refunds->refunded->pluck('detail')->implode('');
                })
                ->editColumn('status', function($refunds) {
                    if ($refunds->status == 'void') {
                        return "<span class='badge badge-danger'>$refunds->status</span>";
                    }
                    else {
                        return "<span class='badge badge-success'>$refunds->status</span>";
                    }
                })
                ->addColumn('action', function($refunds){
                    $actions = '';

                    if (auth()->user()->can('void return item')) {
                        if ($refunds->status != 'void') {
                            $actions .= "<form action='" . route('return.void', $refunds->id) . "' class='void_refund_form' method='POST' style='display: inline-block; margin-bottom: 4px;'>
                            <input type='hidden' name='_token' value='" . csrf_token() . "'>
                            <input type='hidden' name='_method' value='PUT'>
                            <button type='submit' class='btn btn-danger'><i class='fas fa-fw fa-times'></i> Void</button>
                            </form>";
                        }
                    }
                    
                    if ($refunds->status != 'void') {
                        $actions .= "<a href='" . route('return.print', $refunds->id) . "' class='btn btn-info' style='margin-bottom: 4px;'><i class='fas fa-fw fa-print'></i> Print CDR</a>";
                    }

                    return $actions;
                })
                ->rawColumns(['sale.details', 'detail', 'action', 'status'])
                ->make(true);
        }
    }

    public function void(Refund $refund)
    {
        $this->authorize('void return item');

        $item_purchase_ids = $refund->item->pluck('pivot.item_purchase_id');

        if (ItemPurchase::whereIn('id', $item_purchase_ids)->where('status', '!=', 'available')->exists()) {
            return redirect()->route('return.index')
                ->with('type', 'danger')
                ->with('message', 'Returned items cannot be voided! One or more of the items has already been sold!');
        }

        $refund->update([
            'status' => 'void',
            'refund_total' => 0,
            'refund_total_for_reports' => 0
        ]);
        
        $branch_id = $refund->sale->branch_id;

        ItemPurchase::whereIn('id', $item_purchase_ids)->update([
            'status' => 'paid',
            'branch_id' => $branch_id
        ]);

        return redirect()->route('return.index')->with('message', 'Return has been voided!');
    }

    public function print(Refund $refund)
    {
        if ($refund->status == 'void') {
            abort(403);
        }

        return view('refund.print', compact('refund'));
    }
}
