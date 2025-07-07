<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function index()
    {
        return view('transaksi.ticket.index');
    }

    public function getData(Request $request)
    {
        try {
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $draw = $request->input('draw');
            $search = $request->input('search.value');

            $query = DB::connection('mysql_dbticket')->table('t_ticket');

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->orWhere('idinbox', 'LIKE', "%{$search}%")
                    ->orWhere('idmutasi', 'LIKE', "%{$search}%")
                    ->orWhere('mitraid', 'LIKE', "%{$search}%")
                    ->orWhere('periode', 'LIKE', "%{$search}%")
                    ->orWhere('tanggal', 'LIKE', "%{$search}%")
                    ->orWhere('jam', 'LIKE', "%{$search}%")
                    ->orWhere('userdevice', 'LIKE', "%{$search}%")
                    ->orWhere('via', 'LIKE', "%{$search}%")
                    ->orWhere('viaid', 'LIKE', "%{$search}%")
                    ->orWhere('userid', 'LIKE', "%{$search}%")
                    ->orWhere('bankid', 'LIKE', "%{$search}%")
                    ->orWhere('bankserver', 'LIKE', "%{$search}%")
                    ->orWhere('bankclient', 'LIKE', "%{$search}%")
                    ->orWhere('norek', 'LIKE', "%{$search}%")
                    ->orWhere('ticketid', 'LIKE', "%{$search}%")
                    ->orWhere('statusticket', 'LIKE', "%{$search}%");
                });
            }

            $totalFiltered = $query->count();

            $orderColumnIndex = $request->input('order.0.column');
            $orderDirection = $request->input('order.0.dir');

            $orderableColumns = [
                'tanggal',       // 0
                'jam',           // 1
                'mitraid',       // 2
                'userid',        // 3
                'bankid',        // 4
                'norek',         // 5
                'total',         // 6
                'ticketid',      // 7
                'ticketexpired', // 8
                'statusticket',  // 9
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

            $tickets = $query->offset($start)->limit($length)->get();

            $data = $tickets->map(function ($item) {
                $item = (array)$item;
                $item['datetime'] = $this->formatDatetime($item['tanggal'], $item['jam']);
                return $item;
            });

            $totalData = DB::connection('mysql_dbticket')->table('t_ticket')->count();

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
