<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class LogController extends Controller
{
    public function index()
    {
        return view('transaksi.log.index');
    }

    public function getData(Request $request)
    {
        try {
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $draw = $request->input('draw');
            $search = $request->input('search.value'); // Keyword pencarian

            $query = DB::connection('mysql_dbticket')->table('t_log');

            // 🔍 Jika ada keyword pencarian
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->orWhere('periode', 'LIKE', "%{$search}%")
                    ->orWhere('tanggal', 'LIKE', "%{$search}%")
                    ->orWhere('jam', 'LIKE', "%{$search}%")
                    ->orWhere('fungsi', 'LIKE', "%{$search}%")
                    ->orWhere('key1', 'LIKE', "%{$search}%")
                    ->orWhere('key2', 'LIKE', "%{$search}%")
                    ->orWhere('key3', 'LIKE', "%{$search}%")
                    ->orWhere('pesan', 'LIKE', "%{$search}%")
                    ->orWhere('inputuser', 'LIKE', "%{$search}%")
                    ->orWhere('inputtanggal', 'LIKE', "%{$search}%");
                });
            }

            $totalFiltered = $query->count(); // Jumlah data setelah filter

            $logs = $query
                ->orderByRaw("CONCAT(LPAD(tanggal, 8, '0'), LPAD(jam, 6, '0')) DESC")
                ->offset($start)
                ->limit($length)
                ->get();
                

            $data = $logs->map(function ($item) {
                $item = (array)$item;
                $item['datetime'] = $this->formatDatetime($item['tanggal'], $item['jam']);
                return $item;
            });

            // Hitung total seluruh data (tanpa filter)
            $totalData = DB::connection('mysql_dbticket')->table('t_log')->count();

            return response()->json([
                'draw' => intval($draw),
                'recordsTotal' => $totalData,
                'recordsFiltered' => $totalFiltered,
                'data' => $data,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengambil data: ' . $e->getMessage()], 500);
        }
    } 


    private function formatDatetime($tanggal, $jam)
    {
        // Format INT tanggal menjadi yyyy-mm-dd
        $tglStr = str_pad($tanggal, 8, '0', STR_PAD_LEFT);
        $jamStr = str_pad($jam, 6, '0', STR_PAD_LEFT);

        $date = \DateTime::createFromFormat('Ymd His', $tglStr . ' ' . $jamStr);
        return $date ? $date->format('Y-m-d H:i:s') : null;
    }
}
