@extends('layouts.master')

@section('title', 'Vehicle Overview')

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
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Vehicle Overview - ({{ $vehicle->registration_no }})</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('vehicle-overviews.create', $vehicle->id) }}"
                                class="btn btn-primary btn-sm">Add New Overview</a>
                            <a href="{{ route('vehicles.index') }}" class="btn btn-secondary btn-sm">Back to Vehicles</a>
                            <a href="{{ route('vehicle-overviews.export', $vehicle->id) }}"
                                class="btn btn-success btn-sm">Export to Excel</a>
                        </div>
                    </div>
                    <div class="table-responsive text-nowrap">
                        @php
                            $overviewDates = $vehicleoverviews->pluck('overview_date')->unique();

                            $overviewMap = [];

                            foreach ($vehicleoverviews as $overview) {
                                foreach ($overview->details as $detail) {
                                    $key =
                                        $detail->vehicle_check_categories_id .
                                        '-' .
                                        $detail->vehicle_check_categories_child_id .
                                        '-' .
                                        $detail->vehicle_check_checklists_id .
                                        '-' .
                                        ($detail->vehicle_check_checklist_attributes_id ?? 0);

                                    $overviewMap[$key][$overview->overview_date] = $detail;
                                }
                                // dd($overviewMap);
                            }
                        @endphp

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>Checklist</th>
                                    <th>Attributes</th>
                                    @foreach ($vehicleoverviews as $date)
                                        <th>
                                            {{ \Carbon\Carbon::parse($date->overview_date)->format('M-y') }}
                                            <a href="{{ route('vehicle-overviews.show', $date->id) }}">
                                                <i class="menu-icon icon-base ti tabler-edit"></i>
                                            </a>
                                            <br />
                                            Milage: {{ $date->mileage }} km
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($vehiclechecklists as $category)
                                    @php
                                        $categoryRowspan =
                                            $category->child->sum(
                                                fn($child) => $child->checklists->sum(
                                                    fn($checklist) => max($checklist->attributes->count(), 1),
                                                ),
                                            ) ?:
                                            1;
                                    @endphp

                                    @if ($category->child->count())
                                        @php $firstCategoryRow = true; @endphp
                                        @foreach ($category->child as $child)
                                            @php
                                                $childRowspan =
                                                    $child->checklists->sum(
                                                        fn($checklist) => max($checklist->attributes->count(), 1),
                                                    ) ?:
                                                    1;
                                            @endphp
                                            @php $firstChildRow = true; @endphp

                                            @foreach ($child->checklists as $checklist)
                                                @php $checklistRowspan = max($checklist->attributes->count(), 1); @endphp
                                                @php $firstChecklistRow = true; @endphp

                                                @foreach ($checklist->attributes->count() ? $checklist->attributes : [null] as $attribute)
                                                    @php
                                                        $key =
                                                            $category->id .
                                                            '-' .
                                                            $child->id .
                                                            '-' .
                                                            $checklist->id .
                                                            '-' .
                                                            ($attribute->id ?? 0);
                                                    @endphp
                                                    <tr>
                                                        @if ($firstCategoryRow)
                                                            <td rowspan="{{ $categoryRowspan }}">{{ $category->name }}</td>
                                                            @php $firstCategoryRow = false; @endphp
                                                        @endif

                                                        @if ($firstChildRow)
                                                            <td rowspan="{{ $childRowspan }}">{{ $child->name }}</td>
                                                            @php $firstChildRow = false; @endphp
                                                        @endif

                                                        @if ($firstChecklistRow)
                                                            <td rowspan="{{ $checklistRowspan }}">{{ $checklist->title }}
                                                            </td>
                                                            @php $firstChecklistRow = false; @endphp
                                                        @endif

                                                        <td>{{ $attribute?->attribute_name ?? '' }}</td>

                                                        {{-- Fill overview values per date --}}
                                                        @foreach ($overviewDates as $date)
                                                            @php $detail = $overviewMap[$key][$date] ?? null; @endphp
                                                            @php
                                                                $bgClass = '';
                                                                $display = $detail->overview_value ?? '-';

                                                                if ($detail?->overview_value == 1) {
                                                                    $bgClass = 'table-success'; // green background
                                                                    $display =
                                                                        '<i class="menu-icon icon-base ti tabler-check"></i> OK'; // ✅
                                                                    if (!empty($detail?->notes)) {
                                                                        $display = e($detail->notes); // override with note if exists
                                                                    }
                                                                } elseif ($detail?->overview_value == 2) {
                                                                    $bgClass = 'table-danger'; // yellow background
                                                                    $display = e($detail->notes ?: 'Note'); // ⚠️ note or "Note"
                                                                }
                                                            @endphp

                                                            <td class="{{ $bgClass }}">{!! $display !!}</td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        @endforeach
                                    @else
                                        {{-- Category with no children --}}
                                        <tr>
                                            <td>{{ $category->name }}</td>
                                            <td colspan="2">-</td>
                                            <td>-</td>
                                            @foreach ($overviewDates as $date)
                                                <td>-</td>
                                            @endforeach
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>


                    </div>


                </div>
            </div>
        </div>
        <!-- /Sticky Actions -->
    </div>
    <!-- / Content -->

@endsection
