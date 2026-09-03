@extends('layouts.master')

@section('title', 'Requisition Details')

@section('scripts')
    @parent
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/pickr/pickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script>
        $('#sendInvoiceOffcanvas').on('shown.bs.offcanvas', function() {
            $('#variant-item').select2({
                dropdownParent: $('#sendInvoiceOffcanvas') // important for inside offcanvas/modal
            });
        });

        // Listen for change
        $(document).on('change', '#variant-item', function() {
            extactavqnunit();
        });

        function extactavqnunit() {
            let select = document.getElementById("variant-item");
            let selectedValue = select.value;
            if (selectedValue) {
                let [id, quantity, unit] = selectedValue.split("_");
                document.getElementById("available-quantity").value = quantity;
                document.getElementById("unit").value = unit;
            } else {
                document.getElementById("available-quantity").value = "";
                document.getElementById("unit").value = "";
            }
        }
    </script>

@endsection

@section('styles')
    @parent
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/pickr/pickr-themes.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endsection

@section('content')

    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row invoice-add">
            <!-- Invoice Add-->
            <div class="col-lg-9 col-12 mb-lg-0 mb-6">
                <div class="card invoice-preview-card p-sm-12 p-6">
                    <div class="card-body invoice-preview-header rounded">
                        <div class="d-flex flex-wrap flex-column flex-sm-row justify-content-between text-heading">
                            <div class="mb-md-0 mb-6">
                                <div class="d-flex svg-illustration mb-6 gap-2 align-items-center">
                                    <span class="app-brand-text fw-bold fs-4 ms-50">Requisition Details</span>
                                </div>
                                <p class="mb-2">
                                    <strong>For Job: </strong> {{ $requisition->fjob->job_name }} -
                                    {{ $requisition->fjob->job_title }}
                                </p>
                                <p class="mb-2">
                                    <strong>Warehouse: </strong> {{ $requisition->stockWarehouse->name }}
                                </p>
                                <p class="mb-2">
                                    <strong>Requested By: </strong> {{ $requisition->requestedBy->name ?? 'N/A' }}
                                </p>
                                <p class="mb-2">
                                    <strong>Approved By: </strong> {{ $requisition->approved_by ?? 'Not Approved' }}
                                </p>
                            </div>
                            <div class="col-md-5 col-8 pe-0 ps-0 ps-md-2">
                                <dl class="row mb-0 gx-4">
                                    <dt class="col-sm-5 mb-2 d-md-flex align-items-center justify-content-end">
                                        <span class="h5 text-capitalize mb-0 text-nowrap">Requisition No#</span>
                                    </dt>
                                    <dd class="col-sm-7">
                                        <input type="text" class="form-control" disabled
                                            placeholder="{{ $requisition->requisition_number }}"
                                            value="{{ $requisition->requisition_number }}" />
                                    </dd>
                                    <dt class="col-sm-5 mb-1 d-md-flex align-items-center justify-content-end">
                                        <span class="fw-normal">Date Issued:</span>
                                    </dt>
                                    <dd class="col-sm-7">
                                        <input type="text" class="form-control invoice-date" placeholder="MM/DD/YYYY"
                                            value="{{ $requisition->requisition_date }}" />
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <hr class="mt-0 mb-6" />
                    <div class="card-body pt-0 px-0">
                        <div class="d-flex justify-content-between align-items-center mb-6">
                            <h5 class="mb-0">Requisition Items</h5>
                            <button class="btn btn-primary btn-md mb-4" data-bs-toggle="offcanvas"
                                data-bs-target="#sendInvoiceOffcanvas">
                                <span class="d-flex align-items-center justify-content-center text-nowrap">
                                    <i class="icon-base ti tabler-plus icon-xs me-1_5"></i>Add Item
                                </span>
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Quantity</th>
                                        <th>Unit</th>
                                        <th>Notes</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($requisition->details as $detail)
                                        <tr>
                                            <td>{{ $detail->stockitemvariant->name }}</td>
                                            <td>{{ $detail->quantity }}</td>
                                            <td>{{ $detail->stockitemvariant->unit }}</td>
                                            <td>{{ $detail->notes }}</td>
                                            <td>
                                                <a href="{{ route('stockitemrequisitions.show', $requisition->id) }}"
                                                    class="btn btn-sm btn-primary">Delete</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <hr class="my-0" />
                    <div class="card-body px-0 pb-0">
                        <div class="row">
                            <div class="col-12">
                                <div>
                                    <label for="note" class="text-heading mb-1 fw-medium">Note:</label>
                                    <textarea class="form-control" rows="2" id="note" placeholder="Invoice note">
                                    </textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Invoice Add-->

            <!-- Invoice Actions -->
            <div class="col-lg-3 col-12 invoice-actions">
                <div class="card mb-6">
                    <div class="card-body">
                        <a href="#" class="btn btn-label-secondary d-grid w-100 mb-4">Approve</a>
                        <button type="button" class="btn btn-label-secondary d-grid w-100">Print</button>
                    </div>
                </div>
            </div>
            <!-- /Invoice Actions -->
        </div>

        <!-- Offcanvas -->
        <!-- Send Invoice Sidebar -->
        <div class="offcanvas offcanvas-end" id="sendInvoiceOffcanvas" aria-hidden="false" aria-modal="true" role="dialog">
            <div class="offcanvas-header mb-6 border-bottom">
                <h5 class="offcanvas-title">Add Items</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body pt-0 flex-grow-1">
                <form action="{{ route('stockitemrequisitions.store-details') }}" method="POST">
                    @csrf
                    <input type="hidden" name="stock_item_requisition_id" value="{{ $requisition->id }}" />
                    <div class="mb-6">
                        <label for="variant-item" class="form-label">Items</label>
                        <select id="variant-item" class="form-select" name="stock_item_id" required>
                            <option value="">Select Item</option>
                            @foreach ($stockitems as $item)
                                <option value="{{ $item->id }}_{{ $item->quantity }}_{{ $item->unit }}">
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-6">
                        <label for="available-quantity" class="form-label">Available Quantity</label>
                        <input type="number" class="form-control" id="available-quantity" value="" placeholder="0"
                            readonly />
                    </div>
                    <div class="mb-6">
                        <label for="unit" class="form-label">Unit</label>
                        <input type="text" class="form-control" id="unit" name="unit" placeholder="pcs"
                            readonly />
                    </div>
                    <div class="mb-6">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity"
                            placeholder="Enter quantity" required />
                    </div>
                    <div class="mb-6">
                        <label for="note" class="form-label">Notes</label>
                        <textarea class="form-control" id="note" name="notes" rows="3"
                            placeholder="Enter any additional notes"></textarea>
                    </div>


                    <div class="mb-6 d-flex flex-wrap">
                        <button type="submit" class="btn btn-primary me-4" data-bs-dismiss="offcanvas">Send</button>
                        <button type="button" class="btn btn-label-secondary"
                            data-bs-dismiss="offcanvas">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- /Send Invoice Sidebar -->

        <!-- /Offcanvas -->
    </div>
    <!-- / Content -->

@endsection
