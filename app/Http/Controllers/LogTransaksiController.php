<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class LogTransaksiController extends Controller
{
    public function index()
    {
        return view('tools.log_transaksi.index');
    }

    public function getData(Request $request)
    {
        try {
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $draw = $request->input('draw');
            $search = $request->input('search.value'); // Keyword pencarian

            $query = DB::connection('mysql_dbticket')->table('t_log');

            // Jika ada keyword pencarian
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

            // Hitung total data setelah filter (penting untuk DataTables)
            $totalFiltered = $query->count();

            // --- Logika Sorting Dimulai ---
            $orderColumnIndex = $request->input('order.0.column');
            $orderDirection = $request->input('order.0.dir');
            // $columns = $request->input('columns'); // Bisa digunakan untuk debugging jika diperlukan

            // Mapping indeks kolom DataTables ke nama kolom di database
            // Urutan di sini HARUS sama dengan urutan '<th>' dan 'columns' di view
            $orderableColumns = [
                'tanggal', // Kolom index 0: Untuk 'datetime'
                'fungsi',  // Kolom index 1
                'key1',    // Kolom index 2
                'key2',    // Kolom index 3
                'key3',    // Kolom index 4
                'pesan',   // Kolom index 5
                'inputuser', // Kolom index 6
                'inputtanggal' // Kolom index 7
            ];

            // Terapkan sorting jika parameter sorting tersedia dan valid
            if (isset($orderableColumns[$orderColumnIndex])) {
                $columnNameToSort = $orderableColumns[$orderColumnIndex];

                // Penanganan khusus untuk kolom 'datetime' yang digabungkan dari 'tanggal' dan 'jam'
                if ($columnNameToSort === 'tanggal') {
                    // Sorting berdasarkan tanggal lalu jam untuk kolom 'datetime'
                    $query->orderBy('tanggal', $orderDirection)->orderBy('jam', $orderDirection);
                } else {
                    $query->orderBy($columnNameToSort, $orderDirection);
                }
            } else {
                // Sorting default jika tidak ada parameter sorting atau kolom tidak dapat diurutkan
                // Sesuai dengan order: [[0, 'desc']] di view (sort by datetime desc)
                $query->orderBy('tanggal', 'desc')->orderBy('jam', 'desc');
            }
            // --- Logika Sorting Berakhir ---

            $logs = $query
                ->offset($start)
                ->limit($length)
                ->get();

            $data = $logs->map(function ($item) {
                $item = (array)$item;
                $item['datetime'] = $this->formatDatetime($item['tanggal'], $item['jam']);
                return $item;
            });

            // Hitung total seluruh data (tanpa filter, untuk 'recordsTotal')
            $totalData = DB::connection('mysql_dbticket')->table('t_log')->count();

            return response()->json([
                'draw' => intval($draw),
                'recordsTotal' => $totalData,
                'recordsFiltered' => $totalFiltered,
                'data' => $data,
            ]);

        } catch (\Exception $e) {
            // Log error untuk debugging lebih lanjut
            Log::error("Gagal mengambil data log: " . $e->getMessage());
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
