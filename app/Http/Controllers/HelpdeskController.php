<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelpdeskController extends Controller
{
    public function __construct()
    {
        $this->middleware(['session', 'ceklogin']);
    }

    public function dashboard_portal(Request $request){
        return view('helpdesk.dashboard_portal');
    }
    public function dashboard_trouble_ticket(Request $request){
        return view('helpdesk.dashboard_trouble_ticket');
    }
    public function dashboard_maintenance_order(Request $request){
        return view('helpdesk.dashboard_maintenance_order');
    }
}
