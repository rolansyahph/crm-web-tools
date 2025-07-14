<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Traits\CrmLogger;

class UserController extends Controller
{
    use CrmLogger;

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


    // FORM LOGIN & LOGOUT
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
                'mitraid' => $user['mitraid'],
            ]);
            return redirect('/Dasboard-CRM')->with('sukses', 'User ditemukan');
        } else {
            return redirect('/')->with('error', 'User tidak terdaftar');
        }
     }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('sukses', 'Logout berhasil');
    }

    // END

    // M USER
    // Tampilkan halaman index
    public function index()
    {
        return view('master_data.m_users.index');
    }

    // Ambil data user dari database
    public function getData()
    {
        try {
            $rawData = DB::connection('mysql_dbticket')->select("SELECT * FROM m_user");

            $data = array_map(function ($item) {
                return (array) $item;
            }, $rawData);

            return response()->json(['data' => $data]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    // Simpan user baru
    public function store(Request $request)
    {
        try {
            $exists = DB::connection('mysql_dbticket')
                ->selectOne("SELECT * FROM m_user WHERE userid = ?", [$request->userid]);

            if ($exists) {
                $this->log_crm("Master Users - Create", json_encode(['message' => 'UserID sudah ada!']));
                return response()->json(['message' => 'UserID sudah ada!'], 400);
            }

            $inputUser = session('userid');

            DB::connection('mysql_dbticket')->insert(
                "INSERT INTO m_user (userid, `password`, nama, roleuser, aktif, mitraid, inputuser, inputtanggal)
                 VALUES (?, ?, ?, ?, ?, ?, ?, NOW())",
                [
                    $request->userid,
                    $request->password,
                    $request->nama,
                    $request->roleuser,
                    $request->aktif,
                    $request->mitraid,
                    $inputUser
                ]
            );
            $userid_create = session('userid');
            $this->log_crm("Master Users - Create", json_encode([
                'message' => 'User berhasil ditambahkan.',
                'userid_create' => $userid_create,
                'data' => $request->only(['nama', 'password', 'roleuser', 'aktif', 'mitraid'])
            ]));

            return response()->json(['message' => 'User berhasil ditambahkan.']);

        } catch (\Exception $e) {
            $this->log_crm("Master Users - Create", json_encode(['message' => 'Gagal tambah', 'error' => $e->getMessage()]));
            return response()->json(['message' => 'Gagal tambah: ' . $e->getMessage()], 500);
        }
    }

    // Update user
    public function update(Request $request)
    {
        try {
            $updateUser = session('userid');

            DB::connection('mysql_dbticket')->update(
                "UPDATE m_user
                SET nama = ?, `password` = ?, roleuser = ?, aktif = ?, mitraid = ?, updateuser = ?, updatetanggal = NOW()
                WHERE userid = ?",
                [
                    $request->nama,
                    $request->password,
                    $request->roleuser,
                    $request->aktif,
                    $request->mitraid,
                    $updateUser,
                    $request->userid
                ]
            );

            // Log data hasil perubahan saja
            $userid_update = session('userid');
            $this->log_crm("Master Users - Update", json_encode([
                'message' => 'User berhasil diupdate.',
                'userid_update' => $userid_update,
                'data' => $request->only(['nama', 'password', 'roleuser', 'aktif', 'mitraid'])
            ]));

            return response()->json(['message' => 'User berhasil diupdate.']);

        } catch (\Exception $e) {
            $this->log_crm("Master Users - Update", json_encode([
                'message' => 'Gagal update',
                'error' => $e->getMessage()
            ]));
            return response()->json(['message' => 'Gagal update: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            // Ambil data sebelum dihapus (agar bisa dicatat di log)
            $deletedData = DB::connection('mysql_dbticket')->selectOne(
                "SELECT * FROM m_user WHERE userid = ?",
                [$request->userid]
            );

            // if (!$deletedData) {
            //     return response()->json(['message' => 'User tidak ditemukan.'], 404);
            // }

            // Lakukan penghapusan
            DB::connection('mysql_dbticket')->delete(
                "DELETE FROM m_user WHERE userid = ?",
                [$request->userid]
            );

            // Catat log ke t_log_crm
            $userid_delete = session('userid');
            $this->log_crm("Master Users - Delete", json_encode([
                'message' => 'User berhasil dihapus.',
                'userid_delete' => $userid_delete,
                'data' => $deletedData
            ]));

            return response()->json(['message' => 'User berhasil dihapus.']);

        } catch (\Exception $e) {
            // Jika gagal hapus, catat juga log gagalnya
            $this->log_crm("Master Users - Delete", json_encode([
                'message' => 'Gagal hapus',
                'error' => $e->getMessage()
            ]));

            return response()->json(['message' => 'Gagal hapus: ' . $e->getMessage()], 500);
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
