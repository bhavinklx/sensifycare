<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Patient;
use Carbon\Carbon;
use DB;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    /*public function __construct()
    {
        $this->middleware('auth');
    }*/
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $year = Carbon::now()->year;
        $register = Patient::select(
            DB::raw("MONTH(created_at) as month"),
            DB::raw("COUNT(*) as total")
        )
            ->whereYear('created_at', $year)
            ->groupBy(DB::raw("MONTH(created_at)"))
            ->pluck('total', 'month')
            ->toArray();

        // Prepare 12 months default 0
        $registerData = [];
        for ($i = 1; $i <= 12; $i++) {
            $registerData[] = $register[$i] ?? 0;
        }

        // Male Count
        $male = Patient::where('patient_gender', 'male')->count();
        // Female Count
        $female = Patient::where('patient_gender', 'female')->count();
        // Other Count (example: age < 15)
        $other = Patient::where('patient_gender', 'other')->count();

        return view("admin.dashboard.dashboard", compact('registerData', 'male', 'female', 'other'));
    }
}
