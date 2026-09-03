@extends('layouts.master')

@section('title', 'Stock Requisitions')

@section('scripts')
    @parent

    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script type="text/javascript">
        $(function() {
            let ajaxUrl = "{{ route('stockitemrequisitions.index') }}";

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
                        data: 'requisition_date',
                        name: 'requisition_date',
                    },
                    {
                        data: 'requisition_number',
                        name: 'requisition_number',
                    },
                    {
                        data: 'fjob',
                        name: 'fjob'
                    },
                    {
                        data: 'stock_warehouse',
                        name: 'stock_warehouse',
                    },
                    {
                        data: 'requested_by',
                        name: 'requested_by',
                    },
                    {
                        data: 'approved_by',
                        name: 'approved_by',
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
        $(document).ready(function() {
            var select2Elements = $('.select2');

            if (select2Elements.length) {
                select2Elements.each(function() {
                    var $this = $(this);
                    $this.wrap('<div class="position-relative"></div>').select2({
                        placeholder: 'Select value',
                        dropdownParent: $this.parent()
                    });
                });
            }
        });
    </script>

@endsection

@section('styles')
    @parent
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endsection

@section('content')

    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
            <div class="d-flex flex-column justify-content-center">
                <h4 class="mb-1">Stock Requisitions</h4>
            </div>

            <div class="d-flex justify-content-end mb-3">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#addStockRequisitionModal">
                    <i class="icon-base ti tabler-plus"></i>
                    New Requisitions
                </button>

                <!-- Modal -->
                <div class="modal fade" id="addStockRequisitionModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addStockRequisitionModalTitle">Prepare Requisitions</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>

                            <form id="addStockRequisitionForm" method="POST"
                                action="{{ route('stockitemrequisitions.store-primary') }}">
                                @csrf
                                <div class="modal-body">

                                    <!-- Jobs -->
                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <label for="jobSelect" class="form-label">Jobs</label>
                                            <select id="jobSelect" class="select2 form-select" data-allow-clear="true"
                                                name="fjob_id">
                                                @foreach ($fjobs as $fjob)
                                                    <option value="{{ $fjob->id }}">
                                                        {{ $fjob->job_name }} - {{ $fjob->job_title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Warehouse & Date -->
                                    <div class="row g-4 mb-4">
                                        <div class="col mb-0">
                                            <label for="warehouseSelect" class="form-label">Warehouse</label>
                                            <select id="warehouseSelect" class="select2 form-select" data-allow-clear="true"
                                                name="stock_warehouse_id">
                                                @foreach ($warehouses as $warehouse)
                                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col mb-0">
                                            <label for="requisitionDate" class="form-label">Requisition Date</label>
                                            <input class="form-control" type="date" id="requisitionDate"
                                                name="requisition_date" value="{{ date('Y-m-d') }}" />
                                        </div>
                                    </div>

                                    <!-- Notes -->
                                    <div class="row g-4">
                                        <div class="col mb-0">
                                            <label for="notes" class="form-label">Notes</label>
                                            <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-label-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


            </div>
        </div>

        <div class="card">
            <div class="card-datatable table-responsive p-2">
                <table class="datatables-ajax table table-bordered" id="stock-categories-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>DATE</th>
                            <th>NUMBER</th>
                            <th>FOR JOB</th>
                            <th>WAREHOUSE</th>
                            <th>REQUESTED BY</th>
                            <th>APPROVED BY</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                </table>
            </div>



        </div>
    </div>
    <!-- / Content -->

@endsection
