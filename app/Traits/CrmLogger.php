<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait CrmLogger
{
    public function log_crm($fungsi, $pesan)
    {
        try {
            $userId = session('userid');
            $now = now()->timezone('Asia/Jakarta');

            $periode = $now->format('Ym');
            $tanggal = $now->format('Ymd');
            $jam = $now->format('His');

            // Debug isi variabel Cek di stroge/log
            // Log::debug("CRM Log Params", [
            //     'userid' => $userId,
            //     'fungsi' => $fungsi,
            //     'pesan' => $pesan,
            // ]);

            DB::connection('mysql_dbticket')->insert(
                "INSERT INTO t_log_crm (periode, tanggal, jam, fungsi, pesan, userupdate)
                 VALUES (?, ?, ?, ?, ?, ?)",
                [$periode, $tanggal, $jam, $fungsi, $pesan, $userId]
            );
        } catch (\Exception $e) {
            Log::error("Gagal mencatat log CRM: " . $e->getMessage());
        }
    }
}
