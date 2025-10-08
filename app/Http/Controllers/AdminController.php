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

        return back()->with('ok', "Queued ingestion #{$row->id} to run now.");
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
        $data['chunk_rows']     = (int)($data['chunk_rows'] ?? 10000);
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
            ->with('ok', "Job #{$row->id} created.");
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
            ->with('ok', "Job #{$ck2ck->id} updated.");
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
            ->with('ok', "Job #{$id} deleted.");
    }

    public function queue_datawarehouse(Ck2CkIngestion $ck2ck)
    {
        // mark as due now; optional: mark status
        $ck2ck->next_run_at = now();
        $ck2ck->last_status = 'queued'; // optional
        $ck2ck->save();

        return back()->with('ok', "Queued ck2ck #{$ck2ck->id} to run now.");
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
}
