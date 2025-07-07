<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MutasiController extends Controller
{
    public function index()
    {
        return view('transaksi.mutasi.index');
    }

    public function getData(Request $request)
    {
        try {
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $draw = $request->input('draw');
            $search = $request->input('search.value');

            $query = DB::connection('mysql_dbticket')->table('t_mutasi');

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->orWhere('mitraid', 'LIKE', "%{$search}%")
                    ->orWhere('periode', 'LIKE', "%{$search}%")
                    ->orWhere('tanggal', 'LIKE', "%{$search}%")
                    ->orWhere('jam', 'LIKE', "%{$search}%")
                    ->orWhere('dk', 'LIKE', "%{$search}%")
                    ->orWhere('mutasi', 'LIKE', "%{$search}%")
                    ->orWhere('saldoakhir', 'LIKE', "%{$search}%")
                    ->orWhere('bank', 'LIKE', "%{$search}%")
                    ->orWhere('norek', 'LIKE', "%{$search}%")
                    ->orWhere('mutasiinfo', 'LIKE', "%{$search}%")
                    ->orWhere('userid', 'LIKE', "%{$search}%")
                    ->orWhere('status', 'LIKE', "%{$search}%")
                    ->orWhere('statusinfo', 'LIKE', "%{$search}%");
                });
            }

            $totalFiltered = $query->count();

            $orderColumnIndex = $request->input('order.0.column');
            $orderDirection = $request->input('order.0.dir');

            $orderableColumns = [
                'mitraid',
                'periode',
                'tanggal',
                'jam',
                'dk',
                'mutasi',
                'saldoakhir',
                'bank',
                'norek',
                'mutasiinfo',
                'userid',
                'status',
                'statusinfo'
            ];

            if (isset($orderableColumns[$orderColumnIndex])) {
                $columnNameToSort = $orderableColumns[$orderColumnIndex];

                if ($columnNameToSort === 'tanggal') {
                    $query->orderBy('tanggal', $orderDirection)->orderBy('jam', $orderDirection);
                } else {
                    $query->orderBy($columnNameToSort, $orderDirection);
                }
            } else {
                $query->orderBy('tanggal', 'desc')->orderBy('jam', 'desc');
            }

            $mutasi = $query->offset($start)->limit($length)->get();

            $data = $mutasi->map(function ($item) {
                $item = (array) $item;
                $item['datetime'] = $this->formatDatetime($item['tanggal'], $item['jam']);
                return $item;
            });

            $totalData = DB::connection('mysql_dbticket')->table('t_mutasi')->count();

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
