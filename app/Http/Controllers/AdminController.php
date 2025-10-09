<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Traits\SSPManageUsers;
use App\Traits\SSPManageAirflowTable;
use DB;
use App\Models\DwhIngestionRegistry;
use Illuminate\Validation\Rule;
use Illuminate\Support\Arr;
use App\Models\Ck2CkIngestion;
use App\Models\Ck2PgIngestion;
class AdminController extends Controller
{
    use SSPManageUsers;
    use SSPManageAirflowTable;

    public function __construct()
    {
        $this->middleware(['session', 'ceklogin', 'cekadmin']);
    }

    public function get_list_manage_users() { return $this->list_manage_users(); }
    public function get_list_manage_airflow_table() { return $this->list_manage_airflow_table(); }


    public function dashboard_admin(Request $request) {
        return view('admin.dashboard_admin');
    }

    public function manage_users(Request $request) {
        return view('admin.manage_users');
    }

    public function detail_users(Request $request, $id = NULL){
        $data['privilege'] = DB::table('master_privilege')->select('*')->get();
        if ($id != NULL){
            $data['u'] = DB::table('users')->select('*')->where('id', $id)->get();
            if (count($data['u']) > 0){
                $data['u'] = $data['u']->first();
                $data['id'] = $id;
                return view('admin.detail_users', compact('data'));
            } else {
                return view('admin.detail_users', compact('data'))->with('error', 'User ID tidak ditemukan! Mulai menambahkan user baru.');
            }
        } else {
            return view('admin.detail_users', compact('data'));
        }
    }

    public function submit_users(Request $request, $id = NULL){
        $nik_tg = $request->session()->get('user')->nik_tg;
        $datetime = date('Y-m-d H:i:s');
        $input = $request->all();
        if (isset($input['status_active'])){
            $status_active = 1;
        } else {
            $status_active = 0;
        }
        if (isset($input['notifikasi'])){
            $notifikasi = 1;
        } else {
            $notifikasi = 0;
        }
        if ($id == NULL){
            if (trim($input['password']) == ''){
                $password = Hash::make('dwhMitratel#135');
            } else {
                $password = Hash::make($input['password']);
            }
            $update = DB::table('users')->insertGetId([
                                            'nik_tg' => $input['nik_tg'],
                                            'name' => $input['name'],
                                            'password' => $password,
                                            'privilege' => $input['privilege'],
                                            'status_active' => $status_active,
                                            'notifikasi' => $notifikasi,
                                            'email' => $input['email'],
                                            'nomor_hp' => $input['nomor_hp'],
                                            'created_at' => $datetime,
                                            'created_by' => $nik_tg,
                                        ]);
            $id = $update;
            $redirect = 0;
        } else {
            $check = DB::table('users')->where('id', $id)->select('*')->get()->first();
            if (trim($input['password']) == ''){
                $password = $check->password;
            } else {
                $password = Hash::make($input['password']);
            }
            $update = DB::table('users')->where('id', $id)
                                        ->update([
                                            'nik_tg' => $input['nik_tg'],
                                            'name' => $input['name'],
                                            'password' => $password,
                                            'privilege' => $input['privilege'],
                                            'status_active' => $status_active,
                                            'notifikasi' => $notifikasi,
                                            'email' => $input['email'],
                                            'nomor_hp' => $input['nomor_hp'],
                                            'updated_at' => $datetime,
                                            'updated_by' => $nik_tg,
                                        ]);
            $redirect = 1;
        }
        $activity = 'Success Update Detail User DWH Monitoring ID '.$id.', NIK TG '.$input['nik_tg'];
        $status = 'SUCCESS';
        DB::table('log')->insert([
            'nik_tg' => $nik_tg,
            'activity' => $activity,
            'status' => $status,
            'datetime' => $datetime
        ]);
        if ($redirect){
            return redirect()->route('detail_users', ['id' => $id])->with('success', 'Update user berhasil.');
        } else {
            return redirect()->route('detail_users')->with('success', 'Update user berhasil.');
        }
    }

