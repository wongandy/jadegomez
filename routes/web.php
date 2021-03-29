<?php

use App\Models\Sale;
use App\Models\User;
use App\Models\ItemPurchase;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;

Auth::routes();

Route::get('test2', function () {
    $sales = Sale::with('items', 'customer', 'user')->get();
    // dd($sales);

    return view('test2', compact('sales'));
});

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
   
    Route::get('/', [DashboardController::class, 'index']);
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

    Route::get('sale', [SaleController::class, 'index'])->name('sale.index');
    Route::get('sale/create', [SaleController::class, 'create'])->name('sale.create');
    Route::get('sale/{sale}/review', [SaleController::class, 'review'])->name('sale.review');
    Route::get('sale/{sale}/print', [SaleController::class, 'print'])->name('sale.print');
    Route::post('sale/{sale}/updateStatus', [SaleController::class, 'updateStatus'])->name('sale.updatestatus');
    Route::post('sale/store', [SaleController::class, 'store'])->name('sale.store');
    Route::post('sale/endofday', [SaleController::class, 'endOfDay'])->name('sale.endofday');
    Route::put('sale/{sale}/void', [SaleController::class, 'void'])->name('sale.void');
    Route::get('customer/dataAjax', [CustomerController::class, 'dataAjax'])->name('customer.dataajax');

    Route::get('transfer', [TransferController::class, 'index'])->name('transfer.index');
    Route::get('transfer/create', [TransferController::class, 'create'])->name('transfer.create');
    Route::get('transfer/{transfer}/print', [TransferController::class, 'print'])->name('transfer.print');
    Route::post('transfer/store', [TransferController::class, 'store'])->name('transfer.store');
    Route::post('transfer/{transfer}/updateStatus', [TransferController::class, 'updateStatus'])->name('transfer.updatestatus');
    Route::put('transfer/{transfer}/void', [TransferController::class, 'void'])->name('transfer.void');

    Route::get('report', [ReportController::class, 'index'])->name('report.index');
    Route::get('report/create', [ReportController::class, 'create'])->name('report.create');
    Route::post('report/print', [ReportController::class, 'print'])->name('report.print');
    // Route::get('report/print', [ReportController::class, 'print'])->name('report.print');
});