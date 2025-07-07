@extends('layouts.master')

@section('content')
<div class="main-content-inner">
    <div class="row">
        <div class="col-12 mt-1">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Data Ticket</h4>
                    <div class="data-tables datatable-dark">
                        <table id="ticketTable" class="table table-striped table-bordered table-hover nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Datetime</th>
                                    <th>Mitra ID</th>
                                    <th>User ID</th>
                                    <th>Bank ID</th>
                                    <th>No Rek</th>
                                    <th>Total</th>
                                    <th>Ticket ID</th>
                                    <th>Ticket Expired</th>
                                    <th>Status Ticket</th>
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
        $('#ticketTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('ticket_transaksi.data') }}",
            columns: [
                { data: "datetime" },
                { data: "mitraid" },
                { data: "userid" },
                { data: "bankid" },
                { data: "norek" },
                {
                    data: "total",
                    render: $.fn.dataTable.render.number('.', ',', 0, '')
                },
                { data: "ticketid" },
                { data: "ticketexpired" },
                { data: "statusticket" }
            ],
            order: [[0, 'desc']],
            scrollX: true
        });
    });
</script>


@endsection