    public function delete_users(Request $request, $id){
        $nik_tg = $request->session()->get('user')->nik_tg;
        $datetime = date('Y-m-d H:i:s');
        $delete = DB::table('users')->where('id', $id)->delete();
        $activity = 'Delete User DWH ID '.$id;
        $status = 'SUCCESS';
        DB::table('log')->insert([
                        'nik_tg' => $nik_tg,
                        'activity' => $activity,
                        'status' => $status,
                        'datetime' => $datetime
                    ]);
        $request->session()->flash('success', 'User DWH berhasil dihapus.');
        return 1;
    }

    public function manage_airflow_table(Request $request) {
        return view('admin.manage_airflow_table');
    }

    public function detail_airflow_table(Request $request, $id = NULL){
        $data = [];
        if ($id != NULL){
            $data['u'] = DB::table('airflow_table')->select('*')->where('id', $id)->get();
            if (count($data['u']) > 0){
                $data['u'] = $data['u']->first();
                $data['id'] = $id;
                return view('admin.detail_airflow_table', compact('data'));
            } else {
                return view('admin.detail_airflow_table', compact('data'))->with('error', 'Table ID tidak ditemukan! Mulai menambahkan user baru.');
            }
        } else {
            return view('admin.detail_airflow_table', compact('data'));
        }
    }

    public function submit_airflow_table(Request $request, $id = NULL){
        $nik_tg = $request->session()->get('user')->nik_tg;
        $datetime = date('Y-m-d H:i:s');
        $input = $request->all();
        if ($id == NULL){
            $update = DB::table('airflow_table')->insertGetId([
                                            'table_airflow' => $input['table'],
                                            'deskripsi' => $input['deskripsi'],
                                            'type_table' => $input['type_table'],
                                            'created_at' => $datetime,
                                            'created_by' => $nik_tg,
                                        ]);
            $id = $update;
            $redirect = 0;
        } else {
            $update = DB::table('airflow_table')->where('id', $id)
                                        ->update([
                                            'table_airflow' => $input['table'],
                                            'deskripsi' => $input['deskripsi'],
                                            'type_table' => $input['type_table'],
                                            'updated_at' => $datetime,
                                            'updated_by' => $nik_tg,
                                        ]);
            $redirect = 1;
        }
        $activity = 'Success Update Detail Airflow Table ID '.$id.', Table '.$input['table'];
        $status = 'SUCCESS';
        DB::table('log')->insert([
            'nik_tg' => $nik_tg,
            'activity' => $activity,
            'status' => $status,
            'datetime' => $datetime
        ]);
        if ($redirect){
            return redirect()->route('detail_airflow_table', ['id' => $id])->with('success', 'Update table berhasil.');
        } else {
            return redirect()->route('detail_airflow_table')->with('success', 'Update table berhasil.');
        }
    }

    public function delete_airflow_table(Request $request, $id){
        $nik_tg = $request->session()->get('user')->nik_tg;
        $datetime = date('Y-m-d H:i:s');
        $delete = DB::table('airflow_table')->where('id', $id)->delete();
        $activity = 'Delete Airflow Table DWH ID '.$id;
        $status = 'SUCCESS';
        DB::table('log')->insert([
                        'nik_tg' => $nik_tg,
                        'activity' => $activity,
                        'status' => $status,
                        'datetime' => $datetime
                    ]);
        $request->session()->flash('success', 'Table DWH berhasil dihapus.');
        return 1;
    }

