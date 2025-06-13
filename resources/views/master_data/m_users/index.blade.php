@extends('layouts.master')

@section('content')
<!-- M_USERS -->
<div class="main-content-inner">
    <div class="row">
        <!-- Dark table start -->
        <div class="col-12 mt-1">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Data User</h4>
                    {{-- <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addUserModal">Tambah User</button> --}}
                    @if (session('role') === 'root' || session('role') === 'admin')
                        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addUserModal">Tambah User</button>
                    @endif
                    <div class="data-tables datatable-dark">
                        <table id="dataTable3" class="table table-striped table-bordered nowrap" style="width:100%">
                            <thead class="text-capitalize">
                                <tr>
                                    <th>User ID</th>
                                    <th>Nama</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Input User</th>
                                    <th>Input Tanggal</th>
                                    <th>Update User</th>
                                    <th>Update Tanggal</th>
                                    @if (session('role') === 'user')
                                        <th style="display: none;">Aksi</th>
                                    @else
                                        <th>Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody></tbody> <!-- Kosong, karena diisi oleh JS -->
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Dark table end -->
        {{-- Tambah Users --}}
        <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <form id="addUserForm">
                    @csrf
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title">Tambah User</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                    <div class="form-group">
                        <label>User ID</label>
                        <input type="text" name="userid" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="text" name="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="roleuser" id="edit_roleuser" class="form-control">
                            <option value="root">root</option>
                            <option value="admin">admin</option>
                            <option value="user">user</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Aktif</label>
                        <select name="aktif" class="form-control">
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                        </select>
                    </div>
                    </div>
                    <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
        {{-- Edit User --}}
        <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <form id="editUserForm">
                    @csrf
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                    <input type="hidden" name="userid" id="edit_userid">
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" name="nama" id="edit_nama" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="text" name="password" id="edit_password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="roleuser" id="edit_roleuser" class="form-control">
                            <option value="root">root</option>
                            <option value="admin">admin</option>
                            <option value="user">user</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Aktif</label>
                        <select name="aktif" id="edit_aktif" class="form-control">
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                        </select>
                    </div>
                    </div>
                    <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">Update</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
        {{-- EndModal --}}
    </div>
</div>
@endsection

@section('javascript')
<script>
    
$(document).ready(function () {
    if ($.fn.DataTable.isDataTable('#dataTable3')) {
        $('#dataTable3').DataTable().clear().destroy();
    }

    const userRole = "{{ session('role') }}";
    const isUser = userRole === 'user';

    const table = $('#dataTable3').DataTable({
        scrollX: true,
        responsive: true,
        processing: true,
        serverSide: false,
        ajax: "{{ route('m_users.data') }}",
        columns: [
           { data: "userid", name: "userid" },
            { data: "nama", name: "nama" },
            { data: "roleuser", name: "roleuser" },
            {
                data: "aktif",
                name: "aktif",
                render: function (data) {
                    return data == 1 ? 'Aktif' : 'Tidak Aktif';
                }
            },
            { data: "inputuser", name: "inputuser" },
            { data: "inputtanggal", name: "inputtanggal" },
            { data: "updateuser", name: "updateuser" },
            { data: "updatetanggal", name: "updatetanggal" },
            {
                data: null,
                // render: function (data) {
                //     return `<button class="btn btn-sm btn-warning" onclick='openEditModal(${JSON.stringify(data)})'>Edit</button>
                //             <button class="btn btn-sm btn-danger ml-1" onclick='deleteUser("${data.userid}")'>Hapus</button>
                //     `;
                // }
                visible: !isUser, // <-- disembunyikan jika role 'user'
                render: function (data) {
                    let buttons = '';

                    if (userRole === 'root') {
                        buttons += `<button class="btn btn-sm btn-warning" onclick='openEditModal(${JSON.stringify(data)})'>Edit</button>`;
                        buttons += `<button class="btn btn-sm btn-danger ml-1" onclick='deleteUser("${data.userid}")'>Hapus</button>`;
                    } else if (userRole === 'admin') {
                        buttons += `<button class="btn btn-sm btn-warning" onclick='openEditModal(${JSON.stringify(data)})'>Edit</button>`;
                    }
                    // Untuk user biasa tidak ditampilkan tombol apa-apa

                    return buttons;
                }
            }
        ]
    });

    // Submit tambah user
    $('#addUserForm').submit(function (e) {
        e.preventDefault();
        $.post("{{ route('m_users.store') }}", $(this).serialize())
            .done(function (res) {
                $('#addUserModal').modal('hide');
                table.ajax.reload();
                alert(res.message);
            })
            .fail(function (xhr) {
                alert(xhr.responseJSON.message || 'Terjadi kesalahan.');
            });
    });

    // Submit edit user
    $('#editUserForm').submit(function (e) {
        e.preventDefault();
            $.post("{{ route('m_users.update') }}", $(this).serialize())
                .done(function (res) {
                    $('#editUserModal').modal('hide');
                    table.ajax.reload();
                    alert(res.message);
                })
                .fail(function (xhr) {
                    alert(xhr.responseJSON.message || 'Gagal update.');
                });
        });
    });

    function deleteUser(userid) {
        if (confirm('Yakin ingin menghapus user ini?')) {
            $.post("{{ route('m_users.destroy') }}", {
                _token: "{{ csrf_token() }}",
                userid: userid
            })
            .done(function (res) {
                $('#dataTable3').DataTable().ajax.reload();
                alert(res.message);
            })
            .fail(function (xhr) {
                alert(xhr.responseJSON.message || 'Gagal menghapus user.');
            });
        }
    }

    // Modal edit
    function openEditModal(data) {
        $('#edit_userid').val(data.userid);
        $('#edit_nama').val(data.nama);
        $('#edit_password').val(data.password);
        $('#edit_roleuser').val(data.roleuser);
        $('#edit_aktif').val(data.aktif);
        $('#editUserModal').modal('show');
    }
</script>
@endsection
