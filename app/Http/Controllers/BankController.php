<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\CrmLogger;

class BankController extends Controller
{
    use CrmLogger;

    public function index()
    {
        return view('master_data.m_bank.index');
    }

    public function getData()
    {
        try {
            if (session('role') == "root") {
                $rawData = DB::connection('mysql_dbticket')->select("SELECT * FROM m_bank");
            } else {
                $data_mitraid = session('mitraid');
                $rawData = DB::connection('mysql_dbticket')->select("SELECT * FROM m_bank WHERE mitraid = ?", [$data_mitraid]);
            }

            $data = array_map(function ($item) {
                return (array) $item;
            }, $rawData);

            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengambil data: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $exists = DB::connection('mysql_dbticket')
                ->selectOne("SELECT * FROM m_bank WHERE mitraid = ? AND bank_id = ?", [$request->mitraid, $request->bank_id]);

            if ($exists) {
                $this->log_crm("Master Bank - Create", json_encode([
                    'message' => 'Data bank sudah ada!',
                    'mitraid' => $request->mitraid,
                    'bank_id' => $request->bank_id
                ]));
                return response()->json(['message' => 'Data bank sudah ada!'], 400);
            }

            DB::connection('mysql_dbticket')->insert(
                "INSERT INTO m_bank (mitraid, bank_id, bank_server, bank_client, nama, norek, gangguan_sts, gangguan_ket, input_user, input_tgl, lastupdate_user, lastupdate_tgl)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $request->mitraid,
                    $request->bank_id,
                    $request->bank_server,
                    $request->bank_client,
                    $request->nama,
                    $request->norek,
                    $request->gangguan_sts,
                    $request->gangguan_ket,
                    session('userid'),
                    now()->timezone('Asia/Jakarta'),
                    $request->lastupdate_user,
                    now()->timezone('Asia/Jakarta'),
                ]
            );

            $this->log_crm("Master Bank - Create", json_encode([
                'message' => 'Data bank berhasil ditambahkan.',
                'data' => $request->only([
                    'mitraid',
                    'bank_id',
                    'bank_server',
                    'bank_client',
                    'nama',
                    'norek',
                    'gangguan_sts',
                    'gangguan_ket'
                ])
            ]));

            return response()->json(['message' => 'Data bank berhasil ditambahkan.']);
        } catch (\Exception $e) {
            $this->log_crm("Master Bank - Create", json_encode([
                'message' => 'Gagal tambah',
                'error' => $e->getMessage()
            ]));
            return response()->json(['message' => 'Gagal tambah: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $lastupdate_user = session('userid');

            DB::connection('mysql_dbticket')->update(
                "UPDATE m_bank SET bank_server = ?, bank_client = ?, nama = ?, norek = ?, gangguan_sts = ?, gangguan_ket = ?, lastupdate_user = ?, lastupdate_tgl = ?
                 WHERE mitraid = ? AND bank_id = ?",
                [
                    $request->bank_server,
                    $request->bank_client,
                    $request->nama,
                    $request->norek,
                    $request->gangguan_sts,
                    $request->gangguan_ket,
                    $lastupdate_user,
                    now()->timezone('Asia/Jakarta'),
                    $request->mitraid,
                    $request->bank_id
                ]
            );

            $this->log_crm("Master Bank - Update", json_encode([
                'message' => 'Data bank berhasil diupdate.',
                    'data' => $request->only([
                        'mitraid',
                        'bank_id',
                        'bank_server',
                        'bank_client',
                        'nama',
                        'norek',
                        'gangguan_sts',
                        'gangguan_ket'
                    ])
                ]));

            return response()->json(['message' => 'Data bank berhasil diupdate.']);
        } catch (\Exception $e) {
            $this->log_crm("Master Bank - Update", json_encode([
                'message' => 'Gagal update',
                'error' => $e->getMessage()
            ]));
            return response()->json(['message' => 'Gagal update: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $deletedData = DB::connection('mysql_dbticket')->selectOne(
                "SELECT * FROM m_bank WHERE mitraid = ? AND bank_id = ?",
                [$request->mitraid, $request->bank_id]
            );

            if (!$deletedData) {
                return response()->json(['message' => 'Data bank tidak ditemukan.'], 404);
            }

            DB::connection('mysql_dbticket')->delete(
                "DELETE FROM m_bank WHERE mitraid = ? AND bank_id = ?",
                [$request->mitraid, $request->bank_id]
            );

            $this->log_crm("Master Bank - Delete", json_encode([
                'message' => 'Data bank berhasil dihapus.',
                'data' => $deletedData
            ]));

            return response()->json(['message' => 'Data bank berhasil dihapus.']);
        } catch (\Exception $e) {
            $this->log_crm("Master Bank - Delete", json_encode([
                'message' => 'Gagal hapus',
                'error' => $e->getMessage()
            ]));
            return response()->json(['message' => 'Gagal hapus: ' . $e->getMessage()], 500);
        }
    }
}
