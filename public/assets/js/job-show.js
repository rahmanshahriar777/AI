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

const quill = new Quill('#job-description-edit', {
    theme: 'snow',
    placeholder: 'Write note here...',
});


function updatejobDetails(jobId) {
    const htmlContent = quill.root.innerHTML.trim();
    const textContent = quill.getText().trim();

    if (textContent === '') {
        toastr.error('Description cannot be empty');
        return;
    }

    // Add to hidden input
    document.getElementById('jobdescription-hidden').value = htmlContent;
    const form = document.getElementById('jobForm');
    const formData = new FormData(form);
    fetch(`/job/updatedescription/${jobId}`, {
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
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                toastr.error('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            toastr.error('Something went wrong');
        });
}

let quilln = null;
if ($('#jobnotes').length) {
    quilln = new Quill('#jobnotes', {
        theme: 'snow',
        placeholder: 'Write note here...',
    });
}

function addNotes(jobId) {
    const htmlContent = quilln.root.innerHTML.trim();
    const textContent = quilln.getText().trim();

    if (textContent === '') {
        toastr.danger('Note is required.');
        return;
    }

    // Add to hidden input
    document.getElementById('jobnotes-hidden').value = htmlContent;

    const notedata = quilln.root.innerHTML.trim();

    $.ajax({
        url: `/job/add-note/${jobId}`, // Your Laravel route
        method: "POST",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Ensure CSRF token is included
        },
        data: {
            job_id: jobId,
            note: notedata,
        },
        success: function (response) {
            if (response.status === 'success') {
                $('#note-message').html('<div class="alert alert-success">' + response.message +
                    '</div>');
                toastr.success('Note added successfully');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                toastr.danger('Error: ' + response.message);
                $('#note-message').html('<div class="alert alert-danger">' + response.message +
                    '</div>');
            }
        },
        error: function (xhr) {
            let message = "Something went wrong.";
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
            }
            toastr.danger(message);
            $('#note-message').html('<div class="alert alert-danger">' + message + '</div>');
        }
    });
}
function clearJobNote(){
    quilln.setText('');

}

let quillm=null;
if ($('#mailmessage').length) {
    quillm = new Quill('#mailmessage', {
        theme: 'snow',
        placeholder: 'Write your message...',
    });
}

function selectMailTemplate() {
    const templateId = document.getElementById('mailTemplateSelect').value;
    if (!templateId) return;

    $.ajax({
        url: `/mail-template/${templateId}`,
        method: 'GET',
        success: function (res) {
            quillm.root.innerHTML = res.message;
        },
        error: function () {
            console.error('Failed to load mail template.');
            quillm.root.innerHTML = '';
            toastr.error('Failed to load mail template.');
        }
    });
}

// On form submit
$('form').on('submit', function () {
    $('#mailmessage_input').val(quillm.root.innerHTML);
});

$(document).ready(function () {
    $('.delete-note').click(function () {
        const noteId = $(this).data('id');
        const confirmation = confirm('Are you sure you want to delete this note?');
        if (confirmation) {
            $.ajax({
                url: `/job/delete-note/${noteId}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Ensure CSRF token is included
                },
                data: {},
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
                url: `/job/delete-attachment/${attachmentId}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Ensure CSRF token is included
                },
                data: {},
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

    // Handle form submission for editing user
    $('#editFinForm').on('submit', function(e) {
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
                $('#editFininfo').modal('hide');
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

document.addEventListener('DOMContentLoaded', function () {
    // Handle job image upload
    const imageForm = document.getElementById('job-image-upload-form');
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
                    
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                    
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

$(document).ready(function () {
    $('.delete-image').click(function () {
        const imageId = $(this).data('id');
        const confirmation = confirm('Are you sure you want to delete this image?');
        if (confirmation) {
            $.ajax({
                url: `/job/delete-image/${imageId}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Ensure CSRF token is included
                },
                data: {},
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

function updateJobStatus(jobid, status) {
    if (!confirm(`Are you sure you want to change the status to "${status}"?`)) {
        return; // Stop if user cancels
    }

    $.ajax({
        url: `/job/updatestatus/${jobid}`,
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Include CSRF token
        },
        data: {
            job_status: status,
        },
        success: function (response) {
            toastr.success('Status updated successfully');
            location.reload();
        },
        error: function (xhr) {
            toastr.error('Something went wrong');
            console.error(xhr.responseText);
        }
    });
}

$('#uploadAttachmentsForm').on('submit', function (e) {
    e.preventDefault();

    let form = $(this)[0];
    let formData = new FormData(form);

    $.ajax({
        url: `job/attachments-upload`, // Change to your actual route
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function () {
            // Optional: Show loader/spinner
        },
        success: function (response) {
            $('#uploadAttachmentsForm')[0].reset();
            $('#uploadAttachmentsModalTitle').closest('.modal').modal('hide');
            toastr.success('Attachments uploaded successfully');
            // Optionally reload part of the page
            location.reload(); // Reload the page to see the new attachments
        },
        error: function (xhr) {
            toastr.error(xhr.responseJSON.message || 'Failed to upload attachments.');
            console.error(xhr.responseText);
        }
    });
});

function updateJobTitle(jobId) {
    const jobTitle = document.getElementById('nameWithTitle').value.trim();

    if (jobTitle === '') {
        toastr.error('Job title cannot be empty');
        return;
    }

    $.ajax({
        url: `/job/updatetitle/${jobId}`,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            job_title: jobTitle,
            job_type: document.getElementById('jobType').value
        },
        success: function (response) {
            if (response.status === 'success') {
                toastr.success(response.message);
                location.reload(); // Optional: reload the page to reflect the change
            } else {
                toastr.error(response.message || 'Something went wrong.');
            }
        },
        error: function (xhr) {
            let message = 'Something went wrong!';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            toastr.error(message);
        }
    });
}