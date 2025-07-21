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
                        }
                    }

                    if ($filterType === 'date') {
                        if ($type === 'equals') {
                            $q->$clause(\DB::raw("DATE($field)"), '=', substr($value, 0, 10));
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
                        }
                    }
                }
            });
        }

        foreach ($sortModel as $sort) {
            $query->orderBy($sort['colId'], $sort['sort']);
        }

        $start = intval($request->startRow ?? 0);
        $end = intval($request->endRow ?? 50);
        $limit = $end - $start;

        $filteredQuery = clone $query;
        $totalFiltered = $filteredQuery->count();

        // Get full count (unfiltered)
        $totalAll = DB::connection('pgsql2')->table('mart_om_troubleticketcomp')->count();

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
                        }
                    }

                    if ($filterType === 'date') {
                        if ($type === 'equals') {
                            $q->$clause(\DB::raw("DATE($field)"), '=', substr($value, 0, 10));
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
                        }
                    }
                }
            });
        }

        foreach ($sortModel as $sort) {
            $query->orderBy($sort['colId'], $sort['sort']);
        }

        $headers = ["name",
            "creation",
            "modified",
            "modified_by",
            "owner",
            "docstatus",
            "parent",
            "parentfield",
            "parenttype",
            "idx",
            "status",
            "tt_ant_id",
            "mitra_om",
            "engineer",
            "customer",
            "tower_id",
            "pid",
            "priority",
            "disctrict_city",
            "region",
            "tt_description",
            "ts_open",
            "ts_need_assign",
            "ts_on_progress",
            "ts_pickup",
            "ts_departure",
            "ts_arrived",
            "ts_resolved",
            "ts_closed",
            "pic",
            "detail_issue_type",
            "issue_type",
            "reference",
            "tower_name",
            "tower_owner",
            "actual_category",
            "closed_by",
            "sla_status",
            "mttr_hours"];

        $users = $query->get($headers);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        
        
        $sheet->fromArray(array_map('ucfirst', $headers), NULL, 'A1');

        $sheet->fromArray(
            collect($users)->map(function ($row) use ($headers) {
                return array_map(fn($col) => $row->$col ?? '', $headers);
            })->toArray(),
            NULL,
            'A2'
        );

        // Response
        $writer = new Xlsx($spreadsheet);
        $fileName = 'trouble_ticket.xlsx';
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
                        }
                    }

                    if ($filterType === 'date') {
                        if ($type === 'equals') {
                            $q->$clause(\DB::raw("DATE($field)"), '=', substr($value, 0, 10));
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
                        }
                    }
                }
            });
        }

        foreach ($sortModel as $sort) {
            $query->orderBy($sort['colId'], $sort['sort']);
        }

        $start = intval($request->startRow ?? 0);
        $end = intval($request->endRow ?? 50);
        $limit = $end - $start;

        $filteredQuery = clone $query;
        $totalFiltered = $filteredQuery->count();

        // Get full count (unfiltered)
        $totalAll = DB::connection('pgsql2')->table('mart_om_maintenanceorder')->count();

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
                        }
                    }

                    if ($filterType === 'date') {
                        if ($type === 'equals') {
                            $q->$clause(\DB::raw("DATE($field)"), '=', substr($value, 0, 10));
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
                        }
                    }
                }
            });
        }

        foreach ($sortModel as $sort) {
            $query->orderBy($sort['colId'], $sort['sort']);
        }

        $headers = [
            "name",
            "creation",
            "modified",
            "modified_by",
            "owner",
            "docstatus",
            "parent",
            "parentfield",
            "parenttype",
            "idx",
            "status",
            "mo_reference",
            "batch",
            "year",
            "funcloc",
            "tenant_id",
            "site_id",
            "site_id_tenant",
            "pid",
            "tower_name",
            "mo_type",
            "type",
            "mitra_om",
            "engineer",
            "visitor_permit",
            "province",
            "district_or_city",
            "address",
            "area",
            "regional",
            "maintenance_zone",
            "lat_long",
            "kode_tipe_site",
            "portofolio",
            "sow",
            "start_date",
            "return_schedule_notes",
            "timestamp_end",
            "ts_open",
            "ts_request_schedule",
            "ts_ready_to_execute",
            "timestamp_start",
            "ts_submitted_vendor",
            "order_number",
            "mp_number",
            "mi_number",
            "pdf_link",
            "pr_reference",
            "po_reference",
            "bapp_number",
            "bast_number",
            "gr_status",
            "customer"];

        $users = $query->get($headers);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        
        
        $sheet->fromArray(array_map('ucfirst', $headers), NULL, 'A1');

        $sheet->fromArray(
            collect($users)->map(function ($row) use ($headers) {
                return array_map(fn($col) => $row->$col ?? '', $headers);
            })->toArray(),
            NULL,
            'A2'
        );

        // Response
        $writer = new Xlsx($spreadsheet);
        $fileName = 'maintenance_order.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);
    }
}
