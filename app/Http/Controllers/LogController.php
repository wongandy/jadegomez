<?php

namespace App\Http\Controllers;

// use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Notifications\Action;
use Spatie\Activitylog\Models\Activity;

class LogController extends Controller
{
    public function create()
    {
        $users = User::where('branch_id', auth()->user()->branch_id)->get();
        return view('log.create', compact('users'));
    }

    public function displayLog(Request $request)
    {
        $data = [];
        $activities = Activity::select()
            ->where('causer_id', $request->user_id)
            ->whereBetween('created_at', [$request->from . ' 00:00:00', $request->to . ' 23:59:59'])
            ->orderBy('created_at', 'DESC')
            ->get();
            
        return response()->json($activities);
    }
}
