<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

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
        $data['latest_modified'] = DB::connection('pgsql2')->table('mart_om_troubleticketcomp')->select('modified')->orderBy('modified', 'DESC')->limit(1)->get();
        if (count($data['latest_modified']) > 0){
            $data['latest_modified'] = $data['latest_modified']->first()->modified;
        } else {
            $data['latest_modified'] = 'NO DATA';
        }
        return view('helpdesk.dashboard_trouble_ticket', compact('data'));
    }
    public function dashboard_maintenance_order(Request $request){

        $data['latest_modified'] = DB::connection('pgsql2')->table('mart_om_maintenanceorder')->select('modified')->orderBy('modified', 'DESC')->limit(1)->get();
        if (count($data['latest_modified']) > 0){
            $data['latest_modified'] = $data['latest_modified']->first()->modified;
        } else {
            $data['latest_modified'] = 'NO DATA';
        }
        return view('helpdesk.dashboard_maintenance_order', compact('data'));        
    }
}
