<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\CrmLogger;
use Illuminate\Support\Facades\Log;

class TopupController extends Controller
{
    use CrmLogger;

    public function index()
    {
        return view('transaksi.topup.index');
    }

    public function getData(Request $request)
    {
        try {
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $draw = $request->input('draw');
            $search = $request->input('search.value');

            $query = DB::connection('mysql_dbticket')->table('t_topup');

            // Filter pencarian
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->orWhere('periode', 'LIKE', "%{$search}%")
                      ->orWhere('tanggal', 'LIKE', "%{$search}%")
                      ->orWhere('jam', 'LIKE', "%{$search}%")
                      ->orWhere('via', 'LIKE', "%{$search}%")
                      ->orWhere('userid', 'LIKE', "%{$search}%")
                      ->orWhere('jmlsetor1', 'LIKE', "%{$search}%")
                      ->orWhere('jmlsetor2', 'LIKE', "%{$search}%")
                      ->orWhere('bankid', 'LIKE', "%{$search}%")
                      ->orWhere('norek', 'LIKE', "%{$search}%")
                      ->orWhere('trxid', 'LIKE', "%{$search}%")
                      ->orWhere('ket', 'LIKE', "%{$search}%")
                      ->orWhere('status_ket', 'LIKE', "%{$search}%")
                      ->orWhere('inputuser', 'LIKE', "%{$search}%")
                      ->orWhere('inputtanggal', 'LIKE', "%{$search}%");
                });
            }

            $totalFiltered = $query->count();

            // Sorting
            $orderColumnIndex = $request->input('order.0.column');
            $orderDirection = $request->input('order.0.dir');

            $orderableColumns = [
                'tanggal',      // index 0
                'via',          // index 1
                'userid',       // index 2
                'jmlsetor1',    // index 3
                'jmlsetor2',    // index 4
                'bankid',       // index 5
                'trxid',        // index 6
                'ket',          // index 7
                'status',       // index 8
                'status_ket',    // index 9
                'inputuser',    // index 10
                'inputtanggal'  // index 11
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

            $topups = $query
                ->offset($start)
                ->limit($length)
                ->get();

            $data = $topups->map(function ($item) {
                $item = (array)$item;
                $item['datetime'] = $this->formatDatetime($item['tanggal'], $item['jam']);
                return $item;
            });

            $totalData = DB::connection('mysql_dbticket')->table('t_topup')->count();

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

    public function update(Request $request)
    {
        try {
            // Ambil data sebelum di-update
            $topupData = DB::connection('mysql_dbticket')->selectOne(
                "SELECT id, userid, jmlsetor2, trxid FROM t_topup WHERE id = ?",
                [$request->id]
            );

            // Update status
            DB::connection('mysql_dbticket')->update(
                "UPDATE t_topup SET `status` = 0 WHERE id = ?",
                [$request->id]
            );


            $userid = session('userid');

            // Coba log ke Laravel log dulu untuk debug
            // Log::info("Reset topup status oleh user $userid", [
            //     'id' => $request->id,
            //     'topup' => $topupData,
            // ]);

            // Log dengan data topup
            $this->log_crm("Topup - Reset Status", json_encode([
                'message' => 'Status berhasil di-reset.',
                'userid' => $userid,
                'data' => $topupData
            ]));

            return response()->json(['message' => 'Status berhasil di-reset.']);
        } catch (\Exception $e) {
            $this->log_crm("Topup - Reset Status", json_encode([
                'message' => 'Gagal reset status.',
                'id' => $request->id,
                'error' => $e->getMessage()
            ]));

            return response()->json(['message' => 'Gagal Reset: ' . $e->getMessage()], 500);
        }
    }


    public function confirm_akses(Request $request)
    {
        try {
            $userid = session('userid');
            $password = $request->input('password');

            $user = DB::connection('mysql_dbticket')->select(
                "SELECT * FROM m_user WHERE userid = ? AND `password` = ? AND aktif = 1 LIMIT 1",
                [$userid, $password]
            );

            if (count($user) === 0) {
                return response()->json(['message' => 'Password Anda salah.'], 401);
            }

            return response()->json(['message' => 'Hak Akses Benar.']);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

}
