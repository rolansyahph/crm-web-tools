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
                                    <th>Datetime</th>
                                    <th>Via</th>
                                    <th>User ID</th>
                                    <th>Setor 1</th>
                                    <th>Setor 2</th>
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
@endsection

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        const table = $('#topupTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('topup.data') }}",
            columns: [
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
                { data: "bankid" },
                { data: "norek" },
                { data: "trxid" },
                { data: "ket" },
                { data: "status" },
                { data: "status_ket" },
                { data: "inputuser" },
                { data: "inputtanggal" },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return `
                            <button class="btn btn-sm btn-warning reset-status-btn" data-id="${row.id}">
                                Reset Status
                            </button>
                        `;
                    }
                }
            ],
            order: [[0, 'desc']],
            scrollX: true,
            responsive: false
        });

        // Reset status button click
        $('#topupTable').on('click', '.reset-status-btn', function () {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Reset Status?',
                text: 'Apakah Anda yakin ingin mereset status menjadi 0?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Reset',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/transaksi/topup/reset-status/${id}`,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (res) {
                            Swal.fire('Berhasil!', res.message, 'success');
                            table.ajax.reload(null, false);
                        },
                        error: function () {
                            Swal.fire('Gagal!', 'Terjadi kesalahan saat mengupdate.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
