<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnitController extends Controller
{
    public function index()
    {
        return view('master_data.m_unit.index');
    }

    public function getData()
    {
        try {
            $rawData = DB::connection('mysql_dbticket')->select("SELECT * FROM m_unit");
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

            return response()->json(['message' => 'Unit berhasil ditambahkan.']);
        } catch (\Exception $e) {
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

            return response()->json(['message' => 'Unit berhasil diupdate.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal update: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            DB::connection('mysql_dbticket')->delete(
                "DELETE FROM m_unit WHERE mitraid = ? AND kdunit = ?",
                [$request->mitraid, $request->kdunit]
            );

            return response()->json(['message' => 'Unit berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal hapus: ' . $e->getMessage()], 500);
        }
    }
}


