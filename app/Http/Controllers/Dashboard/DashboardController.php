<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor;
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
        // Total Patients
        $totalPatient = Patient::count();

        // This Month
        $thisMonthPatient = Patient::whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->count();

        // Last Month
        $lastMonthPatient = Patient::whereBetween('created_at', [
            Carbon::now()->subMonth()->startOfMonth(),
            Carbon::now()->subMonth()->endOfMonth()
        ])->count();

        // Percentage Calculation
        if ($lastMonthPatient > 0) {
            $patientPercentage = (($thisMonthPatient - $lastMonthPatient) / $lastMonthPatient) * 100;
            if ($thisMonthPatient == 0) {
                $patientPercentage = 0;
            }
        } else {
            $patientPercentage = $thisMonthPatient > 0 ? 100 : 0;
        }

        $totalDoctor = Doctor::count();

        $thisMonthDoctor = Doctor::whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->count();

        $lastMonthDoctor = Doctor::whereBetween('created_at', [
            Carbon::now()->subMonth()->startOfMonth(),
            Carbon::now()->subMonth()->endOfMonth()
        ])->count();

        if ($lastMonthDoctor > 0) {
            $doctorPercentage = (($thisMonthDoctor - $lastMonthDoctor) / $lastMonthDoctor) * 100;
            if ($thisMonthDoctor == 0) {
                $doctorPercentage = 0;
            }
        } else {
            $doctorPercentage = $thisMonthDoctor > 0 ? 100 : 0;
        }

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

        return view("admin.dashboard.dashboard", compact(
            'totalPatient',
            'patientPercentage',
            'totalDoctor',
            'doctorPercentage',
            'registerData',
            'male',
            'female',
            'other'
        ));
    }

    public function analyzeReport(Request $request)
    {
        // Validate: At least one of 'file_url' OR 'question' must be provided
        $request->validate([
            'file_url' => 'required_without:question',
            'question' => 'required_without:file_url',
            'language' => 'nullable|string'
        ]);

        // Prepare the payload for the AI Agent
        $payload = [
            'language' => $request->language ?? 'en'
        ];

        if ($request->filled('file_url')) {
            // Option 1: Handle File Upload (Image, PDF, Excel)
            $payload['type'] = 'file';
            $payload['fileUrl'] = $request->file_url;
        } else {
            // Option 2: Handle Health Question (Message)
            $payload['type'] = 'text';
            $payload['question'] = $request->question;
        }

        // Call the Sensify Care AI Agent
        $response = Http::timeout(60)->post(
            'http://localhost:5000/analyze-report',
            $payload
        );

        // Return the agent's response (now contains 'answer' and 'disclaimer')
        return response()->json($response->json());
    }

}
