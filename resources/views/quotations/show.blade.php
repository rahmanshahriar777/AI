@extends('layouts.master')

@section('title', 'Quotations')

@section('scripts')
    @parent

    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/pickr/pickr.js') }}"></script>

    <script>
        $(document).on('click', '.update-quotation-status', function(e) {
            e.preventDefault();

            let status = $(this).data('status');
            let quotationId = $(this).data('id');

            if (status === 'accepted') {
                // Store quotationId and show modal
                $('#modalQuotationId').val(quotationId);
                $('#acceptModal').modal('show');
                return;
            }

            if (status === 'rejected') {
                if (!confirm(`Are you sure you want to reject this quotation?`)) return;
                updateStatus(quotationId, status);
            }
        });

        // ✅ Modal form submission (for accepted status)
        $('#acceptStatusForm').on('submit', function(e) {
            e.preventDefault();

            let quotationId = $('#modalQuotationId').val();
            let status = 'accepted';

            updateStatus(quotationId, status);

            //$('#acceptModal').modal('hide');
        });

        // ✅ Common AJAX call
        function updateStatus(quotationId, status) {
            $.ajax({
                url: `/quotation/updatestatus/${quotationId}`,
                type: 'POST',
                data: {
                    status: status,
                    quotation_amount: $('#totalAmount').val(),
                    valid_until: $('#validUntil').val(),
                    remarks: $('#remarks').val(),
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    toastr.success('Status updated successfully');
                    location.reload();
                },
                error: function(xhr) {
                    //toastr.error('Something went wrong');
                    // Handle errors
                    let errors = xhr.responseJSON.errors;
                    let message = 'Something went wrong';

                    if (errors) {
                        message = Object.values(errors).flat().join('\n');
                    }
                    toastr.error(message);
                }
            });
        }
    </script>


@endsection

@section('styles')
    @parent
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/pickr/pickr-themes.css') }}" />

    <!-- Row Group CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css') }}" />

@endsection

