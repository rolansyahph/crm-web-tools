@extends('layouts.master')

@section('content')
<div class="main-content-inner">
    <div class="row">
        <div class="col-12 mt-1">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Data Inbox</h4>
                    <div class="data-tables datatable-dark">
                        <table id="inboxTable" class="table table-striped table-bordered table-hover nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Datetime</th>
                                    <th>Fungsi</th>
                                    <th>Mitra ID</th>
                                    <th>Via</th>
                                    <th>Via ID</th>
                                    <th>Pesan</th>
                                    <th>User ID</th>
                                    <th>Ticket ID</th>
                                    <th>Input User</th>
                                    <th>Input Tanggal</th>
                                    <th>Update User</th>
                                    <th>Update Tanggal</th>
                                    <th>Status</th>
                                    <th>Status Reset</th>
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
        $('#inboxTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('inbox_transaksi.data') }}",
            columns: [
                { data: "datetime" },
                { data: "fungsi" },
                { data: "mitraid" },
                { data: "via" },
                { data: "viaid" },
                { data: "pesan" },
                { data: "userid" },
                { data: "ticketid" },
                { data: "inputuser" },
                { data: "inputtanggal" },
                { data: "updateuser" },
                { data: "updatetanggal" },
                { data: "status" },
                { data: "statusreset" }
            ],
            order: [[0, 'desc']],
            scrollX: true
        });
    });
</script>
@endsection
