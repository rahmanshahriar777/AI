@extends('layouts.master')

@section('title', 'Quotation Templates')

@section('scripts')
@parent

<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/moment/moment.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>

<script type="text/javascript">
    $(function() {
        var table = $('.datatables-ajax').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('quotationtemplates.index') }}",

            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                
                {
                    data: 'template_name',
                    name: 'template_name',
                },
                {
                    data: 'job_type_name',
                    name: 'job_type_name',
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });
    });
</script>

<script>
    $(document).on('click', '.delete', function() {
        var id = $(this).data('id');

        if (confirm('Are you sure you want to delete this item?')) {
            $.ajax({
                url: 'quotationtemplates/' + id,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    alert('Deleted successfully!');
                    $('.datatables-ajax').DataTable().ajax.reload(); // Reload table
                },
                error: function(xhr) {
                    alert('Something went wrong while deleting.');
                }
            });
        }
    });
</script>

@endsection

@section('styles')
@parent
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />

<!-- Row Group CSS -->
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css')}}" />

@endsection

@section('content')

<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    <div
        class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
        <div class="d-flex flex-column justify-content-center">
            <h4 class="mb-1">Quotation Templates</h4>
        </div>
        <div class="d-flex align-content-center flex-wrap gap-4">
            <div class="d-flex gap-4">
                <a href="{{ route('quotationtemplates.create') }}" class="btn btn-label-primary">Create New</a>
                <a class="btn btn-label-warning">Archived</a>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-ajax table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>TEMPLATE NAME</th>
                        <th width="20%">JOB TYPE</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<!-- / Content -->

@endsection