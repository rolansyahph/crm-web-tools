@extends('layouts.master')

@section('content')
<div class="main-content-inner">
    <div class="row">
        <div class="col-12 mt-1">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Data Topup</h4>
                    <div class="data-tables datatable-dark">
                        <table id="topupTable" class="table table-striped table-bordered table-hover nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th style="display: none;">Id</th>
                                    <th>Datetime</th>
                                    <th>Via</th>
                                    <th>User ID</th>
                                    <th>Setor 1</th>
                                    <th>Setor 2</th>
                                    <th>Tiecket ID</th>
                                    <th>Bank ID</th>
                                    <th>No Rek</th>
                                    <th>Trx ID</th>
                                    <th>Keterangan</th>
                                    <th>Status</th>
                                    <th>Status Ket</th>
                                    <th>Input User</th>
                                    <th>Input Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="aksesModal" tabindex="-1" role="dialog" aria-labelledby="aksesModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="aksesForm">
        <div class="modal-header">
          <h5 class="modal-title">Konfirmasi Akses</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>User ID: <strong id="modalUserid">{{ session('userid') }}</strong></p>
          <input type="hidden" name="id" id="topupId">
          <div class="form-group">
            <label for="aksesPassword">Masukkan Password Anda</label>
            <input type="password" class="form-control" name="password" id="aksesPassword" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Konfirmasi</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        const table = $('#topupTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('topup_transaksi.data') }}",
            columns: [
                { data: "id", visible: false },
                { data: "datetime" },
                { data: "via" },
                { data: "userid" },
                {
                    data: "jmlsetor1",
                    render: $.fn.dataTable.render.number('.', ',', 0, '')
                },
                {
                    data: "jmlsetor2",
                    render: $.fn.dataTable.render.number('.', ',', 0, '')
                },
                { data: "ticketid" },
                { data: "bankid" },
                { data: "norek" },
                { data: "trxid" },
                { data: "ket" },
                { data: "status" },
                { data: "status_ket" },
                { data: "inputuser" },
                { data: "inputtanggal" },
                // {
                //     data: "id",
                //     render: function (data, type, row, meta) {
                //         return `<button class="btn btn-sm btn-warning update-status-btn" data-id="${data}">Update Status</button>`;
                //     },
                //     orderable: false,
                //     searchable: false
                // }
                {
                    data: "id",
                    render: function (data, type, row, meta) {
                        if (row.status == 0) {
                            return ''; // Hide the button
                        } else {
                            return `<button class="btn btn-sm btn-warning update-status-btn" data-id="${data}">Update Status</button>`;
                        }
                    },
                    orderable: false,
                    searchable: false
                }
            ],
            order: [[0, 'desc']],
            scrollX: true,
            responsive: false
        });

        let selectedId = null;

        // Tampilkan modal saat tombol update diklik
        $('#topupTable').on('click', '.update-status-btn', function () {
            selectedId = $(this).data('id');
            $('#topupId').val(selectedId);
            $('#aksesPassword').val('');
            $('#aksesModal').modal('show');
        });

        // Submit form konfirmasi password
        $('#aksesForm').submit(function (e) {
            e.preventDefault();
            const password = $('#aksesPassword').val();
            const id = $('#topupId').val();

            // Verifikasi password
            $.post("{{ route('topup.confirm_akses') }}", {
                password: password,
                _token: '{{ csrf_token() }}'
            })
            .done(function () {
                // Jika password benar, update status
                $.post("{{ route('topup.update_status') }}", {
                    id: id,
                    _token: '{{ csrf_token() }}'
                })
                .done(function (res2) {
                    $('#aksesModal').modal('hide');
                    table.ajax.reload();
                    Swal.fire('Berhasil', res2.message, 'success');
                })
                .fail(function (xhr2) {
                    Swal.fire('Gagal', xhr2.responseJSON.message || 'Gagal update status.', 'error');
                });
            })
            .fail(function (xhr) {
                Swal.fire('Gagal', xhr.responseJSON.message || 'Password salah.', 'error');
            });
        });
    });
</script>

@endsection