    // GET /admin/manage_datalake
    public function index_datalake(Request $request)
    {
        // Safe, version-agnostic
        $q = trim((string) $request->query('q', ''));
        $f = (array) $request->input('filter', []);

        $rows = DwhIngestionRegistry::query()
            // global search
            ->when($q !== '', function ($qbuilder) use ($q) {
                $qbuilder->where(function ($qq) use ($q) {
                    $like = "%{$q}%";
                    $qq->where('source_db', 'like', $like)
                    ->orWhere('source_table', 'like', $like)
                    ->orWhere('source_mysql_conn_id', 'like', $like)
                    ->orWhere('target_db', 'like', $like)
                    ->orWhere('target_table', 'like', $like)
                    ->orWhere('target_ch_conn_id', 'like', $like);
                });
            })

            // per-column filters
            ->when(($id = Arr::get($f, 'id')), fn($qq) => $qq->where('id', (int) $id))
            ->when(array_key_exists('enabled', $f) && $f['enabled'] !== '',
                fn($qq) => $qq->where('enabled', (int) $f['enabled'])
            )
            ->when(($v = Arr::get($f, 'source')), function ($qq) use ($v) {
                $like = "%{$v}%";
                $qq->where(function ($w) use ($like) {
                    $w->where('source_db', 'like', $like)
                    ->orWhere('source_table', 'like', $like)
                    ->orWhere('source_mysql_conn_id', 'like', $like);
                });
            })
            ->when(($v = Arr::get($f, 'target')), function ($qq) use ($v) {
                $like = "%{$v}%";
                $qq->where(function ($w) use ($like) {
                    $w->where('target_db', 'like', $like)
                    ->orWhere('target_table', 'like', $like)
                    ->orWhere('target_ch_conn_id', 'like', $like);
                });
            })
            ->when(($v = Arr::get($f, 'schedule_type')), fn($qq) => $qq->where('schedule_type', $v))
            ->when(($v = Arr::get($f, 'schedule_text')), function ($qq) use ($v) {
                $like = "%{$v}%";
                $qq->where(function ($w) use ($like) {
                    $w->where('interval_minutes', 'like', $like)
                    ->orWhere('cron_expr', 'like', $like);
                });
            })
            ->when(($v = Arr::get($f, 'last_status')), fn($qq) => $qq->where('last_status', 'like', "%{$v}%"))
            ->when(($v = Arr::get($f, 'next_run_from')), fn($qq) => $qq->whereDate('next_run_at', '>=', $v))
            ->when(($v = Arr::get($f, 'next_run_to')), fn($qq) => $qq->whereDate('next_run_at', '<=', $v))

            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.manage_datalake', [
            'rows' => $rows,
            'q'    => $q,
        ]);
    }

    // GET /admin/manage_datalake/create
    public function create_datalake()
    {
        return view('admin.detail_datalake', [
            'data' => [] // form expects $data
        ]);
    }

    // POST /admin/manage_datalake
    public function store_datalake(Request $request)
    {
        $validated = $this->validatePayloadDatalake($request);

        // Normalize checkboxes & defaults
        $validated['enabled'] = $request->boolean('enabled');
        $this->normalizeScheduleDatalake($validated);

        $row = DwhIngestionRegistry::create($validated);

        return redirect()
            ->to('/admin/manage_datalake/'.$row->id.'/edit')
            ->with('success', 'Ingestion registry created.');
    }

    // GET /admin/manage_datalake/{id}/edit
    public function edit_datalake($id)
    {
        $row = DwhIngestionRegistry::findOrFail($id);
        return view('admin.detail_datalake', [
            'data' => ['id' => $row->id, 'row' => $row],
        ]);
    }

    // PUT /admin/manage_datalake/{id}
    public function update_datalake(Request $request, $id)
    {
        $row = DwhIngestionRegistry::findOrFail($id);

        $validated = $this->validatePayloadDatalake($request);
        $validated['enabled'] = $request->boolean('enabled');
        $this->normalizeScheduleDatalake($validated);

        $row->fill($validated)->save();

        return redirect()
            ->to('/admin/manage_datalake/'.$row->id.'/edit')
            ->with('success', 'Ingestion registry updated.');
    }

    // DELETE /admin/manage_datalake/{id}
    public function destroy_datalake($id)
    {
        $row = DwhIngestionRegistry::findOrFail($id);
        $row->delete();

        return redirect()
            ->to('/admin/manage_datalake')
            ->with('success', 'Ingestion registry deleted.');
    }

    // PATCH /admin/manage_datalake/{id}/toggle
    public function toggle_datalake($id)
    {
        $row = DwhIngestionRegistry::findOrFail($id);
        $row->enabled = ! $row->enabled;
        $row->save();

        return back()->with('success', 'Status toggled to '.($row->enabled ? 'Enabled' : 'Disabled').'.');
    }

    public function queue_datalake($id)
    {
        $row = DwhIngestionRegistry::findOrFail($id);
        // mark as due now; optional: mark status
        $row->next_run_at = now();
        $row->last_status = 'queued'; // optional
        $row->save();

        return back()->with('success', "Queued ingestion #{$row->id} to run now.");
    }

    // --------- helpers ---------

    private function validatePayloadDatalake(Request $request): array
    {
        $rules = [
            'enabled'               => ['nullable'],
            // conn ids
            'source_mysql_conn_id'  => ['required','string','max:255'],
            'target_ch_conn_id'     => ['required','string','max:255'],
            'pg_log_conn_id'        => ['nullable','string','max:255'],
            // source & target
            'source_db'             => ['required','string','max:255'],
            'source_table'          => ['required','string','max:255'],
            'target_db'             => ['required','string','max:255'],
            'target_table'          => ['required','string','max:255'],
            // keys
            'pk_col'                => ['required','string','max:255'],
            'version_col'           => ['nullable','string','max:255'],
            // schedule
            'schedule_type'         => ['required', Rule::in(['interval','cron'])],
            'interval_minutes'      => ['nullable','integer','min:1'],
            'cron_expr'             => ['nullable','string','max:255'],
            // perf
            'chunk_rows'            => ['nullable','integer','min:1000'],
            'max_parallel'          => ['nullable','integer','min:1','max:64'],
            'tmp_dir'               => ['nullable','string','max:512'],
            'ndjson_prefix'         => ['nullable','string','max:255'],
            // logs
            'log_table'             => ['nullable','string','max:255'],
            'log_type'              => ['nullable','string','max:255'],
            'log_kategori'          => ['nullable','string','max:255'],
        ];

        $validated = $request->validate($rules, [
            'schedule_type.in'          => 'Schedule type must be interval or cron.',
            'interval_minutes.min'      => 'Interval must be at least 1 minute.',
            'chunk_rows.min'            => 'Chunk rows must be at least 1000.',
            'max_parallel.max'          => 'Max parallel cannot exceed 64.',
        ]);

        // Conditional required
        $st = $validated['schedule_type'] ?? 'interval';
        if ($st === 'interval' && empty($validated['interval_minutes'])) {
            return back()->withErrors(['interval_minutes' => 'Interval minutes is required for interval schedule.'])
                         ->withInput()->throwResponse();
        }
        if ($st === 'cron' && empty($validated['cron_expr'])) {
            return back()->withErrors(['cron_expr' => 'Cron expression is required for cron schedule.'])
                         ->withInput()->throwResponse();
        }

        return $validated;
    }

    private function normalizeScheduleDatalake(array &$data): void
    {
        // defaults
        $data['pg_log_conn_id'] = $data['pg_log_conn_id'] ?? 'airflow_logs_mitratel';
        $data['chunk_rows']     = (int)($data['chunk_rows'] ?? 100000);
        $data['max_parallel']   = (int)($data['max_parallel'] ?? 4);
        $data['tmp_dir']        = $data['tmp_dir'] ?? '/tmp';
        $data['ndjson_prefix']  = $data['ndjson_prefix'] ?? 'DL_generic_';
        $data['log_table']      = $data['log_table'] ?? 'airflow_logs';
        $data['log_type']       = $data['log_type'] ?? 'incremental';
        $data['log_kategori']   = $data['log_kategori'] ?? 'Data Lake';

        if (($data['schedule_type'] ?? 'interval') === 'interval') {
            $data['cron_expr'] = null;
            $data['interval_minutes'] = (int)($data['interval_minutes'] ?? 15);
        } else {
            $data['interval_minutes'] = null;
            // you can add extra cron validation here if needed
        }
    }

    public function index_datawarehouse(Request $request)
    {
        $q = Ck2CkIngestion::query();

        if ($search = $request->get('q')) {
            $q->where(function($w) use ($search) {
                $w->where('target_table', 'ilike', "%{$search}%")
                  ->orWhere('target_db', 'ilike', "%{$search}%")
                  ->orWhere('src_database', 'ilike', "%{$search}%")
                  ->orWhere('source_ch_conn_id', 'ilike', "%{$search}%")
                  ->orWhere('target_ch_conn_id', 'ilike', "%{$search}%");
            });
        }

        $rows = $q->orderBy('id', 'desc')->paginate(20)->withQueryString();

        return view('admin.manage_datawarehouse', compact('rows'));
    }

    public function create_datawarehouse()
    {
        $row = new Ck2CkIngestion([
            'enabled' => true,
            'schedule_type' => 'interval',
            'interval_minutes' => 15,
            'parallel_slices' => 4,
            'page_rows' => 50000,
            'allow_drop_columns' => true,
            'truncate_before_load' => false,
            'log_type' => 'incremental',
            'log_kategori' => 'Data Warehouse',
        ]);
        return view('admin.detail_datawarehouse', compact('row'));
    }

    public function store_datawarehouse(Request $request)
    {
        $data = $this->validatedDatawarehouse($request);

        // Normalize checkboxes (if unchecked, send 0)
        $data['enabled'] = (bool)($data['enabled'] ?? false);
        $data['allow_drop_columns'] = (bool)($data['allow_drop_columns'] ?? false);
        $data['truncate_before_load'] = (bool)($data['truncate_before_load'] ?? false);

        $row = Ck2CkIngestion::create($data);

        return redirect()
            ->route('manage_datawarehouse')
            ->with('success', "Job #{$row->id} created.");
    }

    public function edit_datawarehouse(Ck2CkIngestion $ck2ck)
    {
        $row = $ck2ck;
        return view('admin.detail_datawarehouse', compact('row'));
    }

    public function update_datawarehouse(Request $request, Ck2CkIngestion $ck2ck)
    {
        $data = $this->validatedDatawarehouse($request);

        $data['enabled'] = (bool)($data['enabled'] ?? false);
        $data['allow_drop_columns'] = (bool)($data['allow_drop_columns'] ?? false);
        $data['truncate_before_load'] = (bool)($data['truncate_before_load'] ?? false);

        $ck2ck->update($data);

        return redirect()
            ->route('manage_datawarehouse')
            ->with('success', "Job #{$ck2ck->id} updated.");
    }

    // PATCH /admin/manage_datawarehouse/{id}/toggle
    public function toggle_datawarehouse($id)
    {
        $row = Ck2CkIngestion::findOrFail($id);
        $row->enabled = ! $row->enabled;
        $row->save();

        return back()->with('success', 'Status toggled to '.($row->enabled ? 'Enabled' : 'Disabled').'.');
    }

    public function destroy_datawarehouse(Ck2CkIngestion $ck2ck)
    {
        $id = $ck2ck->id;
        $ck2ck->delete();

        return redirect()
            ->route('manage_datawarehouse')
            ->with('success', "Job #{$id} deleted.");
    }

    public function queue_datawarehouse(Ck2CkIngestion $ck2ck)
    {
        // mark as due now; optional: mark status
        $ck2ck->next_run_at = now();
        $ck2ck->last_status = 'queued'; // optional
        $ck2ck->save();

        return back()->with('success', "Queued ck2ck #{$ck2ck->id} to run now.");
    }

    private function validatedDatawarehouse(Request $request): array
    {
        $rules = [
            'enabled'               => ['nullable', 'boolean'],

            'source_ch_conn_id'     => ['required', 'string', 'max:255'],
            'target_ch_conn_id'     => ['required', 'string', 'max:255'],
            'pg_log_conn_id'        => ['required', 'string', 'max:255'],

            'src_database'          => ['required', 'string', 'max:255'],
            'src_sql'               => ['required', 'string'],

            'target_db'             => ['required', 'string', 'max:255'],
            'target_table'          => ['required', 'string', 'max:255'],

            'pk_cols'               => ['required', 'string', 'max:1000'], // e.g. "id" or "ticket_id,site_id"
            'version_col'           => ['nullable', 'string', 'max:255'],

            'schedule_type'         => ['required', Rule::in(['interval','cron'])],
            'interval_minutes'      => ['nullable', 'integer', 'min:1', 'max:10080'], // up to 7 days
            'cron_expr'             => ['nullable', 'string', 'max:255'],

            'parallel_slices'       => ['required', 'integer', 'min:1', 'max:128'],
            'page_rows'             => ['required', 'integer', 'min:1', 'max:1000000'],

            'allow_drop_columns'    => ['nullable', 'boolean'],
            'truncate_before_load'  => ['nullable', 'boolean'],

            'pre_sql'               => ['nullable', 'string'],
            'post_sql'              => ['nullable', 'string'],

            'log_table'             => ['required', 'string', 'max:255'],
            'log_type'              => ['required', 'string', 'max:255'],
            'log_kategori'          => ['required', 'string', 'max:255'],

            // optional manual tweaks
            'last_run_at'           => ['nullable', 'date'],
            'next_run_at'           => ['nullable', 'date'],
            'last_status'           => ['nullable', 'string', 'max:255'],
            'last_error'            => ['nullable', 'string'],
        ];

        // If schedule_type = cron, cron_expr is required
        if ($request->input('schedule_type') === 'cron') {
            $rules['cron_expr'][] = 'required';
        } else {
            // interval required
            $rules['interval_minutes'][] = 'required';
        }

        return $request->validate($rules);
    }

    // ===================== Datamart (ClickHouse -> PostgreSQL) =====================

    /**
     * GET /admin/manage_datamart
     * List with search + filters, returns view: admin.manage_datamart
     */
    public function index_datamart(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $f = (array) $request->input('filter', []);

        $rows = Ck2PgIngestion::query()
            // global search
            ->when($q !== '', function ($qb) use ($q) {
                $like = "%{$q}%";
                $qb->where(function ($w) use ($like) {
                    $w->where('source_db', 'ilike', $like)
                    ->orWhere('source_table', 'ilike', $like)
                    ->orWhere('source_ch_conn_id', 'ilike', $like)
                    ->orWhere('target_schema', 'ilike', $like)
                    ->orWhere('target_table', 'ilike', $like)
                    ->orWhere('target_pg_conn_id', 'ilike', $like);
                });
            })
            // per-column filters (matching the Blade filter row)
            ->when(($v = $f['id'] ?? null), fn($qb) => $qb->where('id', (int) $v))
            ->when(array_key_exists('enabled', $f) && $f['enabled'] !== '',
                fn($qb) => $qb->where('enabled', (int) $f['enabled'])
            )
            ->when(($v = $f['source_db'] ?? null),        fn($qb) => $qb->where('source_db', 'ilike', "%{$v}%"))
            ->when(($v = $f['source_table'] ?? null),     fn($qb) => $qb->where('source_table', 'ilike', "%{$v}%"))
            ->when(($v = $f['source_ch_conn_id'] ?? null),fn($qb) => $qb->where('source_ch_conn_id', 'ilike', "%{$v}%"))
            ->when(($v = $f['target_schema'] ?? null),    fn($qb) => $qb->where('target_schema', 'ilike', "%{$v}%"))
            ->when(($v = $f['target_table'] ?? null),     fn($qb) => $qb->where('target_table', 'ilike', "%{$v}%"))
            ->when(($v = $f['target_pg_conn_id'] ?? null),fn($qb) => $qb->where('target_pg_conn_id', 'ilike', "%{$v}%"))
            ->when(($v = $f['cursor_col'] ?? null),       fn($qb) => $qb->where('cursor_col', 'ilike', "%{$v}%"))
            ->when(($v = $f['schedule_type'] ?? null),    fn($qb) => $qb->where('schedule_type', $v))
            ->when(($v = $f['schedule_text'] ?? null), function ($qb) use ($v) {
                $like = "%{$v}%";
                $qb->where(function ($w) use ($like) {
                    $w->where('interval_minutes', '::text ilike', $like) // PG casts handled below if needed
                    ->orWhere('cron_expr', 'ilike', $like);
                });
            })
            ->when(($v = $f['chunk_rows'] ?? null),       fn($qb) => $qb->where('chunk_rows', (int) $v))
            ->when(($v = $f['max_parallel'] ?? null),     fn($qb) => $qb->where('max_parallel', (int) $v))
            ->when(($v = $f['last_status'] ?? null),      fn($qb) => $qb->where('last_status', 'ilike', "%{$v}%"))

            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.manage_datamart', compact('rows','q'));
    }

    /**
     * GET /admin/manage_datamart/create
     * Return a blank form view (non-modal) for creating a job.
     * View suggestion: resources/views/admin/detail_datamart.blade.php
     */
    public function create_datamart()
    {
        // default values
        $row = new Ck2PgIngestion([
            'enabled' => true,
            'schedule_type' => 'interval',
            'interval_minutes' => 30,
            'chunk_rows' => 100000,
            'max_parallel' => 4,
            'drop_extra_columns' => true,
            'copy_timeout_seconds' => 7200,
            'log_type' => 'incremental',
            'log_kategori' => 'Data Mart',
            'log_table' => 'airflow_logs',
        ]);
        return view('admin.detail_datamart', compact('row'));
    }

    /**
     * POST /admin/manage_datamart
     */
    public function store_datamart(Request $request)
    {
        $nik_tg = $request->session()->get('user')->nik_tg ?? 'system';
        $now    = date('Y-m-d H:i:s');

        $data = $this->validatePayloadCk2pg($request);

        // normalize checkboxes
        $data['enabled'] = (bool)($data['enabled'] ?? false);
        $data['drop_extra_columns'] = (bool)($data['drop_extra_columns'] ?? false);

        // schedule normalize
        $this->normalizeScheduleCk2pg($data);

        // Normalize pk_value (trim spaces)
        if (!empty($data['pk_value'])) {
            $data['pk_value'] = collect(explode(',', $data['pk_value']))
                ->map(fn($s) => trim($s))
                ->filter()
                ->implode(',');
        }

        $row = Ck2PgIngestion::create($data);

        DB::table('log')->insert([
            'nik_tg'   => $nik_tg,
            'activity' => "Create Datamart registry ID {$row->id}",
            'status'   => 'SUCCESS',
            'datetime' => $now,
        ]);

        return redirect()
            ->route('edit_datamart', $row->id)
            ->with('success', 'Ingestion registry created.');
    }

    /**
     * GET /admin/manage_datamart/{id}/edit
     */
    public function edit_datamart($id)
    {
        $row = Ck2PgIngestion::findOrFail($id);
        return view('admin.detail_datamart', compact('row'));
    }

    /**
     * PUT /admin/manage_datamart/{id}
     */
    public function update_datamart(Request $request, $id)
    {
        $nik_tg = $request->session()->get('user')->nik_tg ?? 'system';
        $now    = date('Y-m-d H:i:s');

        $row = Ck2PgIngestion::findOrFail($id);

        $data = $this->validatePayloadCk2pg($request);

        $data['enabled'] = (bool)($data['enabled'] ?? false);
        $data['drop_extra_columns'] = (bool)($data['drop_extra_columns'] ?? false);

        $this->normalizeScheduleCk2pg($data);

        // Normalize pk_value (trim spaces)
        if (!empty($data['pk_value'])) {
            $data['pk_value'] = collect(explode(',', $data['pk_value']))
                ->map(fn($s) => trim($s))
                ->filter()
                ->implode(',');
        }

        $row->fill($data)->save();

        DB::table('log')->insert([
            'nik_tg'   => $nik_tg,
            'activity' => "Update Datamart registry ID {$row->id}",
            'status'   => 'SUCCESS',
            'datetime' => $now,
        ]);

        return redirect()
            ->route('edit_datamart', $row->id)
            ->with('success', 'Ingestion registry updated.');
    }

    /**
     * DELETE /admin/manage_datamart/{id}
     */
    public function destroy_datamart($id)
    {
        $nik_tg = request()->session()->get('user')->nik_tg ?? 'system';
        $now    = date('Y-m-d H:i:s');

        $row = Ck2PgIngestion::findOrFail($id);
        $row->delete();

        DB::table('log')->insert([
            'nik_tg'   => $nik_tg,
            'activity' => "Delete Datamart registry ID {$id}",
            'status'   => 'SUCCESS',
            'datetime' => $now,
        ]);

        return redirect()
            ->route('manage_datamart')
            ->with('success', "Job #{$id} deleted.");
    }

    /**
     * PATCH /admin/manage_datamart/{id}/toggle
     */
    public function toggle_datamart($id)
    {
        $row = Ck2PgIngestion::findOrFail($id);
        $row->enabled = ! $row->enabled;
        $row->save();

        return back()->with('success', 'Status toggled to '.($row->enabled ? 'Enabled' : 'Disabled').'.');
    }

    /**
     * PATCH /admin/manage_datamart/{id}/queue
     */
    public function queue_datamart($id)
    {
        $row = Ck2PgIngestion::findOrFail($id);
        $row->next_run_at = now();
        $row->last_status = 'queued';
        $row->last_error  = null;
        $row->save();

        return back()->with('success', "Queued job #{$row->id} to run on next DAG tick.");
    }

    // ----------------------- helpers -----------------------

    private function validatePayloadCk2pg(Request $request): array
    {
        $rules = [
            'enabled' => ['required','boolean'],
            'source_ch_conn_id' => ['required','string'],

            'source_sql' => ['required','string'],  // query-only now
            'pk_value'   => ['nullable','string'],  // "id" or "id,name"
            'cursor_col' => ['nullable','string'],

            'target_pg_conn_id' => ['required','string'],
            'target_schema' => ['required','string'],
            'target_table'  => ['required','string'],

            'schedule_type' => ['nullable','in:interval,cron'],
            'interval_minutes' => ['nullable','integer','min:1'],
            'cron_expr' => ['nullable','string'],

            'chunk_rows' => ['nullable','integer','min:100'],
            'max_parallel' => ['nullable','integer','min:1'],
            'drop_extra_columns' => ['nullable','boolean'],
            'copy_timeout_seconds' => ['nullable','integer','min:1'],
            'insert_page_size' => ['nullable','integer','min:100','max:20000'],
            'commit_every_chunks' => ['nullable','integer','min:1'],

            'pg_log_conn_id' => ['required','string'],
            'log_table' => ['required','string'],
            'log_type' => ['required','string'],
            'log_kategori' => ['required','string'],
        ];

        // If schedule_type = cron, cron_expr required. Else interval required.
        if ($request->input('schedule_type') === 'cron') {
            $rules['cron_expr'][] = 'required';
        } else {
            $rules['interval_minutes'][] = 'required';
        }

        return $request->validate($rules);
    }

    private function normalizeScheduleCk2pg(array &$data): void
    {
        if (($data['schedule_type'] ?? 'interval') === 'interval') {
            $data['interval_minutes'] = (int)($data['interval_minutes'] ?? 30);
            $data['cron_expr'] = '*/15 * * * *';
        } else {
            $data['cron_expr'] = $data['cron_expr'] ?? '*/15 * * * *';
            $data['interval_minutes'] = 15;
        }
    }

}
