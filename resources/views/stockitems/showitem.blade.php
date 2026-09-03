@extends('layouts.master')

@section('title', 'Items')

@section('scripts')
    @parent
    <script src="{{ asset('assets/vendor/libs/quill/katex.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/highlight/highlight.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/quill/quill.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/tagify/tagify.js') }}"></script>
    <!-- Vendors JS -->
    <script src="{{ asset('assets/vendor/libs/swiper/swiper.js') }}"></script>
    <!-- Page JS -->
    <script src="{{ asset('assets/js/ui-carousel.js') }}"></script>
    <script src="{{ asset('assets/js/item-show.js') }}"></script>
@endsection

@section('styles')
    @parent
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/typography.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/katex.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/editor.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/tagify/tagify.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/dropzone/dropzone.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/swiper/swiper.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/ui-carousel.css') }}" />
@endsection

@section('content')
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="app-ecommerce">
            <!-- Add Product -->
            <div
                class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
                <div class="d-flex flex-column justify-content-center">
                    <h4 class="mb-1">{{ $stockitem->name }} Details</h4>
                </div>
                <div class="d-flex align-content-center flex-wrap gap-4">
                    <div class="d-flex gap-4">
                        <button class="btn btn-label-danger">Delete</button>
                        <button class="btn btn-label-primary">Update</button>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- First column-->
                <div class="col-12 col-lg-8">
                    <!-- Product Information -->
                    <div class="card mb-6">
                        <div class="card-header">
                            <h5 class="card-tile mb-0">Item information</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-6">
                                <label class="form-label" for="item-name">Name</label>
                                <input type="text" class="form-control" id="item-name" placeholder="Item name"
                                    name="item_name" aria-label="Item name" value="{{ $stockitem->name }}" />
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="mb-1">Description (Optional)</label>
                                <div class="form-control p-0">
                                    <div class="comment-editor border-0 pb-6" id="item-desc">
                                        {!! $stockitem->description !!}
                                    </div>
                                    <input type="hidden" name="item_desc" id="item-desc-hidden">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Product Information -->
                </div>
                <!-- /First column -->

                <!-- Second column -->
                <div class="col-12 col-lg-4">

                    <!-- Organize Card -->
                    <div class="card mb-6">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Organize</h5>
                        </div>
                        <div class="card-body">
                            <!-- Category -->
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="mb-6 col ecommerce-select2-dropdown">
                                    <label class="form-label mb-1" for="category-org">
                                        <span>Category</span>
                                    </label>
                                    <select id="category-org" class="select2 form-select"
                                        data-placeholder="Select Category">
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ $stockitem->category_id == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="d-flex justify-content-between align-items-center border-top pt-2">
                                <span class="mb-0">Active</span>
                                <div class="w-25 d-flex justify-content-end">
                                    <div class="form-check form-switch me-n3">
                                        <input type="checkbox" class="form-check-input"
                                            @if ($stockitem->is_active == true) checked @endif />
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- /Organize Card -->

                    <!-- Attribute Card -->
                    <div class="card mb-6">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Attributes</h5>
                        </div>
                        <div class="card-body">

                            <!-- Attribute -->
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="mb-6 col ecommerce-select2-dropdown">
                                    <label class="form-label mb-1" for="stock-attribute">
                                    </label>
                                    <select id="stock-attribute" class="select2 form-select"
                                        data-placeholder="Select Attribute">
                                        <option value="">Select Attribute</option>
                                        @foreach ($attributes as $attribute)
                                            <option value="{{ $attribute->id }}">{{ $attribute->name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                                <button onclick="addattribute({{ $stockitem->id }})"
                                    class="fw-medium btn btn-icon btn-label-primary ms-4"><i
                                        class="icon-base ti tabler-plus icon-md"></i></button>
                            </div>

                            <div class="demo-inline-spacing">
                                @foreach ($stockitem->attributes as $attribute)
                                    <span class="badge text-bg-secondary">
                                        {{ $attribute->attribute->name }}: {{ $attribute->value }}
                                        <a href="javascript:void(0);" class="text-danger ms-2"><i
                                                class="icon-base ti tabler-x icon-xs"></i></a>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!-- /Attribute Card -->

                </div>
                <!-- /Second column -->
            </div>
            <div class="row">
                <div class="col-12">
                    <!-- Variant Rows -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Variants</h5>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addProductVariantModal">
                                <i class="icon-base ti tabler-plus icon-xs me-1_5"></i> Add Variant
                            </button>

                            <!-- Modal -->
                            <div class="modal fade" id="addProductVariantModal" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addProductVariantModalTitle">Add Product Variant
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>

                                        <form id="variantForm" data-stock-item-id="{{ $stockitem->id }}">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col mb-4">
                                                        <label for="select2Basic" class="form-label">Select
                                                            Warehouse</label>
                                                        <select id="select2Basic" class="select2 form-select"
                                                            name="warehouse_id" required>
                                                            <option value="">Select Warehouse</option>
                                                            @foreach ($warehouses as $warehouse)
                                                                <option value="{{ $warehouse->id }}">
                                                                    {{ $warehouse->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row g-4">
                                                    @foreach ($stockitem->attributes as $attribute)
                                                        <div class="col mb-0">
                                                            <label for="variant-{{ $attribute->id }}"
                                                                class="form-label">{{ $attribute->attribute->name }}</label>
                                                            <input type="text" id="variant-{{ $attribute->id }}"
                                                                class="form-control"
                                                                placeholder="{{ $attribute->attribute->name }}"
                                                                name="variant_attributes[{{ $attribute->attribute->id }}]"
                                                                required />
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div class="row g-4">
                                                    <div class="col mb-0">
                                                        <label for="variant-unit" class="form-label">Unit</label>
                                                        <select id="variant-unit" class="select2 form-select"
                                                            name="unit_id" required>
                                                            <option value="">Select Unit</option>
                                                            @foreach ($stockunits as $unit)
                                                                <option value="{{ $unit->slug }}">{{ $unit->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Save</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            @foreach ($stockitem->attributes as $attribute)
                                                <th>{{ $attribute->attribute->name }}</th>
                                            @endforeach
                                            <th>Warehouse</th>
                                            <th>Quantity</th>
                                            <td>Unit</td>
                                            <th>Avg. Price</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach ($stockitem->variants as $variant)
                                            <tr>
                                                <td>{{ $variant->name }}</td>
                                                @foreach ($variant->attributes as $attribute)
                                                    <td>{{ $attribute->value }}</td>
                                                @endforeach
                                                <td>{{ $variant->warehouse->name }}</td>
                                                <td>{{ $variant->quantity }}</td>
                                                <td>{{ $variant->unit }}</td>
                                                <td>{{ $variant->avg_price }}</td>
                                                <td>
                                                    <span class="badge bg-label-{{ $variant->is_active ? 'success' : 'danger' }}">
                                                        {{ $variant->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-icon btn-label-primary"><i
                                                            class="icon-base ti tabler-edit icon-xs"></i></button>
                                                    <button class="btn btn-icon btn-label-danger"><i
                                                            class="icon-base ti tabler-trash icon-xs"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!--/ Striped Rows -->
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->

@endsection
