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
use Illuminate\Support\Str;
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
        // Explicitly read GET params (avoid '' -> 0 pitfalls)
        $q = trim((string) $request->query('q', ''));
        $f = (array) $request->query('filter', []);

        // Helpers
        $hasNonEmpty = function (string $k) use ($f): bool {
            return array_key_exists($k, $f) && trim((string) $f[$k]) !== '';
        };
        $ciLike = function ($qb, string $column, ?string $value): void {
            if ($value === null || trim($value) === '') return;
            $qb->whereRaw("LOWER({$column}) LIKE ?", ['%' . Str::lower(trim($value)) . '%']);
        };
        $ciOrLike = function ($qb, string $column, ?string $value): void {
            if ($value === null || trim($value) === '') return;
            $qb->orWhereRaw("LOWER({$column}) LIKE ?", ['%' . Str::lower(trim($value)) . '%']);
        };

        $qb = DwhIngestionRegistry::query();

        /* ========== Global search (top search box) ========== */
        if ($q !== '') {
            $qb->where(function ($w) use ($q, $ciLike, $ciOrLike) {
                $ciLike($w, 'source_db',            $q);
                $ciOrLike($w, 'source_table',         $q);
                $ciOrLike($w, 'source_mysql_conn_id', $q);
                $ciOrLike($w, 'target_db',            $q);
                $ciOrLike($w, 'target_table',         $q);
                $ciOrLike($w, 'target_ch_conn_id',    $q);
            });
        }

        /* ========== Per-column filters ========== */

        // id (only when non-empty; avoid '' -> 0)
        if ($hasNonEmpty('id')) {
            $qb->where('id', (int) $f['id']);
        }

        // enabled (allow '0'/'1', skip only empty string)
        if ($hasNonEmpty('enabled')) {
            $qb->where('enabled', (int) $f['enabled']);
        }

        // SOURCE group: source_db / source_table / source_mysql_conn_id (contains)
        if ($hasNonEmpty('source')) {
            $v = $f['source'];
            $qb->where(function ($w) use ($v, $ciLike, $ciOrLike) {
                $ciLike($w, 'source_db',            $v);
                $ciOrLike($w, 'source_table',         $v);
                $ciOrLike($w, 'source_mysql_conn_id', $v);
            });
        }

        // TARGET group: target_db / target_table / target_ch_conn_id (contains)
        if ($hasNonEmpty('target')) {
            $v = $f['target'];
            $qb->where(function ($w) use ($v, $ciLike, $ciOrLike) {
                $ciLike($w, 'target_db',         $v);
                $ciOrLike($w, 'target_table',      $v);
                $ciOrLike($w, 'target_ch_conn_id', $v);
            });
        }

        // Schedule: type / interval_minutes / cron_expr
        if ($hasNonEmpty('schedule_type')) {
            $qb->where('schedule_type', $f['schedule_type']); // 'interval' | 'cron'
        }
        if ($hasNonEmpty('interval_minutes')) {
            $qb->where('interval_minutes', (int) $f['interval_minutes']);
        }
        if ($hasNonEmpty('cron_expr')) {
            $ciLike($qb, 'cron_expr', $f['cron_expr']);
        }

        // Back-compat: if you still send 'schedule_text', search both fields
        if (!$hasNonEmpty('interval_minutes') && !$hasNonEmpty('cron_expr') && $hasNonEmpty('schedule_text')) {
            $v = trim($f['schedule_text']);
            $qb->where(function ($w) use ($v, $ciLike) {
                // If numeric-ish, match interval exactly too
                if (ctype_digit($v)) {
                    $w->orWhere('interval_minutes', (int) $v);
                }
                $ciLike($w, 'cron_expr', $v);
            });
        }

        // last_status (contains)
        if ($hasNonEmpty('last_status')) {
            $ciLike($qb, 'last_status', $f['last_status']);
        }

        // next_run_at range
        if ($hasNonEmpty('next_run_from')) {
            $qb->whereDate('next_run_at', '>=', $f['next_run_from']);
        }
        if ($hasNonEmpty('next_run_to')) {
            $qb->whereDate('next_run_at', '<=', $f['next_run_to']);
        }

        /* ========== Optional: debug SQL & bindings ========== */
        if ($request->query('debug') === '1') {
            $clone = clone $qb;
            dd($clone->toSql(), $clone->getBindings(), $f);
        }

        /* ========== Return all rows if everything is empty ========== */
        $allEmpty = ($q === '') && collect($f)->every(fn($v) => $v === '' || $v === null);
        if ($allEmpty) {
            $rows = DwhIngestionRegistry::query()
                ->orderByDesc('id')
                ->paginate(15)
                ->withQueryString();

            return view('admin.manage_datalake', [
                'rows' => $rows,
                'q'    => $q,
            ]);
        }

        $rows = $qb->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

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
        // Read GET params explicitly
        $q = trim((string) $request->query('q', ''));
        $f = (array) $request->query('filter', []);

        // Helpers
        $hasNonEmpty = function (string $k) use ($f): bool {
            return array_key_exists($k, $f) && trim((string) $f[$k]) !== '';
        };
        $ciLike = function ($qb, string $column, ?string $value): void {
            if ($value === null || trim($value) === '') return;
            // Portable case-insensitive contains (works on Postgres/MySQL)
            $qb->whereRaw("LOWER({$column}) LIKE ?", ['%' . Str::lower(trim($value)) . '%']);
            // If you're 100% on Postgres, you could use:
            // $qb->where($column, 'ilike', '%' . trim($value) . '%');
        };
        $ciOrLike = function ($qb, string $column, ?string $value): void {
            if ($value === null || trim($value) === '') return;
            // Portable case-insensitive contains (works on Postgres/MySQL)
            $qb->orWhereRaw("LOWER({$column}) LIKE ?", ['%' . Str::lower(trim($value)) . '%']);
            // If you're 100% on Postgres, you could use:
            // $qb->where($column, 'ilike', '%' . trim($value) . '%');
        };

        $qb = Ck2CkIngestion::query();

        /* ========== Global search (top search box: "db/table/conn id") ========== */
        if ($q !== '') {
            $qb->where(function ($w) use ($q, $ciLike, $ciOrLike) {
                $ciLike($w, 'src_database',         $q);
                $ciOrLike($w, 'source_ch_conn_id',    $q);
                $ciOrLike($w, 'target_db',            $q);
                $ciOrLike($w, 'target_table',         $q);
                $ciOrLike($w, 'target_ch_conn_id',    $q);
            });
        }

        /* ========== Per-column filters (match Blade 1:1) ========== */

        // ID (only when non-empty; avoid '' -> 0)
        if ($hasNonEmpty('id')) {
            $qb->where('id', (int) $f['id']);
        }

        // Enabled (allow '0'/'1', skip only empty string)
        if ($hasNonEmpty('enabled')) {
            $qb->where('enabled', (int) $f['enabled']);
        }

        // Source (src db / conn)
        if ($hasNonEmpty('source')) {
            $v = $f['source'];
            $qb->where(function ($w) use ($v, $ciLike, $ciOrLike) {
                $ciLike($w, 'src_database',      $v);
                $ciOrLike($w, 'source_ch_conn_id', $v);
            });
        }

        // Target (tgt db/table/conn)
        if ($hasNonEmpty('target')) {
            $v = $f['target'];
            $qb->where(function ($w) use ($v, $ciLike, $ciOrLike) {
                $ciLike($w, 'target_db',         $v);
                $ciOrLike($w, 'target_table',      $v);
                $ciOrLike($w, 'target_ch_conn_id', $v);
            });
        }

        // PK / Version
        if ($hasNonEmpty('pk_cols')) {
            $ciLike($qb, 'pk_cols', $f['pk_cols']);
        }
        if ($hasNonEmpty('version_col')) {
            $ciLike($qb, 'version_col', $f['version_col']);
        }

        // Schedule (type + schedule_text: “min/cron expr”)
        if ($hasNonEmpty('schedule_type')) {
            $qb->where('schedule_type', $f['schedule_type']); // 'interval' | 'cron'
        }
        if ($hasNonEmpty('schedule_text')) {
            $v = trim($f['schedule_text']);
            $qb->where(function ($w) use ($v, $ciLike) {
                // If numeric-like, treat as interval_minutes exact
                if (ctype_digit($v)) {
                    $w->orWhere('interval_minutes', (int) $v);
                }
                // Also search cron_expr textually
                $ciLike($w, 'cron_expr', $v);
            });
        }

        // Parallel (slices / rows per page)
        if ($hasNonEmpty('parallel_slices')) {
            $qb->where('parallel_slices', (int) $f['parallel_slices']);
        }
        if ($hasNonEmpty('page_rows')) {
            $qb->where('page_rows', (int) $f['page_rows']);
        }

        // Last Status
        if ($hasNonEmpty('last_status')) {
            $ciLike($qb, 'last_status', $f['last_status']);
        }

        /* ========== Optional: debug SQL & bindings ========== */
        if ($request->query('debug') === '1') {
            $clone = clone $qb;
            dd($clone->toSql(), $clone->getBindings(), $f);
        }

        /* ========== Return all rows if everything is empty ========== */
        $allEmpty = ($q === '') && collect($f)->every(fn($v) => $v === '' || $v === null);
        if ($allEmpty) {
            $rows = Ck2CkIngestion::query()
                ->orderByDesc('id')
                ->paginate(20)
                ->withQueryString();

            return view('admin.manage_datawarehouse', [
                'rows' => $rows,
                'q'    => $q,
            ]);
        }

        $rows = $qb->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.manage_datawarehouse', [
            'rows' => $rows,
            'q'    => $q,
        ]);
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
        // Read GET params explicitly
        $q = trim((string) $request->query('q', ''));
        $f = (array) $request->query('filter', []);

        // Helpers
        $hasNonEmpty = function (string $k) use ($f): bool {
            return array_key_exists($k, $f) && trim((string) $f[$k]) !== '';
        };
        $ciLike = function ($qb, string $column, ?string $value): void {
            if ($value === null || trim($value) === '') return;
            $qb->whereRaw("LOWER({$column}) LIKE ?", ['%' . Str::lower(trim($value)) . '%']);
        };

        $qb = \App\Models\Ck2PgIngestion::query();

        /* ========== Global search (matches your top search box) ========== */
        if ($q !== '') {
            $qb->where(function ($w) use ($q, $ciLike) {
                $ciLike($w, 'source_ch_conn_id', $q);
                $ciLike($w, 'target_schema',     $q);
                $ciLike($w, 'target_table',      $q);
                $ciLike($w, 'target_pg_conn_id', $q);
            });
        }

        /* ========== Per-column filters (exactly as in Blade) ========== */
        // id (only when non-empty; avoid '' -> 0)
        if ($hasNonEmpty('id')) {
            $qb->where('id', (int) $f['id']);
        }

        // enabled (allow '0'/'1', skip only empty string)
        if ($hasNonEmpty('source_ch_conn_id')) {
            $qb->where('enabled', (int) $f['enabled']);
        }

        // source (CH / SQL)
        if ($hasNonEmpty('source_ch_conn_id')) {
            $ciLike($qb, 'source_ch_conn_id', $f['source_ch_conn_id']);
        }
        if ($hasNonEmpty('source_sql_like')) {
            // your column holding the SQL text is assumed 'source_sql'
            $ciLike($qb, 'source_sql', $f['source_sql_like']);
        }

        // target
        if ($hasNonEmpty('target_schema')) {
            $ciLike($qb, 'target_schema', $f['target_schema']);
        }
        if ($hasNonEmpty('target_table')) {
            $ciLike($qb, 'target_table', $f['target_table']);
        }
        if ($hasNonEmpty('target_pg_conn_id')) {
            $ciLike($qb, 'target_pg_conn_id', $f['target_pg_conn_id']);
        }

        // PK / Cursor
        if ($hasNonEmpty('pk_value')) {
            $ciLike($qb, 'pk_value', $f['pk_value']);
        }
        if ($hasNonEmpty('cursor_col')) {
            $ciLike($qb, 'cursor_col', $f['cursor_col']);
        }

        // Schedule
        if ($hasNonEmpty('schedule_type')) {
            $qb->where('schedule_type', $f['schedule_type']); // 'interval' | 'cron'
        }
        if ($hasNonEmpty('interval_minutes')) {
            $qb->where('interval_minutes', (int) $f['interval_minutes']);
        }
        if ($hasNonEmpty('cron_expr')) {
            $ciLike($qb, 'cron_expr', $f['cron_expr']);
        }

        // Batch / Workers
        if ($hasNonEmpty('chunk_rows')) {
            $qb->where('chunk_rows', (int) $f['chunk_rows']);
        }
        if ($hasNonEmpty('max_parallel')) {
            $qb->where('max_parallel', (int) $f['max_parallel']);
        }

        // Last Status
        if ($hasNonEmpty('last_status')) {
            $ciLike($qb, 'last_status', $f['last_status']);
        }

        /* ========== Return all rows if everything is empty ========== */
        $allEmpty = ($q === '') && collect($f)->every(fn($v) => $v === '' || $v === null);
        if ($allEmpty) {
            $rows = \App\Models\Ck2PgIngestion::query()
                ->orderByDesc('id')
                ->paginate(20)
                ->withQueryString();

            return view('admin.manage_datamart', compact('rows', 'q'));
        }

        /* ========== Optional: debug SQL & bindings ========== */
        if ($request->query('debug') === '1') {
            $clone = clone $qb;
            dd($clone->toSql(), $clone->getBindings(), $f);
        }

        $rows = $qb->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.manage_datamart', compact('rows', 'q'));
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

    public function index_sap_cdc(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $f = (array) $request->query('filter', []);

        $hasNonEmpty = function (string $k) use ($f): bool {
            return array_key_exists($k, $f) && trim((string) $f[$k]) !== '';
        };
        $ciLike = function ($qb, string $column, ?string $value): void {
            if ($value === null || trim($value) === '') return;
            $qb->whereRaw("LOWER({$column}) LIKE ?", ['%' . Str::lower(trim($value)) . '%']);
        };
        $ciOrLike = function ($qb, string $column, ?string $value): void {
            if ($value === null || trim($value) === '') return;
            $qb->orWhereRaw("LOWER({$column}) LIKE ?", ['%' . Str::lower(trim($value)) . '%']);
        };

        $qb = \App\Models\SapCdcRegistry::query();

        /* ========== Global search ========== */
        if ($q !== '') {
            $qb->where(function ($w) use ($q, $ciLike, $ciOrLike) {
                $ciLike($w, 'job_code', $q);
                $ciOrLike($w, 'service_name', $q);
                $ciOrLike($w, 'entity_name', $q);
                $ciOrLike($w, 'sim_table', $q);
                $ciOrLike($w, 'qas_table', $q);
                $ciOrLike($w, 'prod_table', $q);
            });
        }

        /* ========== Per-column filters ========== */
        if ($hasNonEmpty('id')) {
            $qb->where('id', (int) $f['id']);
        }

        if ($hasNonEmpty('is_enabled')) {
            $qb->where('is_enabled', (int) $f['is_enabled']);
        }

        if ($hasNonEmpty('job_code')) {
            $ciLike($qb, 'job_code', $f['job_code']);
        }

        if ($hasNonEmpty('service_name')) {
            $ciLike($qb, 'service_name', $f['service_name']);
        }

        if ($hasNonEmpty('entity_name')) {
            $ciLike($qb, 'entity_name', $f['entity_name']);
        }

        if ($hasNonEmpty('method')) {
            $qb->where('method', $f['method']);
        }

        if ($hasNonEmpty('schedule_type')) {
            $qb->where('schedule_type', $f['schedule_type']);
        }

        if ($hasNonEmpty('environment')) {
            $env = $f['environment'];
            $qb->where(function($w) use ($env) {
                if ($env === 'sim') {
                    $w->where('sim_enabled', true);
                } elseif ($env === 'qas') {
                    $w->where('qas_enabled', true);
                } elseif ($env === 'prod') {
                    $w->where('prod_enabled', true);
                }
            });
        }

        $allEmpty = ($q === '') && collect($f)->every(fn($v) => $v === '' || $v === null);
        if ($allEmpty) {
            $rows = \App\Models\SapCdcRegistry::query()
                ->orderByDesc('id')
                ->paginate(15)
                ->withQueryString();

            return view('admin.manage_sap_cdc', [
                'rows' => $rows,
                'q'    => $q,
            ]);
        }

        $rows = $qb->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.manage_sap_cdc', [
            'rows' => $rows,
            'q'    => $q,
        ]);
    }

    public function create_sap_cdc()
    {
        return view('admin.detail_sap_cdc', [
            'data' => []
        ]);
    }

    public function store_sap_cdc(Request $request)
    {
        $validated = $this->validatePayloadSapCdc($request);

        $validated['is_enabled'] = $request->boolean('is_enabled');
        $validated['sim_enabled'] = $request->boolean('sim_enabled');
        $validated['qas_enabled'] = $request->boolean('qas_enabled');
        $validated['prod_enabled'] = $request->boolean('prod_enabled');
        $this->normalizeScheduleSapCdc($validated);

        $row = \App\Models\SapCdcRegistry::create($validated);

        return redirect()
            ->to('/admin/manage_sap_cdc/'.$row->id.'/edit')
            ->with('success', 'SAP CDC Registry created.');
    }

    public function edit_sap_cdc($id)
    {
        $row = \App\Models\SapCdcRegistry::findOrFail($id);
        return view('admin.detail_sap_cdc', [
            'data' => ['id' => $row->id, 'row' => $row],
        ]);
    }

    public function update_sap_cdc(Request $request, $id)
    {
        $row = \App\Models\SapCdcRegistry::findOrFail($id);

        $validated = $this->validatePayloadSapCdc($request);
        $validated['is_enabled'] = $request->boolean('is_enabled');
        $validated['sim_enabled'] = $request->boolean('sim_enabled');
        $validated['qas_enabled'] = $request->boolean('qas_enabled');
        $validated['prod_enabled'] = $request->boolean('prod_enabled');
        $this->normalizeScheduleSapCdc($validated);

        $row->fill($validated)->save();

        return redirect()
            ->to('/admin/manage_sap_cdc/'.$row->id.'/edit')
            ->with('success', 'SAP CDC Registry updated.');
    }

    public function destroy_sap_cdc($id)
    {
        $row = \App\Models\SapCdcRegistry::findOrFail($id);
        $row->delete();

        return redirect()
            ->to('/admin/manage_sap_cdc')
            ->with('success', 'SAP CDC Registry deleted.');
    }

    public function toggle_sap_cdc($id)
    {
        $row = \App\Models\SapCdcRegistry::findOrFail($id);
        $row->is_enabled = ! $row->is_enabled;
        $row->save();

        return back()->with('success', 'Status toggled to '.($row->is_enabled ? 'Enabled' : 'Disabled').'.');
    }

    public function trigger_sap_cdc($id)
    {
        $row = \App\Models\SapCdcRegistry::findOrFail($id);
        $row->trigger_run_now = true;
        if ($row->prod_enabled) {
            $row->prod_last_status = 'queued';
        }
        if ($row->sim_enabled) {
            $row->sim_last_status = 'queued';
        }
        if ($row->qas_enabled) {
            $row->qas_last_status = 'queued';
        }
        $row->save();

        return back()->with('success', "Triggered SAP CDC job #{$row->id} to run now.");
    }

    // --------- helpers ---------

    private function validatePayloadSapCdc(Request $request): array
    {
        $rules = [
            'is_enabled'        => ['nullable'],
            'job_code'          => ['required','string','max:255', Rule::unique('sap_cdc_registry')->ignore($request->route('id'))],
            'service_name'      => ['required','string','max:255'],
            'entity_name'       => ['required','string','max:255'],
            'method'            => ['required', Rule::in(['continuous_cdc','weekly_refresh'])],
            'schedule_type'     => ['required', Rule::in(['interval','cron'])],
            'interval_minutes'  => ['nullable','integer','min:1'],
            'cron_expr'         => ['nullable','string','max:255'],
            'initial_path'      => ['required','string'],
            'delta_path'        => ['nullable','string'],
            'sim_enabled'       => ['nullable'],
            'sim_db'            => ['nullable','string','max:255'],
            'sim_table'         => ['required','string','max:255'],
            'qas_enabled'       => ['nullable'],
            'qas_db'            => ['nullable','string','max:255'],
            'qas_table'         => ['required','string','max:255'],
            'prod_enabled'      => ['nullable'],
            'prod_db'           => ['nullable','string','max:255'],
            'prod_table'        => ['required','string','max:255'],
            'sim_client'        => ['nullable','string','max:10'],
            'qas_client'        => ['nullable','string','max:10'],
            'prod_client'       => ['nullable','string','max:10'],
            'ck_conn_id'        => ['nullable','string','max:255'],
            'log_conn_id'       => ['nullable','string','max:255'],
            'maxpagesize'       => ['nullable','integer','min:1000'],
        ];

        $validated = $request->validate($rules);

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

    private function normalizeScheduleSapCdc(array &$data): void
    {
        $data['sim_db']      = $data['sim_db'] ?? 'sap_sim';
        $data['qas_db']      = $data['qas_db'] ?? 'sap_qas';
        $data['prod_db']     = $data['prod_db'] ?? 'sap_prod';
        $data['sim_client']  = $data['sim_client'] ?? '300';
        $data['qas_client']  = $data['qas_client'] ?? '300';
        $data['prod_client'] = $data['prod_client'] ?? '300';
        $data['ck_conn_id']  = $data['ck_conn_id'] ?? 'clickhouse_default';
        $data['log_conn_id'] = $data['log_conn_id'] ?? 'airflow_logs_mitratel';
        $data['maxpagesize'] = (int)($data['maxpagesize'] ?? 100000);

        if (($data['schedule_type'] ?? 'interval') === 'interval') {
            $data['cron_expr'] = null;
            $data['interval_minutes'] = (int)($data['interval_minutes'] ?? 5);
        } else {
            $data['interval_minutes'] = null;
        }
    }

    public function force_initial_sap_cdc($id)
    {
        $row = \App\Models\SapCdcRegistry::findOrFail($id);
        $row->trigger_force_initial = true;
        if ($row->prod_enabled) {
            $row->prod_initial_done = false;
        }
        if ($row->sim_enabled) {
            $row->sim_initial_done = false;
        }
        if ($row->qas_enabled) {
            $row->qas_initial_done = false;
        }
        $row->state_json = [];
        $row->save();

        return back()->with('success', "Force Initial Load triggered for SAP CDC job #{$row->id}. All enabled environments will perform a full initial load on next run.");
    }

}
