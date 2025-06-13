<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function testConnection()
    {
        try {
            // Coba jalankan query sederhana seperti SELECT 1
            DB::connection('mysql_dbticket')->select('SELECT 1');

            return response()->json([
                'status' => 200,
                'message' => 'Koneksi ke database mysql_dbticket berhasil.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Gagal terkoneksi ke database mysql_dbticket.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // public function index()
    // {
    //     try {
    //         $rawData = DB::connection('mysql_dbticket')->select(
    //             "SELECT * FROM m_user", []
    //         );

    //         $data = array_map(function ($item) {
    //             return (array) $item;
    //         }, $rawData);

    //         return response()->json([
    //             'status' => 200,
    //             'message' => 'Data user berhasil diambil.',
    //             'data' => $data,
    //         ]);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 500,
    //             'message' => 'Terjadi kesalahan saat mengambil data.',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }


    public function showLoginForm()
    {
        return view('login'); // tampilkan form login
    }

    public function login(Request $request)
    {
        $userid = $request->input('userid');
        $password = $request->input('password');

        $user = DB::connection('mysql_dbticket')->select(
            "SELECT * FROM m_user WHERE userid = ? AND `password` = ? AND aktif = 1 LIMIT 1",
            [$userid, $password]
        );

        if (!empty($user)) {
            $user = (array) $user[0]; // Convert dari stdClass ke array
            // Simpan data ke session
            session([
                'userid' => $user['userid'],
                'nama_user' => $user['nama'],
                'role' => $user['roleuser'],
            ]);
            return redirect('/Dasboard-CRM')->with('sukses', 'User ditemukan');
        } else {
            return redirect('/')->with('error', 'User tidak terdaftar');
        }
     }

    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect('/')->with('sukses', 'Logout berhasil');
    }


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
                "INSERT INTO m_user (userid, `password`, nama, roleuser, aktif, inputuser, inputtanggal) VALUES (?, ?, ?, ?, ?, ?, NOW())",
                [$request->userid, $request->password, $request->nama, $request->roleuser, $request->aktif, $inputUser]
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
                "UPDATE m_user SET nama = ?, `password` = ?, roleuser = ?, aktif = ?, updateuser = ?, updatetanggal = NOW() WHERE userid = ?",
                [$request->nama, $request->password, $request->roleuser, $request->aktif, $updateUser, $request->userid]
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
