<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Sale;
use App\Models\User;
use App\Models\Branch;
use App\Models\ItemPurchase;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        // $this->redirectTo = "https:google.com";
        // return redirect()->route('login');
    }

    public function index()
    {
        $total_items = Item::count();
        $total_branches = Branch::count();
        $total_users = User::where('branch_id', auth()->user()->branch_id)->count();
        $total_sales = Sale::where('branch_id', auth()->user()->branch_id)
                        ->whereDate('created_at', date('Y-m-d'))
                        ->count();

        return view('dashboard.index', compact('total_items', 'total_branches', 'total_users', 'total_sales'));
    }
}
