@extends('layouts.master')

@section('title', 'Items Inventory')

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
            let ajaxUrl = "{{ route('stockitemsinventory.index') }}";

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
                        data: 'name',
                        name: 'name',
                    },
                    {
                        data: 'warehouse',
                        name: 'warehouse.name',
                    },
                    {
                        data: 'quantity',
                        name: 'quantity',
                    },
                    {
                        data: 'avg_price',
                        name: 'avg_price',
                        render: function(data, type, row) {
                            return '$' + parseFloat(data).toFixed(2);
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
    <script>
        function openTransferStockModal(id) {
            // Fetch item data via AJAX
            $.ajax({
                url: `/stockitemsinventory/${id}`,
                type: 'GET',
                success: function(response) {
                    if (response.status) {
                        const item = response.data;
                        let quantity = item.quantity + ' ' + item.unit;
                        // console.log(item);
                        // Safe assignment
                        $('#transfer_item_variant_id').val(item.id);
                        $('#source_product_name').val(item.name || '');
                        $('#source_product_quantity').val(quantity || 0);
                        $('#source_warehouse_name').val(item.source_warehouse_name || '');
                        $('#source_warehouse_id').val(item.source_warehouse_id || '');

                        // Optional: Clear other fields
                        $('#destination_warehouse_id').val('');
                        $('[name="quantity"]').val('');
                        $('[name="reference"]').val('');
                        $('[name="reason"]').val('');
                    } else {
                        alert('Could not load item data.');
                    }
                },
                error: function() {
                    alert('Failed to fetch item data.');
                }
            });
        }
        $('#transferStockForm').on('submit', function(e) {
            e.preventDefault();

            const formData = $(this).serialize();

            $.ajax({
                url: '{{ route('stockitemsinventory.transfer-stock') }}',
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.status) {
                        toastr.success(response.message);
                        $('#transferStockModal').modal('hide');
                        $('#stock-items-table').DataTable().ajax.reload(); // Reload table
                    } else {
                        toastr.error(response.message || 'Transfer failed.');
                    }
                },
                error: function(xhr) {
                    const error = xhr.responseJSON?.message || 'Transfer failed.';
                    toastr.error(error);
                }
            });
        });
    </script>

    <script>
        function openAddStockModal(itemVariantId) {
            document.getElementById('item-variant-id').value = itemVariantId;
        }

        document.getElementById('addStockForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);

            fetch("{{ route('stockitemsinventory.add-stock') }}", {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        toastr.success(data.message);
                        form.reset();
                        var modal = bootstrap.Modal.getInstance(document.getElementById('addStockModal'));
                        modal.hide();

                        // Optional: Reload datatable if used
                        if (typeof $('#stock-items-table').DataTable === 'function') {
                            $('#stock-items-table').DataTable().ajax.reload();
                        }
                    } else {
                        alert(data.message || 'Something went wrong!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Request failed');
                });
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
                <h4 class="mb-1">Items Inventory</h4>
            </div>

            <div class="d-flex justify-content-end mb-3">
                <!--Add stock Modal -->
                <div class="modal fade" id="addStockModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <form id="addStockForm">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Add Stock</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="item_variant_id" id="item-variant-id">
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <label class="form-label">Quantity</label>
                                            <input type="number" step="1" class="form-control" name="quantity"
                                                required>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label class="form-label">Cost</label>
                                            <input type="number" step="0.01" class="form-control" name="cost">
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label class="form-label">Date</label>
                                            <input type="date" name="movement_date" class="form-control"
                                                value="{{ date('Y-m-d') }}">
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label class="form-label">Notes</label>
                                            <textarea name="reason" class="form-control" rows="2"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-label-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Add</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!--Transfer Stock Modal -->
                <!-- Transfer Stock Modal -->
                <div class="modal fade" id="transferStockModal" tabindex="-1" aria-labelledby="transferStockModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <form id="transferStockForm">
                            @csrf
                            <input type="hidden" name="item_variant_id" id="transfer_item_variant_id">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Transfer Stock</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>

                                <div class="modal-body">
                                    <!-- Source Info Display (read-only) -->
                                    <div class="mb-3">
                                        <label class="form-label">Item</label>
                                        <input type="text" class="form-control" id="source_product_name" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Stock Quantity</label>
                                        <input type="text" class="form-control" id="source_product_quantity" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Source Warehouse</label>
                                        <input type="text" class="form-control" id="source_warehouse_name" readonly>
                                        <input type="hidden" name="source_warehouse_id" id="source_warehouse_id">
                                    </div>

                                    <!-- Destination Warehouse Select -->
                                    <div class="mb-3">
                                        <label class="form-label">Destination Warehouse</label>
                                        <select class="form-select" name="destination_warehouse_id"
                                            id="destination_warehouse_id" required>
                                            <option value="">Select destination</option>
                                            @foreach ($warehouses as $warehouse)
                                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Quantity Input -->
                                    <div class="mb-3">
                                        <label class="form-label">Quantity</label>
                                        <input type="number" class="form-control" name="quantity" min="1"
                                            required>
                                    </div>

                                    <!-- Movement Date -->
                                    <div class="mb-3">
                                        <label class="form-label">Transfer Date</label>
                                        <input type="date" class="form-control" name="movement_date"
                                            value="{{ date('Y-m-d') }}" required>
                                    </div>

                                    <!-- Reference -->
                                    <div class="mb-3">
                                        <label class="form-label">Reference (Optional)</label>
                                        <input type="text" class="form-control" name="reference">
                                    </div>

                                    <!-- Reason -->
                                    <div class="mb-3">
                                        <label class="form-label">Reason</label>
                                        <input type="text" class="form-control" name="reason">
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Transfer</button>
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-datatable table-responsive p-2">
                <table class="datatables-ajax table table-bordered" id="stock-items-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>NAME</th>
                            <th>WAREHOUSE</th>
                            <th>STOCK</th>
                            <th>COST</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                </table>
            </div>

        </div>
    </div>
    <!-- / Content -->

@endsection
