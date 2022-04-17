<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class ItemController extends Controller
{
    public function index()
    {
        $this->authorize('view items');
        return view('item.index');
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

    public function getAllItems(Request $request)
    {
        if ($request->ajax()) {
            $items = DB::table('items')
                    ->leftJoin('item_purchase',
                        'item_purchase.item_id', '=', DB::raw('items.id AND branch_id = ' . auth()->user()->branch_id . ' AND status = "available"'))
                    ->select(
                        'items.id',
                        'items.name',
                        'items.upc',
                        'items.dynamic_cost_price',
                        'items.selling_price',
                        DB::raw('COUNT(item_purchase.item_id) AS on_hand')
                    )
                    ->groupBy('items.id')
                    ->get();

            return Datatables::of($items)
                ->addIndexColumn()
                ->addColumn('action', function($items){
                    $actions = '';
                    
                    if (auth()->user()->can('edit items')) {
                        $actions .= "<a href='" . route('item.edit', $items->id) . "' class='btn btn-info' style='margin-bottom: 2px;'><i class='fas fa-fw fa-binoculars'></i> Edit</a>";
                    }
                    return $actions;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
}
