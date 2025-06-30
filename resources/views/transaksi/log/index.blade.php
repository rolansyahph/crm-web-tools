@extends('layouts.master')

@section('content')
<div class="main-content-inner">
    <div class="row">
        <div class="col-12 mt-1">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Cek Log</h4>
                    <div class="data-tables datatable-dark">
                        <table id="logTable" class="table table-striped table-bordered nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Periode</th>
                                    <th>Datetime</th>
                                    <th>Fungsi</th>
                                    <th>Key1</th>
                                    <th>Key2</th>
                                    <th>Key3</th>
                                    <th>Pesan</th>
                                    <th>Input User</th>
                                    <th>Input Tanggal</th>
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
<script>
$(document).ready(function () {
    $('#logTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('log.data') }}",
        columns: [
            { data: "periode" },
            { data: "datetime" },
            { data: "fungsi" },
            { data: "key1" },
            { data: "key2" },
            { data: "key3" },
            { data: "pesan" },
            { data: "inputuser" },
            { data: "inputtanggal" }
        ],
        scrollX: true,        // ✅ Aktifkan scroll horizontal
        responsive: false     // ✅ Nonaktifkan mode responsif collapse
    });
});
</script>
@endsection
