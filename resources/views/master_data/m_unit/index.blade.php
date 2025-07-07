@extends('layouts.master')

@section('content')
<div class="main-content-inner">
    <div class="row">
        <div class="col-12 mt-1">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Data Unit</h4>
                    {{-- <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addUnitModal">Tambah Unit</button> --}}
                    @if (session('role') === 'root' || session('role') === 'admin')
                        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addUnitModal">Tambah Unit</button>
                    @endif
                    <div class="data-tables datatable-dark">
                        <table id="unitTable" class="table table-striped table-bordered nowrap" style="width:100%">
                            <thead class="text-capitalize">
                                <tr>
                                    <th>Mitra ID</th>
                                    <th>Kode Unit</th>
                                    <th>Nama Unit</th>
                                    <th>No DB</th>
                                    <th>Key DB</th>
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

        <!-- Modal Tambah -->
        <div class="modal fade" id="addUnitModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <form id="addUnitForm">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Unit</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Mitra ID</label>
                                <input type="text" name="mitraid" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Kode Unit</label>
                                <input type="text" name="kdunit" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Nama Unit</label>
                                <input type="text" name="namaunit" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>No DB</label>
                                <input type="number" name="nodb" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Key DB</label>
                                <input type="text" name="keydb" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit -->
        <div class="modal fade" id="editUnitModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <form id="editUnitForm">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Unit</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="mitraid" id="edit_mitraid">
                            <input type="hidden" name="kdunit" id="edit_kdunit">
                            <div class="form-group">
                                <label>Nama Unit</label>
                                <input type="text" name="namaunit" id="edit_namaunit" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>No DB</label>
                                <input type="number" name="nodb" id="edit_nodb" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Key DB</label>
                                <input type="text" name="keydb" id="edit_keydb" class="form-control">
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

    const table = $('#unitTable').DataTable({
        scrollX: true,
        responsive: false,
        processing: true,
        serverSide: false,
        ajax: "{{ route('m_unit.data') }}",
        columns: [
            { data: "mitraid", name: "mitraid" },
            { data: "kdunit", name: "kdunit" },
            { data: "namaunit", name: "namaunit" },
            { data: "nodb", name: "nodb" },
            { data: "keydb", name: "keydb" },
            {
                data: null,
                visible: !isUser,
                render: function (data) {
                    let buttons = '';
                    if (userRole === 'root') {
                        buttons += `<button class="btn btn-sm btn-warning" onclick='openEditModal(${JSON.stringify(data)})'>Edit</button>`;
                        buttons += `<button class="btn btn-sm btn-danger ml-1" onclick='deleteUnit("${data.mitraid}", "${data.kdunit}")'>Hapus</button>`;
                    } else if (userRole === 'admin') {
                        buttons += `<button class="btn btn-sm btn-warning" onclick='openEditModal(${JSON.stringify(data)})'>Edit</button>`;
                    }
                    return buttons;
                }
            }
        ]
    });

    $('#addUnitForm').submit(function (e) {
        e.preventDefault();

        Swal.fire({
            title: 'Simpan Data?',
            text: "Apakah Anda yakin ingin menyimpan unit ini?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{ route('m_unit.store') }}", $(this).serialize())
                    .done(function (res) {
                        $('#addUnitModal').modal('hide');
                        $('#addUnitForm')[0].reset();
                        $('#unitTable').DataTable().ajax.reload();
                        Swal.fire('Berhasil', res.message, 'success');
                    })
                    .fail(function (xhr) {
                        Swal.fire('Gagal', xhr.responseJSON.message || 'Terjadi kesalahan.', 'error');
                    });
            }
        });
    });


    $('#editUnitForm').submit(function (e) {
        e.preventDefault();

        Swal.fire({
            title: 'Update Data?',
            text: "Apakah Anda yakin ingin mengupdate unit ini?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Update',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("{{ route('m_unit.update') }}", $(this).serialize())
                    .done(function (res) {
                        $('#editUnitModal').modal('hide');
                        $('#unitTable').DataTable().ajax.reload();
                        Swal.fire('Berhasil', res.message, 'success');
                    })
                    .fail(function (xhr) {
                        Swal.fire('Gagal', xhr.responseJSON.message || 'Gagal update.', 'error');
                    });
            }
        });
    });
});


function openEditModal(data) {
    $('#edit_mitraid').val(data.mitraid);
    $('#edit_kdunit').val(data.kdunit);
    $('#edit_namaunit').val(data.namaunit);
    $('#edit_nodb').val(data.nodb);
    $('#edit_keydb').val(data.keydb);
    $('#editUnitModal').modal('show');
}

function deleteUnit(mitraid, kdunit) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Unit ini akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("{{ route('m_unit.destroy') }}", {
                _token: "{{ csrf_token() }}",
                mitraid: mitraid,
                kdunit: kdunit
            })
            .done(function (res) {
                $('#unitTable').DataTable().ajax.reload();
                Swal.fire('Berhasil!', res.message, 'success');
            })
            .fail(function (xhr) {
                Swal.fire('Gagal!', xhr.responseJSON.message || 'Gagal menghapus unit.', 'error');
            });
        }
    });
}
</script>
@endsection

