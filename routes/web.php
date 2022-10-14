<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogController;
use App\Http\Controllers\RmaController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\RefundController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DefectiveController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ChangeController;
use App\Http\Controllers\ItemPurchaseController;

Auth::routes();

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
   
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('item/getAllItems', [ItemController::class, 'getAllItems'])->name('item.getAllItems');
    Route::resource('item', ItemController::class);
    Route::resource('branch', BranchController::class);
    Route::resource('role', RoleController::class);
    Route::resource('user', UserController::class);
    Route::resource('supplier', SupplierController::class);

    Route::get('purchase', [PurchaseController::class, 'index'])->name('purchase.index');
    Route::get('purchase/supplier', [PurchaseController::class, 'supplier'])->name('purchase.supplier');
    Route::post('purchase/supplier', [PurchaseController::class, 'supplierSelected'])->name('purchase.supplier');
    Route::get('purchase/{supplier}/create', [PurchaseController::class, 'create'])->name('purchase.create');
    Route::post('purchase/store', [PurchaseController::class, 'store'])->name('purchase.store');
    Route::put('purchase/{purchase}/void', [PurchaseController::class, 'void'])->name('purchase.void');
    Route::get('purchase/getAllPurchases', [PurchaseController::class, 'getAllPurchases'])->name('purchase.getAllPurchases');

    Route::get('sale', [SaleController::class, 'index'])->name('sale.index');
    Route::get('sale/create', [SaleController::class, 'create'])->name('sale.create');
    Route::get('sale/{sale}/review', [SaleController::class, 'review'])->name('sale.review');
    Route::get('sale/{sale}/print', [SaleController::class, 'print'])->name('sale.print');
    Route::post('sale/{sale}/updateStatus', [SaleController::class, 'updateStatus'])->name('sale.updatestatus');
    Route::post('sale/store', [SaleController::class, 'store'])->name('sale.store');
    Route::post('sale/endofday', [SaleController::class, 'endOfDay'])->name('sale.endofday');
    Route::put('sale/{sale}/void', [SaleController::class, 'void'])->name('sale.void');
    Route::get('customer/dataAjax', [CustomerController::class, 'dataAjax'])->name('customer.dataajax');
    Route::get('sale/getAllSales', [SaleController::class, 'getAllSales'])->name('sale.getAllSales');
    Route::get('sale/getAllBranchesSales/{type}', [SaleController::class, 'getAllBranchesSales'])->name('sale.getAllBranchesSales');

    Route::get('transfer', [TransferController::class, 'index'])->name('transfer.index');
    Route::get('transfer/create', [TransferController::class, 'create'])->name('transfer.create');
    Route::get('transfer/{transfer}/print', [TransferController::class, 'print'])->name('transfer.print');
    Route::post('transfer/store', [TransferController::class, 'store'])->name('transfer.store');
    Route::post('transfer/{transfer}/updateStatus', [TransferController::class, 'updateStatus'])->name('transfer.updatestatus');
    Route::put('transfer/{transfer}/void', [TransferController::class, 'void'])->name('transfer.void');
    Route::get('transfer/getAllTransfers', [TransferController::class, 'getAllTransfers'])->name('transfer.getAllTransfers');

    Route::get('report/create', [ReportController::class, 'create'])->name('report.create');
    Route::post('report/print', [ReportController::class, 'print'])->name('report.print');

    Route::get('rma/create', [RmaController::class, 'create'])->name('rma.create');
    Route::get('itempurchase/rmaTrack', [ItemPurchaseController::class, 'rmaTrack'])->name('itempurchase.rmatrack');
        
    Route::get('log', [LogController::class, 'create'])->name('log');
    Route::get('log/displayLog', [LogController::class, 'displayLog'])->name('log.displaylog');

    Route::get('return/{sale}/create', [RefundController::class, 'create'])->name('return.create');
    Route::post('return/store', [RefundController::class, 'store'])->name('return.store');
    Route::get('return', [RefundController::class, 'index'])->name('return.index');
    Route::get('return/getAllReturns', [RefundController::class, 'getAllReturns'])->name('return.getAllReturns');
    Route::put('return/{refund}/void', [RefundController::class, 'void'])->name('return.void');
    Route::get('return/{refund}/print', [RefundController::class, 'print'])->name('return.print');

    Route::get('defective', [DefectiveController::class, 'index'])->name('defective.index');
    Route::get('defective/{sale}/create', [DefectiveController::class, 'create'])->name('defective.create');
    Route::post('defective/store', [DefectiveController::class, 'store'])->name('defective.store');
    Route::put('defective/{defective}/void', [DefectiveController::class, 'void'])->name('defective.void');
    Route::get('defective/{defective}/print', [DefectiveController::class, 'print'])->name('defective.print');
    Route::get('defective/getAllDefectives', [DefectiveController::class, 'getAllDefectives'])->name('defective.getAllDefectives');
    Route::get('getItemsWithSerialNumberForReplacement/{item_id}', [DefectiveController::class, 'getItemsWithSerialNumberForReplacement']);
    Route::get('getItemsWithOutSerialNumberForReplacement/{item_id}/{qty}', [DefectiveController::class, 'getItemsWithOutSerialNumberForReplacement']);

    Route::get('change',[ChangeController::class, 'index'])->name('change.index');
    Route::get('change/{sale}/create', [ChangeController::class, 'create'])->name('change.create');
    Route::post('change/store', [ChangeController::class, 'store'])->name('change.store');
    Route::put('change/{change}/void', [ChangeController::class, 'void'])->name('change.void');
    Route::get('change/{change}/print', [ChangeController::class, 'print'])->name('change.print');
    Route::get('change/getAllChanges', [ChangeController::class, 'getAllChanges'])->name('change.getAllChanges');
    Route::get('getItemsWithOutSerialNumberForReplacement/{item_id}/{qty}', [ChangeController::class, 'getItemsWithOutSerialNumberForReplacement']);
});