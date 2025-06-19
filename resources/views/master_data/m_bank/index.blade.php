@extends('layouts.master')

@section('content')
<div class="main-content-inner">
    <div class="row">
        <div class="col-12 mt-1">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Data Bank</h4>
                    @if (session('role') === 'root' || session('role') === 'admin')
                        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addBankModal">Tambah Bank</button>
                    @endif
                    <div class="data-tables datatable-dark">
                        <table id="bankTable" class="table table-striped table-bordered nowrap" style="width:100%">
                            <thead class="text-capitalize">
                                <tr>
                                    <th>Mitra ID</th>
                                    <th>Bank ID</th>
                                    <th>Bank Server</th>
                                    <th>Bank Client</th>
                                    <th>Nama</th>
                                    <th>No Rekening</th>
                                    <th>Gangguan Status</th>
                                    <th>Gangguan Keterangan</th>
                                    <th>Input User</th>
                                    <th>Input Tgl</th>
                                    <th>Update User</th>
                                    <th>Update Tgl</th>
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

        {{-- Modal Tambah --}}
        <div class="modal fade" id="addBankModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <form id="addBankForm">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Data Bank</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body">
                            @foreach ([
                                'mitraid', 'bank_id', 'bank_server', 'bank_client',
                                'nama', 'norek', 'gangguan_sts', 'gangguan_ket'
                            ] as $field)
                                <div class="form-group">
                                    <label>{{ ucwords(str_replace('_', ' ', $field)) }}</label>
                                    @if ($field === 'gangguan_ket')
                                        <textarea name="{{ $field }}" class="form-control" rows="3" required></textarea>
                                    @else
                                        <input type="{{ $field === 'gangguan_sts' ? 'number' : 'text' }}"
                                            name="{{ $field }}" class="form-control" required>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Edit --}}
        <div class="modal fade" id="editBankModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <form id="editBankForm">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Data Bank</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="mitraid" id="edit_mitraid">
                            <input type="hidden" name="bank_id" id="edit_bank_id">
                           @foreach ([
                                'bank_server', 'bank_client', 'nama', 'norek',
                                'gangguan_sts', 'gangguan_ket'
                            ] as $field)`
                                <div class="form-group">
                                    <label>{{ ucwords(str_replace('_', ' ', $field)) }}</label>
                                    @if ($field === 'gangguan_ket')
                                        <textarea name="{{ $field }}" id="edit_{{ $field }}" class="form-control" rows="3" required></textarea>
                                    @else
                                        <input type="{{ $field === 'gangguan_sts' ? 'number' : 'text' }}"
                                            name="{{ $field }}" id="edit_{{ $field }}" class="form-control" required>
                                    @endif
                                </div>
                            @endforeach
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
    if ($.fn.DataTable.isDataTable('#mitraTable')) {
        $('#mitraTable').DataTable().clear().destroy();
    }

    const userRole = "{{ session('role') }}";
    const isUser = userRole === 'user';

    const table = $('#bankTable').DataTable({
        scrollX: true,
        responsive: true,
        processing: true,
        ajax: "{{ route('m_bank.data') }}",
        columns: [
            { data: 'mitraid' },
            { data: 'bank_id' },
            { data: 'bank_server' },
            { data: 'bank_client' },
            { data: 'nama' },
            { data: 'norek' },
            { data: 'gangguan_sts' },
            { data: 'gangguan_ket' },
            { data: 'input_user' },
            { data: 'input_tgl' },
            { data: 'lastupdate_user' },
            { data: 'lastupdate_tgl' },
            {
                data: null,
                visible: !isUser, // <-- disembunyikan jika role 'user'
                render: function (data) {
                    let buttons = '';

                    if (userRole === 'root') {
                        buttons += `<button class="btn btn-sm btn-warning" onclick='editBank(${JSON.stringify(data)})'>Edit</button>`;
                        buttons += `<button class="btn btn-sm btn-danger ml-1" onclick='deleteBank("${data.bank_id}")'>Hapus</button>`;
                    } else if (userRole === 'admin') {
                        buttons += `<button class="btn btn-sm btn-warning" onclick='editBank(${JSON.stringify(data)})'>Edit</button>`;
                    }
                    // Untuk user biasa tidak ditampilkan tombol apa-apa

                    return buttons;
                }
            }
        ]
    });

    $('#addBankForm').submit(function (e) {
        e.preventDefault();
        $.post("{{ route('m_bank.store') }}", $(this).serialize())
            .done(res => {
                $('#addBankModal').modal('hide');
                table.ajax.reload();
                alert(res.message);
            })
            .fail(xhr => alert(xhr.responseJSON.message || 'Gagal menambah data.'));
    });

    $('#editBankForm').submit(function (e) {
        e.preventDefault();
        $.post("{{ route('m_bank.update') }}", $(this).serialize())
            .done(res => {
                $('#editBankModal').modal('hide');
                table.ajax.reload();
                alert(res.message);
            })
            .fail(xhr => alert(xhr.responseJSON.message || 'Gagal mengubah data.'));
    });
});

function editBank(data) {
    $('#edit_mitraid').val(data.mitraid);
    $('#edit_bank_id').val(data.bank_id);
    $('#edit_bank_server').val(data.bank_server);
    $('#edit_bank_client').val(data.bank_client);
    $('#edit_nama').val(data.nama);
    $('#edit_norek').val(data.norek);
    $('#edit_gangguan_sts').val(data.gangguan_sts);
    $('#edit_gangguan_ket').val(data.gangguan_ket);
    $('#editBankModal').modal('show');
}

function deleteBank(mitraid, bank_id) {
    if (confirm('Yakin ingin menghapus data bank ini?')) {
        $.post("{{ route('m_bank.destroy') }}", {
            _token: "{{ csrf_token() }}",
            mitraid, bank_id
        })
        .done(res => {
            $('#bankTable').DataTable().ajax.reload();
            alert(res.message);
        })
        .fail(xhr => alert(xhr.responseJSON.message || 'Gagal menghapus data.'));
    }
}
</script>
@endsection
