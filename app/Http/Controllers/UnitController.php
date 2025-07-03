<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\CrmLogger;

class UnitController extends Controller
{
    use CrmLogger;

    public function index()
    {
        return view('master_data.m_unit.index');
    }

    public function getData()
    {
        try {
            if (session('role') == "root") {
                $rawData = DB::connection('mysql_dbticket')->select("SELECT * FROM m_unit");
            } else {
                $data_mitraid = session('mitraid');
                $rawData = DB::connection('mysql_dbticket')->select("SELECT * FROM m_unit WHERE mitraid = ?", [$data_mitraid]);
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
                ->selectOne("SELECT * FROM m_unit WHERE mitraid = ? AND kdunit = ?", [$request->mitraid, $request->kdunit]);

            if ($exists) {
                $this->log_crm("Master Unit - Create", json_encode([
                    'message' => 'Unit sudah ada!',
                    'mitraid' => $request->mitraid,
                    'kdunit' => $request->kdunit
                ]));
                return response()->json(['message' => 'Unit sudah ada!'], 400);
            }

            DB::connection('mysql_dbticket')->insert(
                "INSERT INTO m_unit (mitraid, kdunit, namaunit, nodb, keydb)
                 VALUES (?, ?, ?, ?, ?)",
                [
                    $request->mitraid,
                    $request->kdunit,
                    $request->namaunit,
                    $request->nodb,
                    $request->keydb
                ]
            );

            $this->log_crm("Master Unit - Create", json_encode([
                'message' => 'Unit berhasil ditambahkan.',
                'data' => $request->only(['mitraid', 'kdunit', 'namaunit', 'nodb', 'keydb'])
            ]));

            return response()->json(['message' => 'Unit berhasil ditambahkan.']);
        } catch (\Exception $e) {
            $this->log_crm("Master Unit - Create", json_encode([
                'message' => 'Gagal tambah',
                'error' => $e->getMessage()
            ]));
            return response()->json(['message' => 'Gagal tambah: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::connection('mysql_dbticket')->update(
                "UPDATE m_unit SET namaunit = ?, nodb = ?, keydb = ?
                 WHERE mitraid = ? AND kdunit = ?",
                [
                    $request->namaunit,
                    $request->nodb,
                    $request->keydb,
                    $request->mitraid,
                    $request->kdunit
                ]
            );

            $this->log_crm("Master Unit - Update", json_encode([
                'message' => 'Unit berhasil diupdate.',
                'data' => $request->only(['mitraid', 'kdunit', 'namaunit', 'nodb', 'keydb'])
            ]));

            return response()->json(['message' => 'Unit berhasil diupdate.']);
        } catch (\Exception $e) {
            $this->log_crm("Master Unit - Update", json_encode([
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
                "SELECT * FROM m_unit WHERE mitraid = ? AND kdunit = ?",
                [$request->mitraid, $request->kdunit]
            );

            if (!$deletedData) {
                return response()->json(['message' => 'Unit tidak ditemukan.'], 404);
            }

            DB::connection('mysql_dbticket')->delete(
                "DELETE FROM m_unit WHERE mitraid = ? AND kdunit = ?",
                [$request->mitraid, $request->kdunit]
            );

            $this->log_crm("Master Unit - Delete", json_encode([
                'message' => 'Unit berhasil dihapus.',
                'data' => $deletedData
            ]));

            return response()->json(['message' => 'Unit berhasil dihapus.']);
        } catch (\Exception $e) {
            $this->log_crm("Master Unit - Delete", json_encode([
                'message' => 'Gagal hapus',
                'error' => $e->getMessage()
            ]));
            return response()->json(['message' => 'Gagal hapus: ' . $e->getMessage()], 500);
        }
    }
}
