<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ApiController extends Controller
{
    public function get_list_trouble_ticket(Request $request)
    {
        $query = DB::connection('pgsql2')->table('mart_om_troubleticketcomp');

        $filterModel = json_decode($request->input('filterModel', '{}'), true);
        $sortModel = json_decode($request->input('sortModel', '[]'), true);

        foreach ($filterModel as $field => $filter) {
            $operator = strtoupper($filter['operator'] ?? 'AND');
            $conditions = $filter['conditions'] ?? [];

            // fallback for single filter (non-multi condition)
            if (empty($conditions) && isset($filter['type'])) {
                $conditions = [$filter];
            }

            $query->where(function ($q) use ($field, $conditions, $operator) {
                foreach ($conditions as $index => $condition) {
                    $type = $condition['type'] ?? null;
                    $filterType = $condition['filterType'] ?? null;

                    // Prepare value
                    $value = $condition['filter'] ?? $condition['dateFrom'] ?? null;

                    // Define logic
                    $clause = ($index === 0 || $operator === 'AND') ? 'where' : 'orWhere';

                    if ($filterType === 'text') {
                        if ($type === 'contains') {
                            $q->$clause($field, 'like', "%$value%");
                        } elseif ($type === 'notContains') {
                            $q->$clause($field, 'not like', "%$value%");
                        } elseif ($type === 'equals') {
                            $q->$clause($field, '=', $value);
                        } elseif ($type === 'notEqual') {
                            $q->$clause($field, '!=', $value);
                        } elseif ($type === 'startsWith') {
                            $q->$clause($field, 'like', "$value%");
                        } elseif ($type === 'endsWith') {
                            $q->$clause($field, 'like', "%$value");
                        } elseif ($type === 'blank') {
                            $q->$clause($field, '=', NULL);
                        } elseif ($type === 'notBlank'){
                            $q->$clause($field, '!=', NULL);
                        }
                    }

                    if ($filterType === 'date') {
                        if ($type === 'equals') {
                            $q->$clause(\DB::raw("DATE($field)"), '=', substr($value, 0, 10));
                        } elseif ($type === 'notEqual') {
                            $q->$clause(\DB::raw("DATE($field)"), '!=', substr($value, 0, 10));
                        } elseif ($type === 'inRange') {
                            $start = substr($condition['dateFrom'], 0, 10);
                            $end = substr($condition['dateTo'], 0, 10);
                            $q->$clause(function($dateQ) use ($field, $start, $end) {
                                $dateQ->whereDate($field, '>=', $start)
                                    ->whereDate($field, '<=', $end);
                            });
                        } elseif ($type === 'lessThan') {
                            $q->$clause(\DB::raw("DATE($field)"), '<', substr($value, 0, 10));
                        } elseif ($type === 'greaterThan') {
                            $q->$clause(\DB::raw("DATE($field)"), '>', substr($value, 0, 10));
                        } elseif ($type === 'blank') {
                            $q->$clause($field, '=', NULL);
                        } elseif ($type === 'notBlank'){
                            $q->$clause($field, '!=', NULL);
                        }
                    }
                }
            });
        }

        foreach ($sortModel as $sort) {
            $query->orderBy($sort['colId'], $sort['sort']);
        }

        // if (!str_contains(session()->get('user')->privilege, 'ADMIN')){
        //     $explode = explode(',', session()->get('user')->mitra_om);
        //     if (count($explode) == 0){
        //         $query->where('mitra_om', NULL);
        //     } else {
        //         $query->where(function ($q) use ($explode){
        //             foreach($explode as $key => $e){
        //                 if ($key == 0){
        //                     $q->where('mitra_om', $e);
        //                 } else {
        //                     $q->orWhere('mitra_om', $e);
        //                 }
        //             }
        //         });
        //     }
        // }

        $start = intval($request->startRow ?? 0);
        $end = intval($request->endRow ?? 50);
        $limit = $end - $start;

        $filteredQuery = clone $query;
        $totalFiltered = $filteredQuery->count();

        // Get full count (unfiltered)
        $totalAll = DB::connection('pgsql2')->table('mart_om_troubleticketcomp');

        // if (!str_contains(session()->get('user')->privilege, 'ADMIN')){
        //     $explode = explode(',', session()->get('user')->mitra_om);
        //     if (count($explode) == 0){
        //         $totalAll->where('mitra_om', NULL);
        //     } else {
        //         $totalAll->where(function ($q) use ($explode){
        //             foreach($explode as $key => $e){
        //                 if ($key == 0){
        //                     $q->where('mitra_om', $e);
        //                 } else {
        //                     $q->orWhere('mitra_om', $e);
        //                 }
        //             }
        //         });
        //     }
        // }
        $totalAll = $totalAll->count();

        $total = $query->count();
        $rows = $query->skip($start)->take($limit)->get();

        return response()->json([
            'rows' => $rows,
            'lastRow' => $totalFiltered,
            'totalFiltered' => $totalFiltered,
            'totalAll' => $totalAll,
        ]);
    }

    public function export_trouble_ticket(Request $request)
    {
        $query = DB::connection('pgsql2')->table('mart_om_troubleticketcomp');

        $filterModel = json_decode($request->input('filterModel', '{}'), true);
        $sortModel = json_decode($request->input('sortModel', '[]'), true);

        foreach ($filterModel as $field => $filter) {
            $operator = strtoupper($filter['operator'] ?? 'AND');
            $conditions = $filter['conditions'] ?? [];

            // fallback for single filter (non-multi condition)
            if (empty($conditions) && isset($filter['type'])) {
                $conditions = [$filter];
            }

            $query->where(function ($q) use ($field, $conditions, $operator) {
                foreach ($conditions as $index => $condition) {
                    $type = $condition['type'] ?? null;
                    $filterType = $condition['filterType'] ?? null;

                    // Prepare value
                    $value = $condition['filter'] ?? $condition['dateFrom'] ?? null;

                    // Define logic
                    $clause = ($index === 0 || $operator === 'AND') ? 'where' : 'orWhere';

                    if ($filterType === 'text') {
                        if ($type === 'contains') {
                            $q->$clause($field, 'like', "%$value%");
                        } elseif ($type === 'notContains') {
                            $q->$clause($field, 'not like', "%$value%");
                        } elseif ($type === 'equals') {
                            $q->$clause($field, '=', $value);
                        } elseif ($type === 'notEqual') {
                            $q->$clause($field, '!=', $value);
                        } elseif ($type === 'startsWith') {
                            $q->$clause($field, 'like', "$value%");
                        } elseif ($type === 'endsWith') {
                            $q->$clause($field, 'like', "%$value");
                        } elseif ($type === 'blank') {
                            $q->$clause($field, '=', NULL);
                        } elseif ($type === 'notBlank'){
                            $q->$clause($field, '!=', NULL);
                        }
                    }

                    if ($filterType === 'date') {
                        if ($type === 'equals') {
                            $q->$clause(\DB::raw("DATE($field)"), '=', substr($value, 0, 10));
                        } elseif ($type === 'notEqual') {
                            $q->$clause(\DB::raw("DATE($field)"), '!=', substr($value, 0, 10));
                        } elseif ($type === 'inRange') {
                            $start = substr($condition['dateFrom'], 0, 10);
                            $end = substr($condition['dateTo'], 0, 10);
                            $q->$clause(function($dateQ) use ($field, $start, $end) {
                                $dateQ->whereDate($field, '>=', $start)
                                    ->whereDate($field, '<=', $end);
                            });
                        } elseif ($type === 'lessThan') {
                            $q->$clause(\DB::raw("DATE($field)"), '<', substr($value, 0, 10));
                        } elseif ($type === 'greaterThan') {
                            $q->$clause(\DB::raw("DATE($field)"), '>', substr($value, 0, 10));
                        } elseif ($type === 'blank') {
                            $q->$clause($field, '=', NULL);
                        } elseif ($type === 'notBlank'){
                            $q->$clause($field, '!=', NULL);
                        }
                    }
                }
            });
        }

        foreach ($sortModel as $sort) {
            $query->orderBy($sort['colId'], $sort['sort']);
        }

        // if (!str_contains(session()->get('user')->privilege, 'ADMIN')){
        //     $explode = explode(',', session()->get('user')->mitra_om);
        //     if (count($explode) == 0){
        //         $query->where('mitra_om', NULL);
        //     } else {
        //         $query->where(function ($q) use ($explode){
        //             foreach($explode as $key => $e){
        //                 if ($key == 0){
        //                     $q->where('mitra_om', $e);
        //                 } else {
        //                     $q->orWhere('mitra_om', $e);
        //                 }
        //             }
        //         });
        //     }
        // }

        $headers1 = [
            "Status",
            "Name",
            "Docstatus",
            "Aging",
            "Severity",
            "PID",
            "Tower Owner",
            "Portofolio",
            "Region",
            "Category",
            "TT Description",
            "Area",
            "Address",
            "Vendor Mitra OM",
            "Timestamp Closed",
            "Customer",
            "Tower Name",
            "PIC",
            "Timestamp Open",
            "TT ID Tenant",
            "Tenant ID",
            "SiteID from Tenant",
            "Tanggal Request ANT",
            "Timestamp Resolved",
            "Service Level Agreement",
            "Service Level Agreement Status",
            "Actual Category",
            "TT From",
            "TT ID AmpuhC",
            "Class of Service",
            "Maintenance Zone",
            "Fullname Engineer",
            "SLA Status",
            "MTTR",
            "MTTR Hours"
        ];

        $headers = [
            "status",
            "name",
            "docstatus",
            "aging",
            "priority",
            "pid",
            "tower_owner",
            "portofolio",
            "region",
            "issue_type",
            "tt_description",
            "area",
            "address",
            "mitra_om",
            "ts_closed",
            "customer",
            "tower_name",
            "pic",
            "ts_open",
            "tt_ant_id",
            "tenant_id",
            "sid_tenant",
            "tanggal_request_ant",
            "ts_resolved",
            "service_level_agreement",
            "agreement_status",
            "actual_category",
            "reference",
            "tt_id_ampuhc",
            "class_of_service",
            "maintenance_zone",
            "engineer",
            "sla_status",
            "mttr",
            "mttr_hours"
        ];

        $users = $query->get($headers);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        
        
        $sheet->fromArray( $headers1, NULL, 'A1');

        $sheet->fromArray(
            collect($users)->map(function ($row) use ($headers) {
                return array_map(fn($col) => $row->$col ?? '', $headers);
            })->toArray(),
            NULL,
            'A2'
        );

        $latest_modified = DB::connection('pgsql2')->table('mart_om_troubleticketcomp')->select('modified')->orderBy('modified', 'desc')->limit(1)->get();
        if (count($latest_modified) > 0){
            $latest_modified = $latest_modified->first()->modified;
        } else {
            $latest_modified = 'no_data';
        }

        // Response
        $writer = new Xlsx($spreadsheet);
        $fileName = 'trouble_ticket_lastupdate_'.$latest_modified.'.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);
    }

    public function get_list_maintenance_order(Request $request)
    {
        $query = DB::connection('pgsql2')->table('mart_om_maintenanceorder');

        $filterModel = json_decode($request->input('filterModel', '{}'), true);
        $sortModel = json_decode($request->input('sortModel', '[]'), true);

        foreach ($filterModel as $field => $filter) {
            $operator = strtoupper($filter['operator'] ?? 'AND');
            $conditions = $filter['conditions'] ?? [];

            // fallback for single filter (non-multi condition)
            if (empty($conditions) && isset($filter['type'])) {
                $conditions = [$filter];
            }

            $query->where(function ($q) use ($field, $conditions, $operator) {
                foreach ($conditions as $index => $condition) {
                    $type = $condition['type'] ?? null;
                    $filterType = $condition['filterType'] ?? null;

                    // Prepare value
                    $value = $condition['filter'] ?? $condition['dateFrom'] ?? null;

                    // Define logic
                    $clause = ($index === 0 || $operator === 'AND') ? 'where' : 'orWhere';

                    if ($filterType === 'text') {
                        if ($type === 'contains') {
                            $q->$clause($field, 'like', "%$value%");
                        } elseif ($type === 'notContains') {
                            $q->$clause($field, 'not like', "%$value%");
                        } elseif ($type === 'equals') {
                            $q->$clause($field, '=', $value);
                        } elseif ($type === 'notEqual') {
                            $q->$clause($field, '!=', $value);
                        } elseif ($type === 'startsWith') {
                            $q->$clause($field, 'like', "$value%");
                        } elseif ($type === 'endsWith') {
                            $q->$clause($field, 'like', "%$value");
                        } elseif ($type === 'blank') {
                            $q->$clause($field, '=', NULL);
                        } elseif ($type === 'notBlank'){
                            $q->$clause($field, '!=', NULL);
                        }
                    }

                    if ($filterType === 'date') {
                        if ($type === 'equals') {
                            $q->$clause(\DB::raw("DATE($field)"), '=', substr($value, 0, 10));
                        } elseif ($type === 'notEqual') {
                            $q->$clause(\DB::raw("DATE($field)"), '!=', substr($value, 0, 10));
                        }  elseif ($type === 'inRange') {
                            $start = substr($condition['dateFrom'], 0, 10);
                            $end = substr($condition['dateTo'], 0, 10);
                            $q->$clause(function($dateQ) use ($field, $start, $end) {
                                $dateQ->whereDate($field, '>=', $start)
                                    ->whereDate($field, '<=', $end);
                            });
                        } elseif ($type === 'lessThan') {
                            $q->$clause(\DB::raw("DATE($field)"), '<', substr($value, 0, 10));
                        } elseif ($type === 'greaterThan') {
                            $q->$clause(\DB::raw("DATE($field)"), '>', substr($value, 0, 10));
                        } elseif ($type === 'blank') {
                            $q->$clause($field, '=', NULL);
                        } elseif ($type === 'notBlank'){
                            $q->$clause($field, '!=', NULL);
                        }
                    }
                }
            });
        }

        foreach ($sortModel as $sort) {
            $query->orderBy($sort['colId'], $sort['sort']);
        }

        // if (!str_contains(session()->get('user')->privilege, 'ADMIN')){
        //     $explode = explode(',', session()->get('user')->mitra_om);
        //     if (count($explode) == 0){
        //         $query->where('mitra_om', NULL);
        //     } else {
        //         $query->where(function ($q) use ($explode){
        //             foreach($explode as $key => $e){
        //                 if ($key == 0){
        //                     $q->where('mitra_om', $e);
        //                 } else {
        //                     $q->orWhere('mitra_om', $e);
        //                 }
        //             }
        //         });
        //     }
        // }

        $start = intval($request->startRow ?? 0);
        $end = intval($request->endRow ?? 50);
        $limit = $end - $start;

        $filteredQuery = clone $query;
        $totalFiltered = $filteredQuery->count();

        // Get full count (unfiltered)
        $totalAll = DB::connection('pgsql2')->table('mart_om_maintenanceorder');

        // if (!str_contains(session()->get('user')->privilege, 'ADMIN')){
        //     $explode = explode(',', session()->get('user')->mitra_om);
        //     if (count($explode) == 0){
        //         $totalAll->where('mitra_om', NULL);
        //     } else {
        //         $totalAll->where(function ($q) use ($explode){
        //             foreach($explode as $key => $e){
        //                 if ($key == 0){
        //                     $q->where('mitra_om', $e);
        //                 } else {
        //                     $q->orWhere('mitra_om', $e);
        //                 }
        //             }
        //         });
        //     }
        // }
        $totalAll = $totalAll->count();

        $total = $query->count();
        $rows = $query->skip($start)->take($limit)->get();

        return response()->json([
            'rows' => $rows,
            'lastRow' => $totalFiltered,
            'totalFiltered' => $totalFiltered,
            'totalAll' => $totalAll,
        ]);
    }

    public function export_maintenance_order(Request $request)
    {
        $query = DB::connection('pgsql2')->table('mart_om_maintenanceorder');

        $filterModel = json_decode($request->input('filterModel', '{}'), true);
        $sortModel = json_decode($request->input('sortModel', '[]'), true);

        foreach ($filterModel as $field => $filter) {
            $operator = strtoupper($filter['operator'] ?? 'AND');
            $conditions = $filter['conditions'] ?? [];

            // fallback for single filter (non-multi condition)
            if (empty($conditions) && isset($filter['type'])) {
                $conditions = [$filter];
            }

            $query->where(function ($q) use ($field, $conditions, $operator) {
                foreach ($conditions as $index => $condition) {
                    $type = $condition['type'] ?? null;
                    $filterType = $condition['filterType'] ?? null;

                    // Prepare value
                    $value = $condition['filter'] ?? $condition['dateFrom'] ?? null;

                    // Define logic
                    $clause = ($index === 0 || $operator === 'AND') ? 'where' : 'orWhere';

                    if ($filterType === 'text') {
                        if ($type === 'contains') {
                            $q->$clause($field, 'like', "%$value%");
                        } elseif ($type === 'notContains') {
                            $q->$clause($field, 'not like', "%$value%");
                        } elseif ($type === 'equals') {
                            $q->$clause($field, '=', $value);
                        } elseif ($type === 'notEqual') {
                            $q->$clause($field, '!=', $value);
                        } elseif ($type === 'startsWith') {
                            $q->$clause($field, 'like', "$value%");
                        } elseif ($type === 'endsWith') {
                            $q->$clause($field, 'like', "%$value");
                        } elseif ($type === 'blank') {
                            $q->$clause($field, '=', NULL);
                        } elseif ($type === 'notBlank'){
                            $q->$clause($field, '!=', NULL);
                        }
                    }

                    if ($filterType === 'date') {
                        if ($type === 'equals') {
                            $q->$clause(\DB::raw("DATE($field)"), '=', substr($value, 0, 10));
                        } elseif ($type === 'notEqual') {
                            $q->$clause(\DB::raw("DATE($field)"), '!=', substr($value, 0, 10));
                        } elseif ($type === 'inRange') {
                            $start = substr($condition['dateFrom'], 0, 10);
                            $end = substr($condition['dateTo'], 0, 10);
                            $q->$clause(function($dateQ) use ($field, $start, $end) {
                                $dateQ->whereDate($field, '>=', $start)
                                    ->whereDate($field, '<=', $end);
                            });
                        } elseif ($type === 'lessThan') {
                            $q->$clause(\DB::raw("DATE($field)"), '<', substr($value, 0, 10));
                        } elseif ($type === 'greaterThan') {
                            $q->$clause(\DB::raw("DATE($field)"), '>', substr($value, 0, 10));
                        } elseif ($type === 'blank') {
                            $q->$clause($field, '=', NULL);
                        } elseif ($type === 'notBlank'){
                            $q->$clause($field, '!=', NULL);
                        }
                    }
                }
            });
        }

        foreach ($sortModel as $sort) {
            $query->orderBy($sort['colId'], $sort['sort']);
        }

        // if (!str_contains(session()->get('user')->privilege, 'ADMIN')){
        //     $explode = explode(',', session()->get('user')->mitra_om);
        //     if (count($explode) == 0){
        //         $query->where('mitra_om', NULL);
        //     } else {
        //         $query->where(function ($q) use ($explode){
        //             foreach($explode as $key => $e){
        //                 if ($key == 0){
        //                     $q->where('mitra_om', $e);
        //                 } else {
        //                     $q->orWhere('mitra_om', $e);
        //                 }
        //             }
        //         });
        //     }
        // }

        $headers1 = [
            "Status",
            "Name",
            "Docstatus",
            "Expired Visit",
            "Site ID",
            "Return Notes",
            "Schedule Visit",
            "Timestamp Submitted by Vendor",
            "Vendor Mitra OM",
            "Tower Name",
            "Scope of Work",
            "Fullname Engineer",
            "Timestamp Ready to Execute",
            "Timestamp End",
            "Timestamp Approved by Vendor",
            "Timestamp Execution",
            "Timestamp Open",
            "Timestamp Need Assign",
            "Timestamp Approved by SM/KSM",
            "Timestamp Expired",
            "MO ID",
            "pdf_link",
            "Regional",
            "PID",
            "TenantID",
            "Customer",
            "Engineer",
            "District or City",
            "Area",
            "Batch",];

        $headers = [
            "status",
            "name",
            "docstatus",
            "end_date",
            "site_id",
            "return_notes",
            "start_date",
            "ts_submitted_vendor",
            "mitra_om",
            "tower_name",
            "sow",
            "fullname_engineer",
            "ts_ready_to_execute",
            "timestamp_end",
            "ts_approved_vendor",
            "timestamp_start",
            "ts_open",
            "ts_need_assign",
            "ts_approved_sm",
            "ts_expired",
            "mo_reference",
            "pdf_link",
            "regional",
            "pid",
            "tenant_id",
            "customer",
            "engineer",
            "district_or_city",
            "area",
            "batch",];

        $users = $query->get($headers);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        
        
        $sheet->fromArray( $headers1, NULL, 'A1');

        $sheet->fromArray(
            collect($users)->map(function ($row) use ($headers) {
                return array_map(fn($col) => $row->$col ?? '', $headers);
            })->toArray(),
            NULL,
            'A2'
        );

        $latest_modified = DB::connection('pgsql2')->table('mart_om_maintenanceorder')->select('modified')->orderBy('modified', 'desc')->limit(1)->get();
        if (count($latest_modified) > 0){
            $latest_modified = $latest_modified->first()->modified;
        } else {
            $latest_modified = 'no_data';
        }

        // Response
        $writer = new Xlsx($spreadsheet);
        $fileName = 'maintenance_order_lastupdate_'.$latest_modified.'.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);
    }

}
