<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    // M USER
    public function index()
    {
        return view('master_data.m_users.index');
    }

    public function getData()
    {
        try {
            $rawData = DB::connection('mysql_dbticket')->select("SELECT * FROM m_user");
            $data = array_map(function ($item) {
                return (array) $item;
            }, $rawData);

            return response()->json([
                'data' => $data
            ]);

            // return $data;
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $exists = DB::connection('mysql_dbticket')
                ->selectOne("SELECT * FROM m_user WHERE userid = ?", [$request->userid]);

            if ($exists) {
                return response()->json(['message' => 'UserID sudah ada!'], 400);
            }

            $inputUser = session('userid');

           DB::connection('mysql_dbticket')->insert(
                "INSERT INTO m_user (userid, `password`, nama, roleuser, aktif, mitraid, inputuser, inputtanggal) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())",
                [$request->userid, $request->password, $request->nama, $request->roleuser, $request->aktif, $request->mitraid, $inputUser]
            );

            return response()->json(['message' => 'User berhasil ditambahkan.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal tambah: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $updateUser = session('userid');

            DB::connection('mysql_dbticket')->update(
                "UPDATE m_user SET nama = ?, `password` = ?, roleuser = ?, aktif = ?, mitraid = ?, updateuser = ?, updatetanggal = NOW() WHERE userid = ?",
                [$request->nama, $request->password, $request->roleuser, $request->aktif, $request->mitraid, $updateUser, $request->userid]
            );

            return response()->json(['message' => 'User berhasil diupdate.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal update: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            DB::connection('mysql_dbticket')->delete(
                "DELETE FROM m_user WHERE userid = ?",
                [$request->userid]
            );

            return response()->json(['message' => 'User berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal hapus: ' . $e->getMessage()], 500);
        }
    }

}
