@extends('layouts.master')

@section('content')
<div class="main-content-inner">
    <div class="row">
        <div class="col-12 mt-1">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Data User</h4>
                    @if (session('role') === 'root' || session('role') === 'admin')
                        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addUserModal">Tambah User</button>
                    @endif
                    <div class="data-tables datatable-dark">
                        <table id="dataTable3" class="table table-striped table-bordered nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Nama</th>
                                    <th>Role</th>
                                    <th>Mitra ID</th>
                                    <th>Status</th>
                                    <th>Input User</th>
                                    <th>Input Tanggal</th>
                                    <th>Update User</th>
                                    <th>Update Tanggal</th>
                                    @if (session('role') === 'user')
                                        <th style="display:none;">Aksi</th>
                                    @else
                                        <th>Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Tambah --}}
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
                                <select name="roleuser" class="form-control">
                                    <option value="root">root</option>
                                    <option value="admin">admin</option>
                                    <option value="user">user</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Mitra ID</label>
                                <select name="mitraid" class="form-control" required>
                                    <option value="DEV">DEV</option>
                                    <option value="SS">SS</option>
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

        {{-- Modal Edit --}}
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
                                <label>Mitra ID</label>
                                <select name="mitraid" id="edit_mitraid" class="form-control" required>
                                    <option value="DEV">DEV</option>
                                    <option value="SS">SS</option>
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
            { data: "userid" },
            { data: "nama" },
            { data: "roleuser" },
            { data: "mitraid" },
            {
                data: "aktif",
                render: function (data) {
                    return data == 1 ? 'Aktif' : 'Tidak Aktif';
                }
            },
            { data: "inputuser" },
            { data: "inputtanggal" },
            { data: "updateuser" },
            { data: "updatetanggal" },
            {
                data: null,
                visible: !isUser,
                render: function (data) {
                    let buttons = '';
                    if (userRole === 'root') {
                        buttons += `<button class="btn btn-sm btn-warning" onclick='openEditModal(${JSON.stringify(data)})'>Edit</button>`;
                        buttons += `<button class="btn btn-sm btn-danger ml-1" onclick='deleteUser("${data.userid}")'>Hapus</button>`;
                    } else if (userRole === 'admin') {
                        buttons += `<button class="btn btn-sm btn-warning" onclick='openEditModal(${JSON.stringify(data)})'>Edit</button>`;
                    }
                    return buttons;
                }
            }
        ]
    });

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

    window.openEditModal = function (data) {
        $('#edit_userid').val(data.userid);
        $('#edit_nama').val(data.nama);
        $('#edit_password').val(data.password);
        $('#edit_roleuser').val(data.roleuser);
        $('#edit_mitraid').val(data.mitraid);
        $('#edit_aktif').val(data.aktif);
        $('#editUserModal').modal('show');
    }

    window.deleteUser = function (userid) {
        if (confirm('Yakin ingin menghapus user ini?')) {
            $.post("{{ route('m_users.destroy') }}", {
                _token: "{{ csrf_token() }}",
                userid: userid
            })
            .done(function (res) {
                table.ajax.reload();
                alert(res.message);
            })
            .fail(function (xhr) {
                alert(xhr.responseJSON.message || 'Gagal menghapus user.');
            });
        }
    }
});
</script>
@endsection
