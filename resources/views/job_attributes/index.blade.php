@extends('layouts.master')

@section('title', 'Job Attributes')

@section('scripts')
    @parent

    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/js/jobattribute-index.js') }}"></script>

    <script>
        $(function() {
            let table = $('#jobAttributeTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('job-attributes.index') }}',
                columns: [{
                        className: 'dt-control',
                        orderable: false,
                        data: null,
                        defaultContent: '',
                    },
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            // Format function for nested row
            function format(d) {
                if (!d.attributedetails || d.attributedetails.length === 0) {
                    return '<div class="text-muted ps-4">No attribute details found.</div>';
                }

                let html = '<table class="table table-sm table-bordered w-75 ms-4">';
                html += '<thead><tr><th>#</th><th>Value</th><th>Action</th></tr></thead><tbody>';

                d.attributedetails.forEach(function(detail, index) {
                    html += `<tr>
                    <td>${index + 1}</td>
                    <td>${detail.value}</td>
                    <td>
                        <button class="btn btn-sm btn-warning edit-detail-btn"
                                data-id="${detail.id}"
                                data-value="${detail.value}"
                                data-description="${detail.description || ''}"
                                data-bs-toggle="modal"
                                data-bs-target="#editDetailModal">
                            Edit
                        </button>
                    </td>
                </tr>`;
                });

                html += '</tbody></table>';
                return html;
            }


            // Handle opening/closing details
            $('#jobAttributeTable tbody').on('click', 'td.dt-control', function() {
                let tr = $(this).closest('tr');
                let row = table.row(tr);

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    row.child(format(row.data())).show();
                    tr.addClass('shown');
                }
            });
        });
    </script>


@endsection

@section('styles')
    @parent
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />

    <!-- Row Group CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css') }}" />

@endsection

@section('content')

    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
            <div class="d-flex flex-column justify-content-center">
                <h4 class="mb-1">Job Attributes</h4>
            </div>
            <div class="d-flex align-content-center flex-wrap gap-4">
                <div class="d-flex gap-4">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#JobAttributesModal">
                        Add Attribute
                    </button>
                </div>
            </div>
            <!-- Add job attribute Modal -->
            <div class="modal fade" id="JobAttributesModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="JobAttributesModalTitle">Job Attribute</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="jobAttributeForm">
                            @csrf
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col mb-4">
                                        <label for="attributename" class="form-label">Name</label>
                                        <input type="text" id="attributename" class="form-control" name="attributename"
                                            placeholder="Enter Name" />
                                    </div>
                                </div>
                                <div class="row g-4">
                                    <div class="col mb-0">
                                        <label for="attributedescription" class="form-label">Description</label>
                                        <textarea id="attributedescription" class="form-control" rows="3" placeholder="Enter Description"
                                            name="attributedescription"></textarea>
                                    </div>

                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                                    Close
                                </button>
                                <button type="button" class="btn btn-primary" onclick="addJobAttributes()">Save
                                    changes</button>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Update job attribute Modal -->
            <div class="modal fade" id="updateJobAttributesModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="updateJobAttributesModalTitle">Update Job Attribute</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="updateJobAttributeForm">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="attribute_id" id="attributrId">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col mb-4">
                                        <label for="updateattributename" class="form-label">Name</label>
                                        <input type="text" id="updateattributename" class="form-control" name="updateattributename"
                                            placeholder="Enter Name" />
                                    </div>
                                </div>
                                <div class="row g-4">
                                    <div class="col mb-0">
                                        <label for="updateattributedescription" class="form-label">Description</label>
                                        <textarea id="updateattributedescription" class="form-control" rows="3" placeholder="Enter Description"
                                            name="updateattributedescription"></textarea>
                                    </div>

                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                                    Close
                                </button>
                                <button type="button" class="btn btn-primary" onclick="updateJobAttributes()">Save
                                    changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
        <!-- Add Detail Modal -->
        <div class="modal fade" id="addDetailModal" tabindex="-1">
            <div class="modal-dialog">
                <form class="modal-content" id="addDetailForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Add Attribute Detail</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="parentAttributeId" name="job_attribute_id">
                        <div class="mb-3">
                            <label for="detailValue" class="form-label">Attribute</label>
                            <input type="text" class="form-control" id="detailValue" name="detailValue">
                        </div>
                        <div class="mb-3">
                            <label for="detailDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="detailDescription" name="detailDescription" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Detail</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Detail Modal -->
        <div class="modal fade" id="editDetailModal" tabindex="-1">
            <div class="modal-dialog">
                <form class="modal-content" id="editDetailForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="detail_id" id="editDetailId">

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Attribute Detail</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Attribute</label>
                            <input type="text" class="form-control" name="editDetailValue" id="editDetailValue">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="editDetailDescription" id="editDetailDescription" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-datatable table-responsive pt-0">
                <table class="table table-bordered datatables-ajax" id="jobAttributeTable">
                    <thead>
                        <tr>
                            <th></th> {{-- For the expand/collapse icon --}}
                            <th>#</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <!-- / Content -->

@endsection
