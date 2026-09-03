$(function () {
    // Select2 in Modals
    $('#scheduleModal').on('shown.bs.modal', function () {
        $(this).find('.select2').select2({
            dropdownParent: $('#scheduleModal')
        });
    });

    // If you have a fake modal as well:
    $('#fakeScheduleModal').on('shown.bs.modal', function () {
        $(this).find('.select2').select2({
            dropdownParent: $('#fakeScheduleModal')
        });
    });

    // Flatpickr
    const flatpickrRange = document.querySelector('#flatpickr-range');
    if (flatpickrRange) {
        flatpickrRange.flatpickr({
            mode: 'range',
            static: true,
            allowInput: false,
            dateFormat: 'Y-m-d',
        });
    }

    const flatpickrRangeFake = document.querySelector('#flatpickr-range-fake');
    if (flatpickrRangeFake) {
        flatpickrRangeFake.flatpickr({
            mode: 'range',
            static: true,
            allowInput: false,
            dateFormat: 'Y-m-d',
        });
    }

    // Quill
    const quillm = new Quill('#instruction', {
        theme: 'snow',
        placeholder: 'Write your message...',
    });

    $('#saveScheduleBtn').on('click', function (e) {
        e.preventDefault();

        const $btn = $(this);
        const $form = $('#scheduleForm');
        $('#instruction_input').val(quillm.root.innerHTML);

        const formData = new FormData($form[0]);

        $.ajax({
            url: `scheduler/store-job-schedule`,
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                );
            },
            success: function (response) {
                toastr.success(response.message || 'Schedule saved successfully!');
                $form[0].reset();
                quillm.setContents([]);
                $('#scheduleModal').modal('hide');
                $btn.prop('disabled', false).html('Save');
                setTimeout(() => location.reload(), 1000);
            },
            error: function (xhr) {
                const errMsg = xhr.responseJSON?.message || 'Error saving schedule.';
                toastr.error(errMsg);
                $btn.prop('disabled', false).html('Save');
            }
        });
    });

    // Quill
    const quillf = new Quill('#fakeinstruction', {
        theme: 'snow',
        placeholder: 'Write your message...',
    });

    $('#saveFakeScheduleBtn').on('click', function (e) {
        e.preventDefault();

        const $btn = $(this);
        const $form = $('#fakeScheduleForm');
        $('#fakeinstruction_input').val(quillf.root.innerHTML);

        const formData = new FormData($form[0]);

        $.ajax({
            url: `scheduler/store-fake-job-schedule`,
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
                );
            },
            success: function (response) {
                toastr.success(response.message || 'Schedule saved successfully!');
                $form[0].reset();
                quillm.setContents([]);
                $('#fakeScheduleModal').modal('hide');
                $btn.prop('disabled', false).html('Save');
                setTimeout(() => location.reload(), 1000);
            },
            error: function (xhr) {
                const errMsg = xhr.responseJSON?.message || 'Error saving schedule.';
                toastr.error(errMsg);
                $btn.prop('disabled', false).html('Save');
            }
        });
    });



    // AJAX Month Filters
    $('#realMonthFilterForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: `/scheduler/workers-calendar`,
            method: "POST",
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: () => $('#real-calendar-table').html('<p>Loading...</p>'),
            success: (response) => $('#real-calendar-table').html(response.html),
            error: () => alert("Something went wrong. Please try again.")
        });
    });

    $('#fakeMonthFilterForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: `/scheduler/fake-workers-calendar`,
            method: "POST",
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: () => $('#fake-calendar-table').html('<p>Loading...</p>'),
            success: (response) => $('#fake-calendar-table').html(response.htmlf),
            error: () => alert("Something went wrong in fake calendar. Please try again.")
        });
    });
});