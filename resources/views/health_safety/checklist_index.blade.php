@extends('layouts.master')

@section('title', 'Health & Safety Checklists')

@section('scripts')
    @parent

    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>

    <script type="text/javascript">
        $(function() {
            let ajaxUrl = "{{ route('hs-checklists.index') }}";

            var table = $('.datatables-ajax').DataTable({
                processing: true,
                serverSide: true,
                ajax: ajaxUrl,

                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'title',
                        name: 'title',
                    },
                    {
                        data: 'short_details',
                        name: 'short_details',
                    },
                    {
                        data: 'type',
                        name: 'type',
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
@endsection

@section('styles')
    @parent
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
@endsection

@section('content')

    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
            <div class="d-flex flex-column justify-content-center">
                <h4 class="mb-1">Health & Safety Checklists</h4>
            </div>
            <div class="d-flex align-content-center flex-wrap gap-4">
                <div class="d-flex gap-4">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addHsChecklist">
                        <i class="icon-base ti tabler-plus"></i>
                        Add New Checklist
                    </button>

                    <!--Add Modal -->
                    <div class="modal fade" id="addHsChecklist" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog  modal-md" role="document">
                            <form action="{{ route('hs-checklists.store') }}" method="POST">
                                @csrf
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addHsChecklistTitle">Health & Safety Checklist</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col mb-4">
                                                <label for="title" class="form-label">Title</label>
                                                <input type="text" class="form-control" name="title"
                                                    id="title"required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col mb-4">
                                                <label for="short-description" class="form-label">Description</label>
                                                <textarea id="short-description" class="form-control" name="short_details" rows="3"
                                                    placeholder="Enter short description here..."></textarea>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col mb-4">
                                                <label class="form-label" for="type">Type</label>
                                                <select id="type" class="form-select" name="type" required>
                                                    <option value="">Select Type</option>
                                                    <option value="jobs">For Jobs</option>
                                                    <option value="equipments">For Equipments</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                                            Close
                                        </button>
                                        <button type="submit" class="btn btn-primary">Create</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>

            </div>

        </div>
        <div class="card">
            <div class="card-datatable table-responsive p-2">
                <table class="datatables-ajax table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>TITLE</th>
                            <th width="20%">SHORT DESC.</th>
                            <th>TYPE</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <!-- / Content -->



@endsection