@section('content')

    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-2">
            <div class="d-flex flex-column justify-content-center">
                <div class="mb-1">
                    <a href="{{ route('lead.show.quotations', ['id' => $quotation->lead->slug, 'viewwith' => 'quotations']) }}"
                        class="btn btn-icon btn-label-secondary waves-effect btn-sm me-2">
                        <span class="icon-base ti tabler-corner-up-left-double icon-20px"></span>
                    </a>

                    <span class="h5">QUOTATION FOR LEAD #
                        <a href="{{ route('leads.show', $quotation->lead->slug) }}">
                            <span class="badge bg-label-success me-1 ms-2">{{ $quotation->lead->lead_name }}
                            </span>
                        </a>

                        @if ($quotation->lead->status == 'draft' || $quotation->lead->status == 'inprogress')
                            <a type="button" class="btn btn-icon btn-sm btn-warning waves-effect waves-light"
                                href="{{ route('quotations.edit', $quotation->id) }}">
                                <span class="icon-base ti tabler-edit icon-18px"></span>
                            </a>
                        @endif
                    </span>

                    <span class="badge bg-label-success me-1 ms-2">{{ $quotation->quotation_version }}</span>

                    <div class="btn-group" style="padding-left: 6em;">
                        @if ($quotation->status == 'draft')
                            <button type="button"
                                class="btn btn-label-info waves-effect">{{ beautify_status($quotation->status) }}</button>
                        @elseif($quotation->status == 'inprogress')
                            <button type="button"
                                class="btn btn-label-warning waves-effect">{{ beautify_status($quotation->status) }}</button>
                        @elseif($quotation->status == 'accepted')
                            <button type="button"
                                class="btn btn-label-success waves-effect">{{ beautify_status($quotation->status) }}</button>
                        @elseif($quotation->status == 'rejected' || $quotation->status == 'archived')
                            <button type="button"
                                class="btn btn-label-danger waves-effect">{{ beautify_status($quotation->status) }}</button>
                        @endif
                        <button type="button"
                            class="btn btn-label-primary dropdown-toggle dropdown-toggle-split waves-effect"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Toggle Dropdown</span>
                        </button>
                        @if (!in_array($quotation->status, ['accepted', 'rejected']))
                            <ul class="dropdown-menu" style="">
                                <li><a class="dropdown-item waves-effect update-quotation-status" data-status="accepted"
                                        data-id="{{ $quotation->id }}" href="#">Accepted</a></li>
                                <li><a class="dropdown-item waves-effect update-quotation-status" data-status="rejected"
                                        data-id="{{ $quotation->id }}" href="#">Rejected</a></li>
                            </ul>
                        @endif
                    </div>

                    <span class="justify-content-center ms-2">
                        <a href="{{ route('quotations.download-pdf', $quotation->id) }}"
                            class="btn btn-dark waves-effect waves-light btn-md">
                            <span class="icon-base ti tabler-download icon-18px"></span>
                        </a>
                    </span>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="acceptModal" data-bs-backdrop="static" tabindex="-1">
                <div class="modal-dialog">
                    <form class="modal-content" id="acceptStatusForm">
                        <div class="modal-header">
                            <h5 class="modal-title" id="acceptModalTitle">Quotaion</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-4">
                                <div class="col mb-4">
                                    <label for="remarks" class="form-label">Remarks</label>
                                    <input type="text" id="remarks" class="form-control" name="remarks" required />
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="quotation_id" id="modalQuotationId">
                            <input type="hidden" name="lead_status" value="accepted">
                            <button type="submit" class="btn btn-success">Confirm</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-12 col-lg-8">
                <div class="card mb-6">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-6">
                                <p class="mb-6">
                                    {{ \Carbon\Carbon::parse($quotation->quotation_date)->format('F jS Y') }}
                                    <br><br>
                                    {{ $quotation->customer->contact_firstname }}
                                    {{ $quotation->customer->contact_lastname }}
                                    <br>
                                    {{ $quotation->customer->company_name }}
                                    <br>
                                    @isset($quotation->customer->billingaddress)
                                        <span style="text-transform: capitalize;">
                                            {{ $quotation->customer->billingaddress->address }} ,
                                            {{ $quotation->customer->billingaddress->county }}
                                        </span>
                                    @endisset
                                </p>
                            </div>
                            <div class="col-6 text-end">
                                <p class="mb-6">
                                    <img src="{{ $appbrand->business_logo??'' }}"
                                        alt="{{ $appbrand->business_name??'Logo' }}" class="mb-4" style="max-width: 100px; max-height: 100px;" >
                                    <br>
                                    Tel: {{ $appbrand->business_phone??'' }}
                                    <br>
                                    Email: {{ $appbrand->business_email??'' }}
                                    <br>
                                    Website: {{ $appbrand->business_website??'' }}
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <p class="text-start mb-2"><strong> Estimated Total Cost:
                                        {{ formatPoundNumber($quotation->total_amount) }}</strong></p>
                            </div>
                            <div class="col-6">
                                <p class="text-md-end mb-2"><strong> Proposal valid until:
                                        {{ \Carbon\Carbon::parse($quotation->valid_until)->format('F jS Y') }}</strong></p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-1">
                        @foreach ($quotation->sections as $sections)
                            <h5>{{ $sections->section_name }}: </h5>

                            @if ($sections->section_type == 'textbox')
                                <p class="mb-6">
                                    @isset($sections->content->sample_source)
                                        {!! $sections->content->sample_source !!}
                                    @endisset
                                </p>
                            @endif
                            @if ($sections->has_attachments)
                                <div class="row">
                                    @foreach ($sections->attachments as $saa)
                                        <div class="col-6 mb-4">
                                            <img src="{{ $saa->source == 's3' ? $saa->attachment_url : asset($saa->attachment_path) }}"
                                                alt="{{ $saa->attachment_name }}" width="100%" class="img-fluid">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

        </div>

    </div>
    <!-- / Content -->

@endsection
