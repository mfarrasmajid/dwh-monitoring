<?php

namespace App\Services;

use Tinderbox\Clickhouse\Client;
use Tinderbox\Clickhouse\Server;
use Tinderbox\Clickhouse\ServerProvider;
use Illuminate\Support\Facades\Log;

class ClickHouseClient
{
    private $client;

    public function __construct()
    {
        $server = new Server(
            \Config::get('values.CLICKHOUSE_HOST'),
            (int) \Config::get('values.CLICKHOUSE_PORT'),
            \Config::get('values.CLICKHOUSE_DB'),
            \Config::get('values.CLICKHOUSE_USER'),
            \Config::get('values.CLICKHOUSE_PASS')
        );

        // (opsional) set default DB
        $server->setDatabase(\Config::get('values.CLICKHOUSE_DB'));

        $provider = new ServerProvider();
        $provider->addServer($server);

        $this->client = new Client($provider);
    }

    public function select(string $sql): array
    {
        // Guard dasar: SELECT only, single statement
        if (!preg_match('/^\s*select\s/i', $sql)) {
            throw new \InvalidArgumentException('Only SELECT is allowed');
        }
        if (strpos($sql, ';') !== false) {
            throw new \InvalidArgumentException('Multiple statements not allowed');
        }
        // Hapus klausa SETTINGS di akhir jika ada (readonly user tak boleh ubah setting)
        $sql = preg_replace('/\s+SETTINGS\s+.+$/i', '', $sql);

        $stmt = $this->client->readOne($sql);
        return $stmt->getRows();
    }

    public function schemaSummary(array $allowedDbs): string
    {
        if (empty($allowedDbs)) {
            return "No allowed databases.";
        }

        // Quote daftar DB: 'db1','db2',...
        $dbList = implode(',', array_map(fn($d) => "'$d'", $allowedDbs));

        // 1) Ambil daftar tabel + engine + primary_key expression (jika ada)
        $tables = $this->client->readOne("
            SELECT
                database AS db_name,
                name     AS tb_name,
                engine   AS engine,
                primary_key AS primary_key_expr
            FROM system.tables
            WHERE database IN ($dbList)
            ORDER BY db_name, tb_name
        ")->getRows();

        // 2) Ambil kolom per tabel (urut posisi)
        $columns = $this->client->readOne("
            SELECT
                database AS db_name,
                table    AS tb_name,
                name     AS col_name,
                type     AS col_type,
                position
            FROM system.columns
            WHERE database IN ($dbList)
            ORDER BY db_name, tb_name, position
        ")->getRows();

        // 3) Format ringkasan menjadi teks
        $lines = [];

        // Bagian daftar tabel
        $lines[] = "Tables:";
        foreach ($tables as $t) {
            $pk = $t['primary_key_expr'] ?? '';
            $pkInfo = ($pk !== '' && $pk !== '[]') ? " (PK: {$pk})" : "";
            $engine = $t['engine'] ? " [Engine: {$t['engine']}]" : "";
            $lines[] = "- {$t['db_name']}.{$t['tb_name']}{$engine}{$pkInfo}";
        }

        // Kelompokkan kolom per fully-qualified table
        $by = [];
        foreach ($columns as $c) {
            $fq = "{$c['db_name']}.{$c['tb_name']}";
            $by[$fq][] = "{$c['col_name']} {$c['col_type']}";
        }

        // Batasi jumlah kolom per tabel bila terlalu panjang (opsional)
        $maxColsPerTable = 60;

        $lines[] = "";
        $lines[] = "Columns per table:";
        foreach ($by as $fq => $cols) {
            $slice = array_slice($cols, 0, $maxColsPerTable);
            $suffix = (count($cols) > $maxColsPerTable) ? " …(+".(count($cols)-$maxColsPerTable)." more)" : "";
            $lines[] = "- {$fq}: ".implode(', ', $slice).$suffix;
        }

        return implode("\n", $lines);
    }
}
