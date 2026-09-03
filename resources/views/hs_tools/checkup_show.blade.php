@extends('layouts.master')

@section('title', 'Health & Safety Tools Check')

@section('scripts')
    @parent
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script>
        $(function() {
            // Initialize Select2
            $('#select2jobs').select2({
                placeholder: "Select a job",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endsection

@section('styles')
    @parent
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />


@endsection

@section('content')
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Sticky Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <form action="{{ route('hs-tools.updatecheck', $hstoolcheck->id) }}" method="POST" class="row g-6">
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
                            <h5 class="card-title mb-sm-0 me-2">
                                Health & Safety Tools Check
                            </h5>
                            <div class="action-btns">
                                <button class="btn btn-label-primary me-4">
                                    <span class="align-middle"> Back</span>
                                </button>
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
                                                <div class="col-md-6">
                                                    <label class="form-label" for="tools">Tools</label>
                                                    <input type="text" class="form-control" id="tools" name="tools"
                                                        placeholder="Tools" value="{{ old('tools', $hsTool->tool_name) }}"
                                                        disabled />
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label" for="title">Title</label>
                                                    <input type="text" class="form-control" id="title"
                                                        name="hs_check_title" placeholder="Title"
                                                        value="{{ old('hs_check_title', $hstoolcheck->checkup_name) }}" />
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label" for="date">Date</label>
                                                    <input type="date" class="form-control" id="date" name="date"
                                                        placeholder="Date"
                                                        value="{{ old('date', $hstoolcheck->checkup_date ? \Carbon\Carbon::parse($hstoolcheck->checkup_date)->format('Y-m-d') : '') }}" />
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label" for="checked_by">Next Checkup Date</label>
                                                    <input type="date" class="form-control" id="next_checkup_date"
                                                        name="next_checkup_date" placeholder="Next Checkup Date"
                                                        value="{{ old('next_checkup_date', $hstoolcheck->next_checkup_date ? \Carbon\Carbon::parse($hstoolcheck->next_checkup_date)->format('Y-m-d') : '') }}" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /First column -->

                                    <!-- Second column-->
                                    <div class="col-12 col-lg-12">
                                        <div class="card p-0 mb-6">
                                            <div class="card-header d-flex align-items-center justify-content-between">
                                                <small class="text-muted">*(Please complete the checklist below.)</small>
                                            </div>
                                            <div class="card-body pb-0">
                                                <div class="row">
                                                    @php
                                                        // Get checklist status and remarks for each checklist item
                                                        $selectedChecklist = [];
                                                        foreach ($hstoolcheck->checklist as $item) {
                                                            $selectedChecklist[$item->hs_checklist_id] = [
                                                                'value' => $item->value,
                                                                'remarks' => $item->remarks,
                                                            ];
                                                        }
                                                    @endphp

                                                    @foreach ($checklists as $checklist)
                                                        @php
                                                            $selected = $selectedChecklist[$checklist->id] ?? [
                                                                'status' => null,
                                                                'note' => '',
                                                            ];
                                                        @endphp
                                                        <div class="col-md-6">
                                                            <div class="p-3 border rounded bg-primary bg-opacity-10 mb-4">
                                                                <div class="mb-2 fw-bold">{{ $checklist->title }}</div>

                                                                <div class="d-flex flex-wrap gap-3 align-items-center">
                                                                    <label class="me-3">
                                                                        <input type="radio"
                                                                            name="checklist[{{ $checklist->id }}][status]"
                                                                            value="1"
                                                                            {{ $selected['value'] == 1 ? 'checked' : '' }}>
                                                                        <span class="ms-1 text-success">✓ OK</span>
                                                                    </label>

                                                                    <label class="me-3">
                                                                        <input type="radio"
                                                                            name="checklist[{{ $checklist->id }}][status]"
                                                                            value="2"
                                                                            {{ $selected['value'] == 2 ? 'checked' : '' }}>
                                                                        <span class="ms-1 text-danger">✗ Not OK</span>
                                                                    </label>

                                                                    <label class="me-3">
                                                                        <input type="radio"
                                                                            name="checklist[{{ $checklist->id }}][status]"
                                                                            value="0"
                                                                            {{ $selected['value'] === 0 ? 'checked' : '' }}>
                                                                        <span class="ms-1 text-muted">N/A</span>
                                                                    </label>

                                                                    {{-- Short note input --}}
                                                                    <input type="text"
                                                                        name="checklist[{{ $checklist->id }}][note]"
                                                                        class="form-control flex-grow-1"
                                                                        placeholder="Short note"
                                                                        value="{{ $selected['remarks'] }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
