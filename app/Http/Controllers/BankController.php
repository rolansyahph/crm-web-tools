<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankController extends Controller
{
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
                // User biasa: filter berdasarkan mitraid
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
                    now(),
                    $request->lastupdate_user,
                    now()
                ]
            );

            return response()->json(['message' => 'Data bank berhasil ditambahkan.']);
        } catch (\Exception $e) {
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
                    now(),
                    $request->mitraid,
                    $request->bank_id
                ]
            );

            return response()->json(['message' => 'Data bank berhasil diupdate.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal update: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            DB::connection('mysql_dbticket')->delete(
                "DELETE FROM m_bank WHERE mitraid = ? AND bank_id = ?",
                [$request->mitraid, $request->bank_id]
            );

            return response()->json(['message' => 'Data bank berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal hapus: ' . $e->getMessage()], 500);
        }
    }
}

