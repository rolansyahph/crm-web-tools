<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\CrmLogger;

class MitraController extends Controller
{
    use CrmLogger;

    public function index()
    {
        return view('master_data.m_mitra.index');
    }

    public function getData()
    {
        try {
            $rawData = DB::connection('mysql_dbticket')->select("SELECT * FROM m_mitra");
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
                ->selectOne("SELECT * FROM m_mitra WHERE mitraid = ?", [$request->mitraid]);

            if ($exists) {
                $this->log_crm("Master Mitra - Create", json_encode([
                    'message' => 'Mitra ID sudah ada!',
                    'mitraid' => $request->mitraid
                ]));
                return response()->json(['message' => 'Mitra ID sudah ada!'], 400);
            }

            DB::connection('mysql_dbticket')->insert(
                "INSERT INTO m_mitra (mitraid, nama, alamat, region, aktif, inputuser, inputtanggal)
                 VALUES (?, ?, ?, ?, ?, ?, NOW())",
                [
                    $request->mitraid,
                    $request->nama,
                    $request->alamat,
                    $request->region,
                    $request->aktif,
                    session('userid')
                ]
            );

            $this->log_crm("Master Mitra - Create", json_encode([
                'message' => 'Mitra berhasil ditambahkan.',
                'data' => $request->only(['mitraid', 'nama', 'alamat', 'region', 'aktif'])
            ]));

            return response()->json(['message' => 'Mitra berhasil ditambahkan.']);
        } catch (\Exception $e) {
            $this->log_crm("Master Mitra - Create", json_encode([
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
                "UPDATE m_mitra SET nama = ?, alamat = ?, region = ?, aktif = ?, updateuser = ?, updatetanggal = NOW()
                 WHERE mitraid = ?",
                [
                    $request->nama,
                    $request->alamat,
                    $request->region,
                    $request->aktif,
                    session('userid'),
                    $request->mitraid
                ]
            );

            $this->log_crm("Master Mitra - Update", json_encode([
                'message' => 'Mitra berhasil diupdate.',
                'data' => $request->only(['mitraid', 'nama', 'alamat', 'region', 'aktif'])
            ]));

            return response()->json(['message' => 'Mitra berhasil diupdate.']);
        } catch (\Exception $e) {
            $this->log_crm("Master Mitra - Update", json_encode([
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
                "SELECT * FROM m_mitra WHERE mitraid = ?",
                [$request->mitraid]
            );

            if (!$deletedData) {
                return response()->json(['message' => 'Mitra tidak ditemukan.'], 404);
            }

            DB::connection('mysql_dbticket')->delete(
                "DELETE FROM m_mitra WHERE mitraid = ?",
                [$request->mitraid]
            );

            $this->log_crm("Master Mitra - Delete", json_encode([
                'message' => 'Mitra berhasil dihapus.',
                'data' => $deletedData
            ]));

            return response()->json(['message' => 'Mitra berhasil dihapus.']);
        } catch (\Exception $e) {
            $this->log_crm("Master Mitra - Delete", json_encode([
                'message' => 'Gagal hapus',
                'error' => $e->getMessage()
            ]));

            return response()->json(['message' => 'Gagal hapus: ' . $e->getMessage()], 500);
        }
    }
}
