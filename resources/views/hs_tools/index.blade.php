@extends('layouts.master')

@section('title', 'Hs Tools')

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
            ajax: "{{ route('hs-tools.index') }}",

            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },

                {
                    data: 'tool_name',
                    name: 'tool_name',
                },
                {
                    data: 'description',
                    name: 'description',
                    render: function(data, type, row) {
                        // Strip HTML tags
                        var div = document.createElement("div");
                        div.innerHTML = data;
                        var text = div.textContent || div.innerText || "";

                        // Truncate to 200 chars
                        var truncated = text.length > 200 ? text.substring(0, 200) + "..." : text;

                        return truncated;
                    }
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

    

    $(document).on('click', '.delete', function() {
        var id = $(this).data('id');

        if (confirm('Are you sure you want to delete this item?')) {
            $.ajax({
                url: '/hs-toolss/' + id,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    toastr.success('Job Type deleted successfully!');
                    $('.datatables-ajax').DataTable().ajax.reload(); // Reload table
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON.message || 'An error occurred.');
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
            <h4 class="mb-1">HS Tools</h4>
        </div>
        <div class="d-flex align-content-center flex-wrap gap-4">

            <button
                type="button"
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#createhsToolsModal">
                <i class="fa-solid fa-plus me-1_5"></i>
                New HS Tools
            </button>

            <!-- Modal -->
            <div class="modal fade" id="createhsToolsModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalCenterTitle">Create HS Tools</h5>
                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form id="hsToolsForm" method="POST" action="{{ route('hs-tools.store') }}">
                            @csrf
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col mb-4">
                                        <label for="toolName" class="form-label">Tool Name</label>
                                        <input
                                            type="text"
                                            id="toolName"
                                            name="toolName"
                                            class="form-control"
                                            placeholder="Enter tool name" required />
                                    </div>
                                </div>
                                <div class="row g-4">
                                    <div class="col mb-0">
                                        <label for="toolDetails" class="form-label">Details</label>
                                        <textarea
                                            id="toolDetails"
                                            name="toolDetails"
                                            class="form-control"
                                            placeholder="Enter Details"
                                            rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-ajax table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>NAME</th>
                        <th>DESCRIPTION</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<!-- / Content -->

@endsection