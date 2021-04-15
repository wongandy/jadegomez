<?php

namespace App\Http\Controllers;

use App\Models\ItemPurchase;
use Illuminate\Http\Request;

class ItemPurchaseController extends Controller
{
    public function rmaTrack(Request $request)
    {
        $data = [];

        if ($request->serial_number) {
            $data = ItemPurchase::select('id', 'item_id', 'serial_number')
                ->where('serial_number', 'LIKE', "%$request->serial_number%")
                ->whereIn('status', ['paid', 'unpaid'])
                ->with([ 
                    'itemSale' => function ($q) {
                        $q->select('item_purchase_id', 'item_id', 'sale_id')
                            ->with('item', function ($q) {
                                $q->select('id', 'name');
                        })->with('sale', function ($q) {
                            $q->select('id', 'sale_number', 'customer_id', 'branch_id', 'created_at', 'user_id', 'approved_by')
                                ->with('branch', function ($q) {
                                    $q->select('id', 'address');
                                })
                                ->with('customer', function ($q) {
                                    $q->select('id', 'name', 'contact_number');
                                })
                                ->with('user', function ($q) {
                                    $q->select('id', 'name');
                                })
                                ->with('approvedByUser', function ($q) {
                                    $q->select('id', 'name');
                                });
                    })->latest();
                }])->get();
        }

        return response()->json($data);
    }
}
