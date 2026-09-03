$(function () {
    // Datepicker
    const date = new Date();
    const productDate = document.querySelector('.product-date');

    if (productDate) {
        productDate.flatpickr({
            monthSelectorType: 'static',
            defaultDate: date
        });
    }
});
$(function () {
    // Select2
    var select2 = $('.select2');
    if (select2.length) {
        select2.each(function () {
            var $this = $(this);
            $this.wrap('<div class="position-relative"></div>').select2({
                dropdownParent: $this.parent(),
                placeholder: $this.data('placeholder') // for dynamic placeholder
            });
        });
    }
});


const quilld = new Quill('#lead-description-edit', {
    theme: 'snow',
    placeholder: 'Write note here...',
});

function updateleadDetails(leadId) {
    const htmlContent = quilld.root.innerHTML.trim();
    const textContent = quilld.getText().trim();

    if (textContent === '') {
        toastr.error('Description cannot be empty');
        return;
    }

    // Add to hidden input
    document.getElementById('leaddescription-hidden').value = htmlContent;
    const form = document.getElementById('leadForm');
    const formData = new FormData(form);
    fetch(`/lead/updatedescription/` + leadId, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
        },
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                toastr.success(data.message);
                location.reload();
            } else {
                toastr.error('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            toastr.error('Something went wrong');
        });
}

try {
    const quilln = new Quill('#leadnotes', {
        theme: 'snow',
        placeholder: 'Write note here...',
    });
} catch (error) {
  // Handle error
} finally {
  // Code that always runs (e.g., cleanup)
  console.log("Cleanup complete.");
}


function addNotes(leadId) {
    const htmlContent = quilln.root.innerHTML.trim();
    const textContent = quilln.getText().trim();

    if (textContent === '') {
        toastr.error('Note is required.');
        return;
    }

    // Add to hidden input
    document.getElementById('leadnotes-hidden').value = htmlContent;

    const notedata = quilln.root.innerHTML.trim();

    $.ajax({
        url: `/lead/add-note/` + leadId, // Your Laravel route
        method: "POST",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            note: notedata,
        },
        success: function (response) {
            if (response.status === 'success') {
                $('#note-message').html('<div class="alert alert-success">' + response.message +
                    '</div>');
                toastr.success('Note added successfully');
                window.location.reload(); // Reload the page to see the new note
            } else {
                toastr.error('Error: ' + response.message);
                $('#note-message').html('<div class="alert alert-danger">' + response.message +
                    '</div>');
            }
        },
        error: function (xhr) {
            let message = "Something went wrong.";
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
            }
            toastr.error(message);
            $('#note-message').html('<div class="alert alert-danger">' + message + '</div>');
        }
    });
}

function clearLeadNote(){
    quilln.setText('');

}

let quillml = null;
if ($('#rejectmessage').length) {
    quillml = new Quill('#rejectmessage', {
        theme: 'snow',
        placeholder: 'Write your message...',
    });
}

// On form submit
$('form').on('submit', function () {
    $('#rejectmessage_input').val(quillml.root.innerHTML);
});

