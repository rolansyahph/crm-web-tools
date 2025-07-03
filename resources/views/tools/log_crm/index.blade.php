@extends('layouts.master')

@section('content')
<div class="main-content-inner">
    <div class="row">
        <div class="col-12 mt-1">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Log CRM</h4>
                    <div class="data-tables datatable-dark">
                        <table id="logCRMTable" class="table table-striped table-bordered table-hover nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    {{-- <th>Periode</th> --}}
                                    <th>Datetime</th>
                                    <th>Fungsi</th>
                                    <th>User Update</th>
                                    <th>Pesan</th>
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
    $('#logCRMTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('log_crm.data') }}",
        columns: [
            // { data: "periode" },
            { data: "datetime" },
            { data: "fungsi" },
            { data: "userupdate" },
            { data: "pesan" }
        ],
        order: [[0, 'desc']],
        scrollX: true,
        responsive: false
    });
});
</script>
@endsection
