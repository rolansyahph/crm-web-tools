<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InboxController extends Controller
{
    public function index()
    {
        return view('transaksi.inbox.index');
    }

    public function getData(Request $request)
    {
        try {
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $draw = $request->input('draw');
            $search = $request->input('search.value');

            $query = DB::connection('mysql_dbticket')->table('t_inbox');

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->orWhere('periode', 'LIKE', "%{$search}%")
                    ->orWhere('tanggal', 'LIKE', "%{$search}%")
                    ->orWhere('jam', 'LIKE', "%{$search}%")
                    ->orWhere('fungsi', 'LIKE', "%{$search}%")
                    ->orWhere('mitraid', 'LIKE', "%{$search}%")
                    ->orWhere('via', 'LIKE', "%{$search}%")
                    ->orWhere('viaid', 'LIKE', "%{$search}%")
                    ->orWhere('pesan', 'LIKE', "%{$search}%")
                    ->orWhere('userid', 'LIKE', "%{$search}%")
                    ->orWhere('ticketid', 'LIKE', "%{$search}%")
                    ->orWhere('inputuser', 'LIKE', "%{$search}%")
                    ->orWhere('status', 'LIKE', "%{$search}%")
                    ->orWhere('statusreset', 'LIKE', "%{$search}%");
                });
            }

            $totalFiltered = $query->count();

            $orderColumnIndex = $request->input('order.0.column');
            $orderDirection = $request->input('order.0.dir');

            $orderableColumns = [
                'tanggal',        // 0
                'jam',            // 1
                'fungsi',         // 2
                'mitraid',        // 3
                'via',            // 4
                'viaid',          // 5
                'pesan',          // 6
                'userid',         // 7
                'ticketid',       // 8
                'inputuser',      // 9
                'inputtanggal',   // 10
                'updateuser',     // 11
                'updatetanggal',  // 12
                'status',         // 13
                'statusreset'     // 14
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

            $inboxes = $query->offset($start)->limit($length)->get();

            $data = $inboxes->map(function ($item) {
                $item = (array)$item;
                $item['datetime'] = $this->formatDatetime($item['tanggal'], $item['jam']);
                return $item;
            });

            $totalData = DB::connection('mysql_dbticket')->table('t_inbox')->count();

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
