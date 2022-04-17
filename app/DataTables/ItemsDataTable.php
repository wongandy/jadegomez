<?php

namespace App\DataTables;

use App\Models\Item;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\DB;

class ItemsDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables($query)
        ->addIndexColumn()
        ->addColumn('action', function ($item) {
            $btn = '';

            if (auth()->user()->can('edit items')) {
                $btn = "<a href='" . route('item.edit', $item->id) . "' class='btn btn-info' style='margin-bottom: 2px;'><i class='fas fa-fw fa-binoculars'></i> Edit</a>";
            }
            
            return $btn;
        })->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Item $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Item $item)
    {
        return $item->newQuery()->leftJoin('item_purchase',
            'item_purchase.item_id', '=', DB::raw('items.id AND branch_id = ' . auth()->user()->branch_id . ' AND status = "available"'))
            ->groupBy('items.id')
            ->select(
                'items.id',
                'items.name',
                'items.upc',
                'items.dynamic_cost_price',
                'items.selling_price',
                DB::raw('COUNT(item_purchase.item_id) AS on_hand')
            );
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('itemdatatable-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(0, 'asc')
                    ->buttons(
                        Button::make('excel'),
                        Button::make('print')
                    );
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            [
                'name' => 'id', 
                'title' => 'Id', 
                'data' => 'id', 
                'visible' => false, 
                'searchable' => false, 
                'printable' => false, 
                'exportable' => false
            ],
            [
                'name' => 'name', 
                'title' => 'Name', 
                'data' => 'name'
            ],
            [
                'name' => 'on_hand', 
                'title' => 'On Hand', 
                'data' => 'on_hand', 
                'searchable' => false
            ],
            [
                'name' => 'upc', 
                'title' => 'UPC', 
                'data' => 'upc', 
                'printable' => false,
                'exportable' => false
            ],
            [
                'name' => 'dynamic_cost_price', 
                'title' => 'Cost Price', 
                'data' => 'dynamic_cost_price'
            ],
            [
                'name' => 'selling_price', 
                'title' => 'Selling Price', 
                'data' => 'selling_price'
            ],
            [
                'name' => 'action', 
                'title' => '', 
                'data' => 'action', 
                'printable' => false,
                'exportable' => false
            ],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Items_' . date('YmdHis');
    }
}
