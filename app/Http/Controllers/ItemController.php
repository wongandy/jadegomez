<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function index()
    {
        $this->authorize('view items');
        $items = DB::table('items')
                    ->leftJoin('item_purchase',
                        'item_purchase.item_id', '=', DB::raw('items.id AND branch_id = ' . auth()->user()->branch_id . ' AND status = "available"'))
                    ->select(
                        'items.id',
                        'items.name',
                        'items.upc',
                        'items.dynamic_cost_price',
                        // 'items.with_serial_number',
                        'items.selling_price',
                        DB::raw('COUNT(item_purchase.item_id) AS on_hand')
                    )
                    ->groupBy('items.id')
                    ->orderBy('items.id', 'ASC')
                    ->get();

        return view('item.index', compact('items'));
    }

    public function create()
    {
        $this->authorize('create items');

        return view('item.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create items');

        $this->validate($request, [
            'name' => 'required|unique:items',
            'with_serial_number' => 'required'
        ]);

        Item::create($request->only('name', 'details', 'upc', 'selling_price', 'with_serial_number'));
        return redirect()->route('item.create')->with('message', 'Create item ' . $request->name . ' successful!');
    }

    public function edit(Item $item)
    {
        $this->authorize('edit items');

        return view('item.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        $this->authorize('edit items');

        $this->validate($request, [
            'name' => [
                'required',
                Rule::unique('items')->ignore($item->id)
            ],
            'selling_price' => 'required',
            'with_serial_number' => 'required'
        ]);
        
        $item->update($request->only('name', 'details', 'upc', 'selling_price', 'with_serial_number'));
        return redirect()->route('item.index')->with('message', 'Edit item successful!');
    }

    public function destroy(Item $item)
    {
        $this->authorize('delete items');

        $item->delete();
        return redirect()->back()->with('message', 'Deleted item!');
    }
}
