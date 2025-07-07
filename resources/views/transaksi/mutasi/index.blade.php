@extends('layouts.master')

@section('content')
<div class="main-content-inner">
    <div class="row">
        <div class="col-12 mt-1">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Data Mutasi</h4>
                    <div class="data-tables datatable-dark">
                        <table id="mutasiTable" class="table table-striped table-bordered table-hover nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Datetime</th>
                                    <th>Mitra ID</th>
                                    <th>Periode</th>
                                    <th>DK</th>
                                    <th>Mutasi</th>
                                    <th>Saldo Akhir</th>
                                    <th>Bank</th>
                                    <th>No Rek</th>
                                    <th>Mutasi Info</th>
                                    <th>User ID</th>
                                    <th>Status</th>
                                    <th>Status Info</th>
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
        $('#mutasiTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('mutasi_transaksi.data') }}",
            columns: [
                { data: "datetime" },
                { data: "mitraid" },
                { data: "periode" },
                { data: "dk" },
                {
                    data: "mutasi",
                    render: $.fn.dataTable.render.number('.', ',', 0, '')
                },
                {
                    data: "saldoakhir",
                    render: $.fn.dataTable.render.number('.', ',', 0, '')
                },
                { data: "bank" },
                { data: "norek" },
                { data: "mutasiinfo" },
                { data: "userid" },
                { data: "status" },
                { data: "statusinfo" }
            ],
            order: [[0, 'desc']],
            scrollX: true,
            responsive: false
        });
    });
</script>

@endsection