$(document).ready(function () {
    $('.delete-note').click(function () {
        const noteId = $(this).data('id');
        const confirmation = confirm('Are you sure you want to delete this note?');
        if (confirmation) {
            $.ajax({
                url: `/lead/delete-note/${noteId}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        window.location.reload(); // Reload the page to see the changes
                    } else {
                        toastr.error('Error: ' + response.message);
                    }
                },
                error: function (xhr) {
                    toastr.error('Something went wrong!');
                }
            });
        }
    });
});
$(document).ready(function () {
    $('.delete-attachment').click(function () {
        const attachmentId = $(this).data('id');
        const confirmation = confirm('Are you sure you want to delete this attachment?');
        if (confirmation) {
            $.ajax({
                url: `/leads/delete-attachment/${attachmentId}`,
                method: 'DELETE',
                data: {
                    _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                success: function (response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        window.location.reload(); // Reload the page to see the changes
                    } else {
                        toastr.error('Error: ' + response.message);
                    }
                },
                error: function (xhr) {
                    toastr.error('Something went wrong!');
                }
            });
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    // Handle lead image upload
    const imageForm = document.getElementById('lead-image-upload-form');
    imageForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(imageForm);

        fetch(imageForm.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': imageForm.querySelector('input[name="_token"]').value
            },
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    toastr.success(data.message);
                    window.location.reload(); // Reload the page to see the new image
                } else {
                    toastr.error(data.message || 'Upload failed');
                }
            })
            .catch(err => {
                console.error(err);
                toastr.error('Something went wrong');
            });
    });
});

function deleteLeadImage(imageId) {
    const confirmation = confirm('Are you sure you want to delete this image?');

    if (confirmation) {
        $.ajax({
            url: `/leads/delete-image/${imageId}`, // Change URL as per your route
            method: 'DELETE',
            data: {
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            success: function (response) {
                if (response.status === 'success') {
                    toastr.success(response.message || 'Image deleted successfully');
                    window.location.reload(); // Or remove the DOM element dynamically
                } else {
                    toastr.error(response.message || 'Failed to delete image');
                }
            },
            error: function (xhr) {
                toastr.error('Something went wrong while deleting the image.');
                console.error(xhr.responseText);
            }
        });
    }
}


$(document).ready(function () {
    $('.convert-to-job-btn').on('click', function () {
        let leadId = $(this).data('id');
        let status = $(this).data('status');

        // Confirmation dialog
        if (!confirm(`Are you sure you want to send it?`)) {
            return; // Stop if user cancels
        }

        $.ajax({
            url: '/lead-to-job-conversion',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Ensure CSRF token is included
            },
            data: {
                status: status,
                lead_id: leadId
            },
            success: function (response) {
                toastr.success(response.message);

                setTimeout(() => {
                    location.reload();
                }, 1000);
            },
            error: function (xhr) {
                let errorMsg = 'An error occurred.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }

                toastr.error(errorMsg);
            }
        });
    });

    // Handle form submission for editing user
    $('#editLocationForm').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        const form = $(this);
        const formData = form.serialize();
        //const customerId = {{ $customer->id }}; // Pass server-side ID to JS

        $.ajax({
            url: $(this).attr('action'), // Update to your route
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            },
            beforeSend: function() {
                // Optional: show loader or disable submit button
            },
            success: function(response) {
                // Handle success (e.g., close modal, show success message)
                //toastr.success('Customer details updated successfully.');
                toastr.success(response.message);
                $('#editLocationForm').modal('hide');
                setTimeout(function(){
                    location.reload(); // This will reload the entire page
                },1000);
            },
            error: function(xhr) {
                // Handle errors
                let errors = xhr.responseJSON.errors;
                let message = 'Update failed.';

                if (errors) {
                    message = Object.values(errors).flat().join('\n');
                }
                toastr.error(message);
            }
        });
    });

});

function updateLeadStatus(leadId, status) {
    const htmlContent = quillml.root.innerHTML.trim();
    const textContent = quillml.getText().trim();

    let lead_status_message = '';
    if(status == 'rejected'){
        if (textContent === '') {
            toastr.error('Message is required.');
            return;
        }
        lead_status_message = htmlContent;
    }

    if (!confirm(`Are you sure you want to change the status to "${status}"?`)) {
        return; // Stop if user cancels
    }

    $.ajax({
        url: `/leads/updatestatus/${leadId}`,
        type: 'POST',
        data: {
            lead_status: status,
            lead_status_message: lead_status_message,
            _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
        },
        success: function (response) {
            if (response.status === 'success') {
                toastr.success(`Status updated successfully to "${status}"`);
                
                setTimeout(function(){
                    //window.location.reload(); 
                    window.location.href = `/leads`;
                }, 1000);
            } else {
                toastr.error('Error: ' + response.message);
            }
        },
        error: function (xhr) {
            let error_message = "Something went wrong.";
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                error_message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
            }
            toastr.error(error_message);
        }
    });
}


function uploadAttachments(leadId, btnElement) {
    const form = document.getElementById('uploadAttachmentsForm');
    const originalBtnText = btnElement.innerHTML;

    const formData = new FormData(form);
    formData.set('leadid', leadId);

    $.ajax({
        url: form.getAttribute('action'),
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function () {
            btnElement.disabled = true;
            btnElement.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...`;
        },
        success: function (response) {
            form.reset();
            $('#uploadAttachmentsModalTitle').closest('.modal').modal('hide');
            toastr.success('Attachments uploaded successfully');
            location.reload();
        },
        error: function (xhr) {
            const msg = xhr.responseJSON?.message || 'Failed to upload attachments.';
            toastr.error(msg);
            console.error(xhr.responseText);
        },
        complete: function () {
            btnElement.disabled = false;
            btnElement.innerHTML = originalBtnText;
        }
    });
}
