<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Customer;
use App\Models\Banner;
use App\Models\Blog;
use App\Models\Cities;
use App\Models\Testimonial;
use App\Models\Pages;
use App\Models\Membership;
use App\Models\Lounge;
use App\Models\Franchise;
use App\Models\Contact;
use App\Models\Inquiry;

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
        return view("admin.dashboard.dashboard");
    }
}
