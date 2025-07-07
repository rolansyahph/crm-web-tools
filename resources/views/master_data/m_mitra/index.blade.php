@extends('layouts.master')

@section('content')
<div class="main-content-inner">
    <div class="row">
        <!-- Tabel Mitra -->
        <div class="col-12 mt-1">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Data Mitra</h4>
                    @if (session('role') === 'root' || session('role') === 'admin')
                        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addMitraModal">Tambah Mitra</button>
                    @endif
                    <div class="data-tables datatable-dark">
                        <table id="mitraTable" class="table table-striped table-bordered nowrap" style="width:100%">
                            <thead class="text-capitalize">
                                <tr>
                                    <th>Mitra ID</th>
                                    <th>Nama</th>
                                    <th>Alamat</th>
                                    <th>Region</th>
                                    <th>Aktif</th>
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
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Tambah Mitra -->
        <div class="modal fade" id="addMitraModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <form id="addMitraForm">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Mitra</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Mitra ID</label>
                                <input type="text" name="mitraid" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Nama</label>
                                <input type="text" name="nama" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Alamat</label>
                                <textarea name="alamat" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Region</label>
                                <input type="text" name="region" class="form-control">
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

        <!-- Modal Edit Mitra -->
        <div class="modal fade" id="editMitraModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <form id="editMitraForm">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Mitra</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="mitraid" id="edit_mitraid">
                            <div class="form-group">
                                <label>Nama</label>
                                <input type="text" name="nama" id="edit_nama" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Alamat</label>
                                <textarea name="alamat" id="edit_alamat" class="form-control"rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Region</label>
                                <input type="text" name="region" id="edit_region" class="form-control">
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function () {
    if ($.fn.DataTable.isDataTable('#mitraTable')) {
        $('#mitraTable').DataTable().clear().destroy();
    }

    const userRole = "{{ session('role') }}";
    const isUser = userRole === 'user';

    const table = $('#mitraTable').DataTable({
        scrollX: true,
        responsive: false,
        processing: true,
        serverSide: false,
        ajax: "{{ route('m_mitra.data') }}",
        columns: [
            { data: "mitraid", name: "mitraid" },
            { data: "nama", name: "nama" },
            { data: "alamat", name: "alamat" },
            { data: "region", name: "region" },
            { data: "aktif", name: "aktif" },
            { data: "inputuser", name: "inputuser" },
            { data: "inputtanggal", name: "inputtanggal" },
            { data: "updateuser", name: "updateuser" },
            { data: "updatetanggal", name: "updatetanggal" },
            {
                data: null,
                visible: !isUser,
                render: function (data) {
                    let buttons = '';
                    if (userRole === 'root') {
                        buttons += `<button class="btn btn-sm btn-warning" onclick='openEditModal(${JSON.stringify(data)})'>Edit</button>`;
                        buttons += `<button class="btn btn-sm btn-danger ml-1" onclick='deleteMitra("${data.mitraid}")'>Hapus</button>`;
                    } else if (userRole === 'admin') {
                        buttons += `<button class="btn btn-sm btn-warning" onclick='openEditModal(${JSON.stringify(data)})'>Edit</button>`;
                    }
                    return buttons;
                }
            }
        ]
    });

    // Submit tambah mitra
    $('#addMitraForm').submit(function (e) {
        e.preventDefault();

        Swal.fire({
            title: 'Simpan Data?',
            text: 'Apakah Anda yakin ingin menyimpan data mitra ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{ route('m_mitra.store') }}", $(this).serialize())
                    .done(function (res) {
                        $('#addMitraModal').modal('hide');
                        $('#addMitraForm')[0].reset();
                        table.ajax.reload();
                        Swal.fire('Berhasil', res.message, 'success');
                    })
                    .fail(function (xhr) {
                        Swal.fire('Gagal', xhr.responseJSON.message || 'Terjadi kesalahan.', 'error');
                    });
            }
        });
    });


    // Submit edit mitra
    $('#editMitraForm').submit(function (e) {
        e.preventDefault();

        Swal.fire({
            title: 'Update Data?',
            text: 'Apakah Anda yakin ingin mengupdate data mitra ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Update',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{ route('m_mitra.update') }}", $(this).serialize())
                    .done(function (res) {
                        $('#editMitraModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Berhasil', res.message, 'success');
                    })
                    .fail(function (xhr) {
                        Swal.fire('Gagal', xhr.responseJSON.message || 'Gagal update.', 'error');
                    });
            }
        });
    });
});


// Modal edit
function openEditModal(data) {
    $('#edit_mitraid').val(data.mitraid);
    $('#edit_nama').val(data.nama);
    $('#edit_alamat').val(data.alamat);
    $('#edit_region').val(data.region);
    $('#edit_aktif').val(data.aktif);
    $('#editMitraModal').modal('show');
}

// SweetAlert konfirmasi hapus
function deleteMitra(mitraid) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data mitra ini akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("{{ route('m_mitra.destroy') }}", {
                _token: "{{ csrf_token() }}",
                mitraid: mitraid
            })
            .done(function (res) {
                $('#mitraTable').DataTable().ajax.reload();
                Swal.fire('Berhasil!', res.message, 'success');
            })
            .fail(function (xhr) {
                Swal.fire('Gagal!', xhr.responseJSON.message || 'Gagal menghapus mitra.', 'error');
            });
        }
    });
}
</script>
@endsection

