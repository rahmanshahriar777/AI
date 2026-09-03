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

                    <form action="{{ route('vehicle-overviews.update', $vehicleoverviews->id) }}" method="POST" enctype="multipart/form-data"
                        class="row g-6">
                        @csrf
                        @method('PUT')
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
                            <h5 class="card-title mb-sm-0 me-2">Overview for vehicle {{ $vehicle->registration_no }}
                            </h5>
                            <div class="action-btns">
                                <a class="btn btn-label-primary me-4" href="{{ route('vehicle-overviews.index', $vehicle->id) }}">
                                    <span class="align-middle"> Back</span>
                                </a>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </div>
                        <div class="card-body pt-6">
                            <div class="row">
                                <!-- First column -->
                                <div class="col-12 col-lg-12">
                                    <!-- Basic Info Card -->
                                    <div class="card mb-6">
                                        <div class="card-body">
                                            <div class="row g-6">
                                                <div class="col-md-4">
                                                    <label class="form-label" for="overview-date">Overview
                                                        Date*</label>
                                                    <input type="date" id="overview-date" class="form-control"
                                                        placeholder="2025-09-01" name="overview_date"
                                                        value="{{ $vehicleoverviews->overview_date }}" required />
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label" for="mileage">Mileage*</label>
                                                    <input type="text" id="mileage" name="mileage" class="form-control"
                                                        placeholder="200" value="{{ $vehicleoverviews->mileage }}"
                                                        required />
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label" for="Notes">Notes</label>
                                                    <textarea id="Notes" class="form-control" rows="2" name="notes" placeholder="Enter notes here...">
                                                        {{ $vehicleoverviews->notes }}
                                                    </textarea>
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
                                                        @foreach ($parent->child as $child)
                                                            <h5 class="mt-3">{{ $child->name }}</h5>
                                                            <table class="table table-bordered">
                                                                <tbody>
                                                                    @foreach ($child->checklists as $checklist)
                                                                        @php
                                                                            // find checklist-level detail if exists
                                                                            $detail =
                                                                                $detailsMap[$checklist->id][
                                                                                    'no_attr'
                                                                                ] ?? null;
                                                                        @endphp
                                                                        <tr>
                                                                            <td>{{ $checklist->title }}</td>
                                                                            <td>
                                                                                <input type="hidden"
                                                                                    name="checklist[{{ $checklist->id }}][parent_category_id]"
                                                                                    value="{{ $parent->id }}">
                                                                                <input type="hidden"
                                                                                    name="checklist[{{ $checklist->id }}][category_id]"
                                                                                    value="{{ $child->id }}">

                                                                                @if ($checklist->has_attributes && $checklist->attributes->count())
                                                                                    @foreach ($checklist->attributes as $attr)
                                                                                        @php
                                                                                            $attrDetail =
                                                                                                $detailsMap[
                                                                                                    $checklist->id
                                                                                                ][$attr->id] ?? null;
                                                                                        @endphp
                                                                                        <div class="mb-2">
                                                                                            <span
                                                                                                class="badge bg-info text-dark me-2">{{ $attr->attribute_name }}</span>

                                                                                            <label class="me-2">
                                                                                                <input type="radio"
                                                                                                    name="checklist[{{ $checklist->id }}][attributes][{{ $attr->id }}][status]"
                                                                                                    value="1"
                                                                                                    {{ $attrDetail && $attrDetail->overview_value == '1' ? 'checked' : '' }}>
                                                                                                ✓ OK
                                                                                            </label>
                                                                                            <label class="me-2">
                                                                                                <input type="radio"
                                                                                                    name="checklist[{{ $checklist->id }}][attributes][{{ $attr->id }}][status]"
                                                                                                    value="2"
                                                                                                    {{ $attrDetail && $attrDetail->overview_value == '2' ? 'checked' : '' }}>
                                                                                                ✗ Not OK
                                                                                            </label>

                                                                                            <input type="text"
                                                                                                name="checklist[{{ $checklist->id }}][attributes][{{ $attr->id }}][note]"
                                                                                                class="form-control d-inline-block w-auto ms-2"
                                                                                                placeholder="Short note"
                                                                                                value="{{ $attrDetail->notes ?? '' }}">
                                                                                        </div>
                                                                                    @endforeach
                                                                                @else
                                                                                    <div class="mb-2">
                                                                                        <label class="me-2">
                                                                                            <input type="radio"
                                                                                                name="checklist[{{ $checklist->id }}][status]"
                                                                                                value="1"
                                                                                                {{ $detail && $detail->overview_value == '1' ? 'checked' : '' }}>
                                                                                            ✓ OK
                                                                                        </label>
                                                                                        <label class="me-2">
                                                                                            <input type="radio"
                                                                                                name="checklist[{{ $checklist->id }}][status]"
                                                                                                value="2"
                                                                                                {{ $detail && $detail->overview_value == '2' ? 'checked' : '' }}>
                                                                                            ✗ Not OK
                                                                                        </label>
                                                                                        <label class="me-2">
                                                                                            <input type="radio"
                                                                                                name="checklist[{{ $checklist->id }}][status]"
                                                                                                value="0"
                                                                                                {{ $detail && $detail->overview_value == '0' ? 'checked' : '' }}>
                                                                                            N/A
                                                                                        </label>

                                                                                        <input type="text"
                                                                                            name="checklist[{{ $checklist->id }}][note]"
                                                                                            class="form-control d-inline-block w-auto ms-2"
                                                                                            placeholder="Short note"
                                                                                            value="{{ $detail->notes ?? '' }}">
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
