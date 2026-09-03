@extends('layouts.master')

@section('title', 'Job Attributes')

@section('scripts')
    @parent
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.toggle-parent').forEach(function(radio) {
                radio.addEventListener('change', function() {
                    const target = document.querySelector(this.dataset.target);

                    if (this.value === 'yes') {
                        target?.classList.add('show'); // Show collapse
                    } else {
                        target?.classList.remove('show'); // Hide collapse
                    }
                });

                // Trigger once on load to apply correct state
                if (radio.checked) {
                    const target = document.querySelector(radio.dataset.target);
                    if (radio.value === 'yes') {
                        target?.classList.add('show');
                    } else {
                        target?.classList.remove('show');
                    }
                }
            });
        });
    </script>
@endsection

@section('styles')
    @parent
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />


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
                    <a href="{{ route('job.show', $job->id) }}" class="btn btn-label-warning">Back</a>

                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('job-attributes.add-store', $job->id) }}">
            @csrf

            @foreach ($jobAttributes as $jatb)
                <div class="card mb-2">
                    <div class="card-body d-flex align-items-center gap-3">
                        <p class="card-text mb-0">{{ $jatb->name }}</p>

                        @php
                            $parentSelected = $selectedAttributes[$jatb->id]['value'] ?? 'na';
                        @endphp

                        <!-- Parent Yes -->
                        <div class="form-check form-check-inline mb-0">
                            <input class="form-check-input toggle-parent" type="radio"
                                name="attributes[{{ $jatb->id }}][selected]" id="parentYes{{ $jatb->id }}"
                                value="yes" data-target="#collapseParent{{ $jatb->id }}"
                                {{ $parentSelected === 'yes' ? 'checked' : '' }}>
                            <label class="form-check-label" for="parentYes{{ $jatb->id }}">Yes</label>
                        </div>

                        <!-- Parent N/A -->
                        <div class="form-check form-check-inline mb-0">
                            <input class="form-check-input toggle-parent" type="radio"
                                name="attributes[{{ $jatb->id }}][selected]" id="parentNa{{ $jatb->id }}"
                                value="na" data-target="#collapseParent{{ $jatb->id }}"
                                {{ $parentSelected === 'na' ? 'checked' : '' }}>
                            <label class="form-check-label" for="parentNa{{ $jatb->id }}">N/A</label>
                        </div>
                    </div>

                    @if ($jatb->attributedetails->count())
                        <div class="collapse mt-3 px-3" id="collapseParent{{ $jatb->id }}">
                            <div class="border p-3 rounded">
                                @foreach ($jatb->attributedetails as $index => $detail)
                                    <div class="mb-2 d-flex align-items-center gap-3">
                                        <p class="mb-0">{{ $detail->value }}</p>

                                        @php
                                            $childSelected =
                                                $selectedAttributes[$jatb->id]['details'][$detail->id] ?? 'na';
                                        @endphp

                                        <!-- Child Yes -->
                                        <div class="form-check form-check-inline mb-0">
                                            <input class="form-check-input" type="radio"
                                                name="attributes[{{ $jatb->id }}][children][{{ $detail->id }}]"
                                                id="childYes{{ $detail->id }}" value="yes"
                                                {{ $childSelected === 'yes' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="childYes{{ $detail->id }}">Yes</label>
                                        </div>

                                        <!-- Child N/A -->
                                        <div class="form-check form-check-inline mb-0">
                                            <input class="form-check-input" type="radio"
                                                name="attributes[{{ $jatb->id }}][children][{{ $detail->id }}]"
                                                id="childNa{{ $detail->id }}" value="na"
                                                {{ $childSelected === 'na' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="childNa{{ $detail->id }}">N/A</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach

            <div class="text-end mt-4">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>

    </div>
    <!-- / Content -->

@endsection
