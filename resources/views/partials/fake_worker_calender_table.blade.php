<div class="table-responsive text-nowrap">
    <table class="table table-hover">
        <thead class="table-light">
            <tr>
                <th class="sticky-col bg-white" rowspan="2">Worker Name</th>
                @foreach ($datesByMonthFake as $month => $dates)
                    <th colspan="{{ count($dates) }}" class="text-center">{{ $month }}</th>
                @endforeach
            </tr>
            <tr>
                @foreach ($datesByMonthFake as $month => $dates)
                    @foreach ($dates as $date)
                        <th>{{ \Carbon\Carbon::parse($date)->format('d') }}</th>
                    @endforeach
                @endforeach
            </tr>
        </thead>
        <tbody>

            @foreach ($fakeworkers as $fworker)
                <tr class="position-relative" style="height: 50px;"> {{-- Control row height --}}
                    <td class="sticky-col bg-white"><strong>{{ $fworker->name }}</strong></td>

                    @php
                        $allDatesArray = $allDatesFake->toArray();
                        $skipDates = []; // Track which dates to skip due to colspan
                    @endphp

                    @foreach ($allDatesArray as $date)
                        @php
                            $dateKey = \Carbon\Carbon::parse($date)->format('Y-m-d');

                            // Get all jobs assigned to this worker that intersect this date
                            $jobSchedules = $fworker->fakeJobSchedules->filter(function ($job) use ($dateKey) {
                                return $dateKey >= $job->start_date && $dateKey <= $job->end_date;
                            });

                        @endphp
                        @php
                            // dd($jobSchedules);
                        @endphp
                        <td class="calendar-cell">
                            @if ($jobSchedules->isNotEmpty())
                                @foreach ($jobSchedules as $jobSchedule)
                                    @php
                                        $startdate = $jobSchedule->start_date;
                                        $enddate = $jobSchedule->end_date;
                                    @endphp

                                    <div class="mb-1">
                                        <button type="button" class="btn btn-sm btn-primary w-100 text-start"
                                            data-bs-toggle="modal" data-bs-target="#fakeScheduleUpdateModal"
                                            data-id="{{ $jobSchedule->id }}">
                                            {{ $jobSchedule->job_name }} - {{ $jobSchedule->job_title }}
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                -
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<!-- Modal -->
<div class="modal fade fake-schedule-update-modal" id="fakeScheduleUpdateModal" data-bs-backdrop="static"
    tabindex="-1">
    <div class="modal-dialog modal-xl">
        <form class="modal-content" id="fakeScheduleUpdateForm">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title" id="backDropModalTitle">Fake Schedule Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col mb-4">
                        <div class="row">
                            <div class="col mb-4">
                                <label class="form-label" for="multicol-job">Job</label>
                                <input type="text" id="jobname" class="form-control" disabled />
                            </div>
                        </div>
                        <div class="row g-4">
                            <div class="col mb-0">
                                <label for="fakeSelect2Info" class="form-label">staffs</label>
                                <div class="select2-info">
                                    <select id="fakeSelect2Info" class="select2 form-select" name="workers[]" multiple>

                                    </select>
                                </div>
                            </div>
                            <div class="col mb-0">
                                <label for="flatpickr-range" class="form-label">Date</label>
                                <input type="text" class="form-control" placeholder="YYYY-MM-DD to YYYY-MM-DD"
                                    id="flatpickr-range2" name="daterange" />
                            </div>
                        </div>
                        <div class="row g-4">
                            <div class="col mb-0">
                                <label for="instructionuf" class="form-label">Instructions</label>
                                <div class="form-control p-0">
                                    <div class="border-0 pb-6" id="instructionuf"></div>
                                </div>

                                <input type="hidden" name="instructionsf" id="instructionuf_input">
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="row">
                            <div class="modal-job-details" style="height: 80%; overflow-y: auto;">

                                <iframe id="jobMap" width="100%" height="250" frameborder="0" style="border:0"
                                    referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>

                                <h5>Contact Info</h5>
                                <div class="row contact-info">
                                    <div class="col-6 mb-3">
                                        <strong>Company:</strong> <span id="companyName"></span>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <strong>Contact:</strong> <span id="contactName"></span>
                                    </div>
                                    <div class="col-4 mb-3">
                                        <strong>Email:</strong> <span id="contactEmail"></span>
                                    </div>
                                    <div class="col-4 mb-3">
                                        <strong>Phone:</strong> <span id="contactPhone"></span>
                                    </div>
                                    <div class="col-4 mb-3">
                                        <strong>Mobile:</strong> <span id="contactMobile"></span>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <strong>Address:</strong> <span id="jobAddress"></span>
                                    </div>
                                </div>
                                <p><strong>Description:</strong> <span id="jobDescription"></span></p>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="fakeJobScheduleId">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                    Close
                </button>
                <button type="button" class="btn btn-primary" onclick="updateFakeSchedule(event)">Update</button>
                <button class="btn btn-md btn-danger"
                    onclick="removeFakeAssignment(this.closest('form').querySelector('#fakeJobScheduleId').value); return false;">
                    <i class="bi bi-trash"></i>
                    Delete Assignment
                </button>
            </div>
        </form>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('fakeScheduleUpdateModal');
        if (!modal) return;

        const flatpickrInput = modal.querySelector('#flatpickr-range2');
        if (flatpickrInput) {
            flatpickr(flatpickrInput, {
                mode: 'range',
                static: true,
                allowInput: false,
                dateFormat: 'Y-m-d',
            });
        }

        const quillEditorFake = new Quill('#instructionuf', {
            theme: 'snow',
            placeholder: 'Write your message...',
        });

        const select2Element = $(modal).find('#fakeSelect2Info');
        if (select2Element.length) {
            select2Element.select2({
                dropdownParent: $(modal)
            });
        }

        modal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const fakeJobScheduleId = button?.getAttribute('data-id');

            setTimeout(() => {
                const jobNameInput = modal.querySelector('#jobname');
                const instructionInput = modal.querySelector('#instructionuf_input');
                const fakeJobScheduleIdInput = modal.querySelector('#fakeJobScheduleId');
                const workerSelect = modal.querySelector('#fakeSelect2Info');

                if (!jobNameInput || !flatpickrInput || !instructionInput || !workerSelect) {
                    console.error('Some modal fields are missing in DOM.');
                    return;
                }

                fetch(`/scheduler/fake-job-schedule/${fakeJobScheduleId}`)
                    .then(response => response.json())
                    .then(data => {
                        const schedule = data.schedule;
                        const workers = data.workers;
                        const workerslist = data.workerslist;
                        const job = data.job;
                        const jobAddress = data.jobAddress;
                        const jobCustomer = data.customer;
                        const GOOGLE_MAPS_API_KEY =
                            "{{ config('services.google.maps_api_key') }}";

                        jobNameInput.value = schedule.job_name || '';
                        flatpickrInput.value =
                            `${schedule.start_date} to ${schedule.end_date}`;

                        // Reset and set Quill content
                        quillEditorFake.root.innerHTML = '';
                        quillEditorFake.clipboard.dangerouslyPasteHTML(schedule
                            .instructions ||
                            '');
                        instructionInput.value = schedule.instructions || '';

                        // Populate Select2 with workers
                        const assignedWorkerIds = workers.map(w => w.id.toString());
                        workerSelect.innerHTML = '';
                        workerslist.forEach(worker => {
                            const option = document.createElement('option');
                            option.value = worker.id;
                            option.textContent = worker.name;
                            if (assignedWorkerIds.includes(worker.id.toString())) {
                                option.selected = true;
                            }
                            workerSelect.appendChild(option);
                        });
                        $(workerSelect).trigger('change');

                        // Set job schedule ID
                        fakeJobScheduleIdInput.value = schedule.id;

                        // Fill job info
                        modal.querySelector('#jobDescription').innerHTML = job
                            .job_description || '';
                        modal.querySelector('#companyName').textContent = jobCustomer
                            .company_name || '';
                        modal.querySelector('#contactName').textContent =
                            `${jobAddress.contact_firstname} ${jobAddress.contact_lastname}` ||
                            '';
                        modal.querySelector('#contactEmail').textContent = jobAddress
                            .contact_email || '';
                        modal.querySelector('#contactPhone').textContent = jobAddress
                            .contact_phone || '';
                        modal.querySelector('#contactMobile').textContent = jobAddress
                            .contact_mobile || '';
                        modal.querySelector('#jobAddress').textContent =
                            `${jobAddress.address}, ${jobAddress.county}, ${jobAddress.country}` ||
                            '';

                        const addressParam = encodeURIComponent(
                            `${jobAddress.address}, ${jobAddress.county}, ${jobAddress.country}`
                        );
                        const mapEl = modal.querySelector('#jobMap');
                        if (mapEl) {
                            if (GOOGLE_MAPS_API_KEY && GOOGLE_MAPS_API_KEY.trim() !== '') {
                                mapEl.style.display = 'block';
                                mapEl.src = `https://www.google.com/maps/embed/v1/place?key=${GOOGLE_MAPS_API_KEY}&q=${addressParam}`;
                            } else {
                                mapEl.style.display = 'none';
                            }
                        }
                    });
            }, 100);
        });

        modal.addEventListener('hidden.bs.modal', function() {
            modal.querySelector('#jobname').value = '';
            modal.querySelector('#flatpickr-range2').value = '';
            modal.querySelector('#instructionuf_input').value = '';
            quillEditorFake.root.innerHTML = '';
            modal.querySelector('#fakeSelect2Info').innerHTML = '';
            modal.querySelector('#fakeJobScheduleId').value = '';

            const select2 = $(modal).find('#fakeSelect2Info');
            if (select2.hasClass("select2-hidden-accessible")) {
                select2.val(null).trigger('change');
            }

            setTimeout(() => {
                document.activeElement.blur();
                const fallbackFocus = document.querySelector('[data-bs-toggle="modal"]') ||
                    document.body;
                fallbackFocus.focus();
            }, 0);
        });
    });

    function updateFakeSchedule(e) {
        e.preventDefault();

        const quillEditorFakeU = new Quill('#instructionuf', {
            theme: 'snow',
            placeholder: 'Write your message...',
        });

        $('#instructionuf_input').val(quillEditorFakeU.root.innerHTML);

        const $btn = $(this);
        const $form = $('#fakeScheduleUpdateForm');

        const formData = new FormData($form[0]);
        const fakeJobScheduleId = $form.find('#fakeJobScheduleId').val();

        // console.log(fakeJobScheduleId);
        // return;
        $.ajax({
            url: `scheduler/update-fake-job-schedule/${fakeJobScheduleId}`,
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                );
            },
            success: function(response) {
                toastr.success(response.message || 'Schedule saved successfully!');
                $form[0].reset();
                quillEditorFakeU.setContents([]);
                $('#fakeScheduleUpdateModal').modal('hide');
                $btn.prop('disabled', false).html('Save');
                setTimeout(() => location.reload(), 1000);
            },
            error: function(xhr) {
                const errMsg = xhr.responseJSON?.message || 'Error saving schedule.';
                toastr.error(errMsg);
                $btn.prop('disabled', false).html('Save');
            }
        });
    }
</script>

<script>
    function removeFakeAssignment(assignmentId) {
        if (!confirm('Are you sure you want to remove this assignment?')) return;

        fetch(`/scheduler/delete-fake-schedule/${assignmentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(async response => {
                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Something went wrong');
                }

                toastr.success(data.message || 'Schedule deleted successfully');
                // Optionally: update UI dynamically instead of full reload
                setTimeout(() => location.reload(), 1000);
            })
            .catch(error => {
                console.error('Delete failed:', error);
                toastr.error(error.message || 'Failed to delete schedule.');
            });
    }
</script>
