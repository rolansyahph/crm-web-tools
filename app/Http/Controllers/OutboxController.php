<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OutboxController extends Controller
{
    public function index()
    {
        return view('transaksi.outbox.index');
    }

    public function getData(Request $request)
    {
        try {
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $draw = $request->input('draw');
            $search = $request->input('search.value');

            $query = DB::connection('mysql_dbticket')->table('t_outbox');

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->orWhere('inboxid', 'LIKE', "%{$search}%")
                    ->orWhere('topupid', 'LIKE', "%{$search}%")
                    ->orWhere('telegram_msgid', 'LIKE', "%{$search}%")
                    ->orWhere('periode', 'LIKE', "%{$search}%")
                    ->orWhere('tanggal', 'LIKE', "%{$search}%")
                    ->orWhere('jam', 'LIKE', "%{$search}%")
                    ->orWhere('mitraid', 'LIKE', "%{$search}%")
                    ->orWhere('fungsi', 'LIKE', "%{$search}%")
                    ->orWhere('via', 'LIKE', "%{$search}%")
                    ->orWhere('viaid', 'LIKE', "%{$search}%")
                    ->orWhere('pesan', 'LIKE', "%{$search}%")
                    ->orWhere('status', 'LIKE', "%{$search}%")
                    ->orWhere('inputuser', 'LIKE', "%{$search}%")
                    ->orWhere('inputtanggal', 'LIKE', "%{$search}%")
                    ->orWhere('updateuser', 'LIKE', "%{$search}%")
                    ->orWhere('updatetanggal', 'LIKE', "%{$search}%");
                });
            }

            $totalFiltered = $query->count();

            $orderColumnIndex = $request->input('order.0.column');
            $orderDirection = $request->input('order.0.dir');

            $orderableColumns = [
                'tanggal',         // 0
                'jam',             // 1
                'mitraid',         // 2
                'fungsi',          // 3
                'via',             // 4
                'viaid',           // 5
                'pesan',           // 6
                'status',          // 7
                'inputuser',       // 8
                'inputtanggal',    // 9
                'updateuser',      // 10
                'updatetanggal'    // 11
            ];

            if (isset($orderableColumns[$orderColumnIndex])) {
                $column = $orderableColumns[$orderColumnIndex];
                if ($column === 'tanggal') {
                    $query->orderBy('tanggal', $orderDirection)->orderBy('jam', $orderDirection);
                } else {
                    $query->orderBy($column, $orderDirection);
                }
            } else {
                $query->orderBy('tanggal', 'desc')->orderBy('jam', 'desc');
            }

            $outboxes = $query->offset($start)->limit($length)->get();

            $data = $outboxes->map(function ($item) {
                $item = (array) $item;
                $item['datetime'] = $this->formatDatetime($item['tanggal'], $item['jam']);
                return $item;
            });

            $totalData = DB::connection('mysql_dbticket')->table('t_outbox')->count();

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
        $tglStr = str_pad($tanggal, 8, '0', STR_PAD_LEFT);
        $jamStr = str_pad($jam, 6, '0', STR_PAD_LEFT);

        $date = \DateTime::createFromFormat('Ymd His', $tglStr . ' ' . $jamStr);
        return $date ? $date->format('Y-m-d H:i:s') : null;
    }

}
