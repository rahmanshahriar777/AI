function addJobAttributes() {
    const $form = $('#jobAttributeForm');
    const formData = $form.serialize(); // If you're not sending files

    $.ajax({
        url: '/job-attributes/store', // 🔁 Replace with your actual route
        type: 'POST',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        beforeSend: function () {
            // Optional: Disable button or show spinner
            $form.find('button[type="submit"], .btn-primary').prop('disabled', true).text('Saving...');
        },
        success: function (response) {
            toastr.success(response.message || 'Attribute saved successfully.');
            $form[0].reset(); // Clear form
            $('.modal').modal('hide'); // Hide modal (if used)
            location.reload(); // Or dynamically update the UI
        },
        error: function (xhr) {
            let errorMsg = 'Something went wrong.';

            if (xhr.status === 422 && xhr.responseJSON?.errors) {
                const firstError = Object.values(xhr.responseJSON.errors)[0];
                if (firstError?.length) {
                    errorMsg = firstError[0];
                }
            }

            toastr.error(errorMsg);
            console.error(xhr.responseText);
        },
        complete: function () {
            $form.find('button[type="submit"], .btn-primary').prop('disabled', false).text('Save changes');
        }
    });
}

$(document).on('click', '.add-detail-btn', function () {
    const attributeId = $(this).data('id');
    $('#parentAttributeId').val(attributeId); // ✅ Set into hidden input
});

// Optional: Hook into modal submit
$(document).on('submit', '#addDetailForm', function (e) {
    e.preventDefault();
    const attributeId = $('#parentAttributeId').val();
    addAttributeDetails(attributeId);
});

$(document).on('click', '.edit-attribute-btn', function () {
    const attributeId = $(this).data('id');

    // Clear previous values
    $('#updateJobAttributeForm')[0].reset();
    $('#attributrId').val(attributeId);

    // Fetch existing data
    $.ajax({
        url: `/job-attributes/${attributeId}/edit`,
        type: 'GET',
        success: function (response) {

            $('#updateattributename').val(response.name);
            $('#updateattributedescription').val(response.description);
        },
        error: function () {
            toastr.error('Failed to fetch attribute data');
        }
    });
});

function updateJobAttributes() {
    const attributeId = $('#attributrId').val();
    const formData = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        _method: 'PUT',
        attributename: $('#updateattributename').val(),
        attributedescription: $('#updateattributedescription').val(),
    };

    $.ajax({
        url: `/job-attributes/${attributeId}/update`,
        type: 'POST',
        data: formData,
        success: function (response) {
            toastr.success('Job Attribute updated successfully');
            $('#updateJobAttributesModal').modal('hide');
            $('.datatables-ajax').DataTable().ajax.reload(null, false); // Refresh table without resetting page
        },
        error: function (xhr) {
            let message = 'Update failed';
            if (xhr.responseJSON?.message) {
                message = xhr.responseJSON.message;
            }
            toastr.error(message);
        }
    });
}


function addAttributeDetails(attributeId) {
    const detailValue = $('#detailValue').val().trim();
    const detailDescription = $('#detailDescription').val().trim();

    if (!detailValue) {
        toastr.warning('Attribute value is required.');
        return;
    }

    $.ajax({
        url: `/job-attributes/${attributeId}/storedetails`,
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        data: {
            detailValue: detailValue,
            detailDescription: detailDescription
        },
        success: function (response) {
            toastr.success(response.message || 'Detail added successfully');
            $('#addDetailModal').modal('hide');
            $('#addDetailForm')[0].reset();
            $('#jobAttributeTable').DataTable().ajax.reload(null, false);
        },
        error: function (xhr) {
            const error = xhr.responseJSON?.message || 'Failed to add detail';
            toastr.error(error);
        }
    });
}

$(document).on('click', '.edit-detail-btn', function () {
    const id = $(this).data('id');
    const value = $(this).data('value');
    const description = $(this).data('description');

    $('#editDetailId').val(id);
    $('#editDetailValue').val(value);
    $('#editDetailDescription').val(description);
});

$(document).on('submit', '#editDetailForm', function (e) {
    e.preventDefault();

    const detailId = $('#editDetailId').val();
    const formData = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        _method: 'PUT',
        value: $('#editDetailValue').val(),
        description: $('#editDetailDescription').val(),
    };

    $.ajax({
        url: `/job-attributes/${detailId}/updatedetails`, // 👈 Adjust this route
        type: 'POST',
        data: formData,
        success: function (response) {
            toastr.success('Attribute detail updated successfully');
            $('#editDetailModal').modal('hide');

            // Optionally reload the DataTable or child rows
            $('.datatables-ajax').DataTable().ajax.reload(null, false);
        },
        error: function (xhr) {
            toastr.error(xhr.responseJSON?.message || 'Update failed');
        }
    });
});