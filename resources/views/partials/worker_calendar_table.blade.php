<div class="table-responsive text-nowrap">
    <table class="table table-hover">
        <thead class="table-light">
            <tr>
                <th class="sticky-col bg-white" rowspan="2">Worker Name</th>
                @foreach ($datesByMonth as $month => $dates)
                    <th colspan="{{ count($dates) }}" class="text-center">{{ $month }}</th>
                @endforeach
            </tr>
            <tr>
                @foreach ($datesByMonth as $month => $dates)
                    @foreach ($dates as $date)
                        <th>{{ \Carbon\Carbon::parse($date)->format('d') }}</th>
                    @endforeach
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($workers as $worker)
                <tr class="position-relative" style="height: 50px;"> {{-- Control row height --}}
                    <td class="sticky-col bg-white"><strong>{{ $worker->name }}</strong></td>

                    @php
                        $allDatesArray = $allDates->toArray();
                        $skipDates = []; // Track which dates to skip due to colspan
                    @endphp

                    @foreach ($allDatesArray as $date)
                        @php
                            $dateKey = \Carbon\Carbon::parse($date)->format('Y-m-d');

                            // Get all jobs assigned to this worker that intersect this date
                            $jobSchedules = $worker->jobSchedules->filter(function ($job) use ($dateKey) {
                                return $dateKey >= $job->start_date && $dateKey <= $job->end_date;
                            });

                        @endphp

                        <td class="calendar-cell">
                            @if ($jobSchedules->isNotEmpty())
                                @foreach ($jobSchedules as $jobSchedule)
                                    @php
                                        $startdate = $jobSchedule->start_date;
                                        $enddate = $jobSchedule->end_date;
                                    @endphp

                                    <div class="mb-1">
                                        <button type="button"
                                            class="btn btn-sm w-100 text-start
                                                @if ($jobSchedule->status == 'completed') btn-dark
                                                @elseif ($jobSchedule->status == 'inprogress') btn-success
                                                @elseif ($jobSchedule->status == 'cancelled') btn-danger
                                                @else btn-primary @endif"
                                            data-bs-toggle="modal" data-bs-target="#scheduleUpdateModal"
                                            data-id="{{ $jobSchedule->id }}">
                                            {{ $jobSchedule->job_name }} - {{ $jobSchedule->job_title }} {{ $jobSchedule->id }}
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
<div class="modal fade schedule-update-modal" id="scheduleUpdateModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <form class="modal-content" id="scheduleUpdateForm">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title" id="backDropModalTitle">Schedule Details</h5>
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
                                <label for="upSelect2Info" class="form-label">staffs</label>
                                <div class="select2-info">
                                    <select id="upSelect2Info" class="select2 form-select" name="workers[]" multiple>

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
                                <label for="instructionu" class="form-label">Instructions</label>
                                <div class="form-control p-0">
                                    <div class="border-0 pb-6" id="instructionu"></div>
                                </div>
                                <input type="hidden" name="instructions" id="instructionu_input">
                            </div>
                        </div>
                        <div class="row g-4">
                            <div class="col mb-0">
                                <label for="status" class="form-label">Status</label>
                                <select id="status" name="status" class="form-select">
                                    
                                </select>
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
                <input type="hidden" id="jobScheduleId">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                    Close
                </button>
                <button type="button" class="btn btn-primary" onclick="updateSchedule(event)">Update</button>
                <button class="btn btn-md btn-danger"
                    onclick="removeAssignment(this.closest('form').querySelector('#jobScheduleId').value); return false;">
                    <i class="bi bi-trash"></i>
                    Delete Assignment
                </button>
            </div>
        </form>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('scheduleUpdateModal');
        if (!modal) return;

        // Initialize Flatpickr
        const flatpickrInput = modal.querySelector('#flatpickr-range2');
        if (flatpickrInput) {
            flatpickr(flatpickrInput, {
                mode: 'range',
                static: true,
                allowInput: false,
                dateFormat: 'Y-m-d',
            });
        }

        // Initialize Quill
        const quillEditor = new Quill('#instructionu', {
            theme: 'snow',
            placeholder: 'Write your message...',
        });

        // Initialize Select2 (deferred to on modal show)
        const workerSelect = $(modal).find('#upSelect2Info');
        if (workerSelect.length) {
            workerSelect.select2({
                dropdownParent: $(modal)
            });
        }

        // Show Event Handler
        modal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const jobScheduleId = button?.getAttribute('data-id');

            setTimeout(() => {
                const jobNameInput = modal.querySelector('#jobname');
                const instructionInput = modal.querySelector('#instructionu_input');
                const jobScheduleIdInput = modal.querySelector('#jobScheduleId');
                const workerSelectEl = modal.querySelector('#upSelect2Info');
                const statusSelectEl = modal.querySelector('#status');

                if (!jobNameInput || !flatpickrInput || !instructionInput || !workerSelectEl || !statusSelectEl) {
                    console.error('Missing fields in modal.');
                    return;
                }

                fetch(`/scheduler/job-schedule/${jobScheduleId}`)
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

                        // Set Fields
                        jobNameInput.value = schedule.job_name || '';
                        flatpickrInput.value =
                            `${schedule.start_date} to ${schedule.end_date}`;
                        quillEditor.root.innerHTML = '';
                        quillEditor.clipboard.dangerouslyPasteHTML(schedule.instructions ||
                            '');
                        instructionInput.value = schedule.instructions || '';

                        // Populate Workers
                        const assignedWorkerIds = workers.map(w => w.id.toString());
                        workerSelectEl.innerHTML = '';
                        workerslist.forEach(worker => {
                            const option = document.createElement('option');
                            option.value = worker.id;
                            option.textContent = worker.name;
                            if (assignedWorkerIds.includes(worker.id.toString())) {
                                option.selected = true;
                            }
                            workerSelectEl.appendChild(option);
                        });
                        $(workerSelectEl).trigger('change');

                        // Populate Status Option
                        const statusOptions = ['assigned', 'inprogress', 'completed', 'cancelled'];
                        statusSelectEl.innerHTML = '';
                        statusOptions.forEach(status => {
                            const option = document.createElement('option');
                            option.value = status;
                            option.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                            if (schedule.status === status) {
                                option.selected = true;
                            }
                            statusSelectEl.appendChild(option);
                        });

                        // Set Schedule ID
                        jobScheduleIdInput.value = schedule.id;

                        // Job Details
                        modal.querySelector('#jobDescription').innerHTML = job.job_description || '';
                        modal.querySelector('#companyName').textContent = jobCustomer
                            .company_name || '';
                        modal.querySelector('#contactName').textContent =
                            `${jobAddress.contact_firstname} ${jobAddress.contact_lastname}`
                            .trim();
                        modal.querySelector('#contactEmail').textContent = jobAddress
                            .contact_email || '';
                        modal.querySelector('#contactPhone').textContent = jobAddress
                            .contact_phone || '';
                        modal.querySelector('#contactMobile').textContent = jobAddress
                            .contact_mobile || '';
                        modal.querySelector('#jobAddress').textContent =
                            `${jobAddress.address}, ${jobAddress.county}, ${jobAddress.country}`;

                        const addressParam = encodeURIComponent(
                            `${jobAddress.address}, ${jobAddress.county}, ${jobAddress.country}`
                            );
                        modal.querySelector('#jobMap').src =
                            `https://www.google.com/maps/embed/v1/place?key=${GOOGLE_MAPS_API_KEY}&q=${addressParam}`;
                    });
            }, 100);
        });

        // Reset Modal on Close
        modal.addEventListener('hidden.bs.modal', function() {
            modal.querySelector('#jobname').value = '';
            flatpickrInput.value = '';
            modal.querySelector('#instructionu_input').value = '';
            quillEditor.root.innerHTML = '';
            modal.querySelector('#upSelect2Info').innerHTML = '';
            modal.querySelector('#jobScheduleId').value = '';

            const select2 = $(modal).find('#upSelect2Info');
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
</script>

<script>
    function updateSchedule(e) {
        e.preventDefault();

        const quillEditorU = new Quill('#instructionu', {
            theme: 'snow',
            placeholder: 'Write your message...',
        });

        $('#instructionu_input').val(quillEditorU.root.innerHTML);

        const $btn = $(this);
        const $form = $('#scheduleUpdateForm');

        const formData = new FormData($form[0]);
        const jobScheduleId = $form.find('#jobScheduleId').val();

        // console.log(jobScheduleId);
        // return;
        $.ajax({
            url: `scheduler/update-job-schedule/${jobScheduleId}`,
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
                quillEditorU.setContents([]);
                $('#scheduleUpdateModal').modal('hide');
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
    function removeAssignment(assignmentId) {
        if (!confirm('Are you sure you want to remove this assignment?')) return;
        fetch(`/scheduler/delete-schedule/${assignmentId}`, {
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
