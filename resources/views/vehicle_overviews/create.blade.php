@extends('layouts.master')

@section('title', 'Create Overview')

@section('scripts')
    @parent
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
@endsection

@section('styles')
    @parent

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />


@endsection

@section('content')
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Sticky Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <form action="{{ route('vehicle-overviews.store') }}" method="POST" enctype="multipart/form-data"
                        class="row g-6">
                        @csrf
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div
                            class="card-header sticky-element bg-label-secondary d-flex justify-content-sm-between align-items-sm-center flex-column flex-sm-row">
                            <h5 class="card-title mb-sm-0 me-2">Add Overview for vehicle {{ $vehicle->registration_no }}
                            </h5>
                            <div class="action-btns">
                                <button class="btn btn-label-primary me-4">
                                    <span class="align-middle"> Back</span>
                                </button>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                        <div class="card-body pt-6">
                            <div class="row">
                                <!-- First column -->
                                <div class="col-12 col-lg-12">
                                    <!-- Basic Info Card -->
                                    <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                                    <div class="card mb-6">
                                        <div class="card-body">
                                            <div class="row g-6">
                                                <div class="col-md-4">
                                                    <label class="form-label" for="overview-date">Overview
                                                        Date*</label>
                                                    <input type="date" id="overview-date" class="form-control"
                                                        placeholder="2025-09-01" name="overview_date" required />
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label" for="mileage">Mileage*</label>
                                                    <input type="text" id="mileage" name="mileage" class="form-control"
                                                        placeholder="200" required />
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label" for="Notes">Notes</label>
                                                    <textarea id="Notes" class="form-control" rows="2" name="notes" placeholder="Enter notes here..."></textarea>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <!-- /First column -->

                                    <!-- Second column-->
                                    <div class="col-12 col-lg-12">

                                        @foreach ($categories as $parent)
                                            @if ($parent->is_parent)
                                                <div class="card mb-4">
                                                    <div class="card-header bg-primary text-white">
                                                        {{ $parent->name }}
                                                    </div>
                                                    <div class="card-body">

                                                        {{-- Loop child categories --}}
                                                        @foreach ($parent->child as $child)
                                                            <h5 class="mt-3">{{ $child->name }}</h5>
                                                            <table class="table table-bordered">

                                                                <tbody>
                                                                    @foreach ($child->checklists as $checklist)
                                                                        <tr>
                                                                            <td>{{ $checklist->title }}</td>
                                                                            <td>
                                                                                {{-- Hidden input for category_id --}}
                                                                                <input type="hidden"
                                                                                    name="checklist[{{ $checklist->id }}][parent_category_id]"
                                                                                    value="{{ $parent->id }}">
                                                                                <input type="hidden"
                                                                                    name="checklist[{{ $checklist->id }}][category_id]"
                                                                                    value="{{ $child->id }}">

                                                                                @if ($checklist->has_attributes && $checklist->attributes->count())
                                                                                    {{-- Show each attribute with radio + note --}}
                                                                                    @foreach ($checklist->attributes as $attr)
                                                                                        <div class="mb-2">
                                                                                            <span
                                                                                                class="badge bg-info text-dark me-2">
                                                                                                {{ $attr->attribute_name }}
                                                                                            </span>

                                                                                            {{-- Radio buttons --}}
                                                                                            <label class="me-2">
                                                                                                <input type="radio"
                                                                                                    name="checklist[{{ $checklist->id }}][attributes][{{ $attr->id }}][status]"
                                                                                                    value="1">
                                                                                                ✓ OK
                                                                                            </label>
                                                                                            <label class="me-2">
                                                                                                <input type="radio"
                                                                                                    name="checklist[{{ $checklist->id }}][attributes][{{ $attr->id }}][status]"
                                                                                                    value="2">
                                                                                                ✗ Not OK
                                                                                            </label>

                                                                                            {{-- Short note input --}}
                                                                                            <input type="text"
                                                                                                name="checklist[{{ $checklist->id }}][attributes][{{ $attr->id }}][note]"
                                                                                                class="form-control d-inline-block w-auto ms-2"
                                                                                                placeholder="Short note">
                                                                                        </div>
                                                                                    @endforeach
                                                                                @else
                                                                                    {{-- No attributes: show checklist-level radio + note --}}
                                                                                    <div class="mb-2">
                                                                                        <label class="me-2">
                                                                                            <input type="radio"
                                                                                                name="checklist[{{ $checklist->id }}][status]"
                                                                                                value="1">
                                                                                            ✓ OK
                                                                                        </label>
                                                                                        <label class="me-2">
                                                                                            <input type="radio"
                                                                                                name="checklist[{{ $checklist->id }}][status]"
                                                                                                value="2">
                                                                                            ✗ Not OK
                                                                                        </label>
                                                                                        <label class="me-2">
                                                                                            <input type="radio"
                                                                                                name="checklist[{{ $checklist->id }}][status]"
                                                                                                value="0">
                                                                                            N/A
                                                                                        </label>

                                                                                        {{-- Short note input --}}
                                                                                        <input type="text"
                                                                                            name="checklist[{{ $checklist->id }}][note]"
                                                                                            class="form-control d-inline-block w-auto ms-2"
                                                                                            placeholder="Short note">
                                                                                    </div>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        @endforeach

                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach


                                    </div>
                                    <!-- /Second column -->


                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        <!-- /Sticky Actions -->
    </div>
    <!-- / Content -->

@endsection
