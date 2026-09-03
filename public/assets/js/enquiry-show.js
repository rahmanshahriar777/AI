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

let quill;
document.addEventListener('DOMContentLoaded', function () {
    const editorContainer = document.querySelector('#enquiry-description-edit');
    if (editorContainer) {
        quill = new Quill(editorContainer, {
            placeholder: 'Enquiry Description',
            theme: 'snow'
        });
    }
});

function updateEnquiryDetails(enquiryId) {
    const htmlContent = quill.root.innerHTML.trim();
    const textContent = quill.getText().trim();

    if (textContent === '') {
        toastr.danger('Enquiry description is required.');
        return;
    }

    // Add to hidden input
    document.getElementById('enquirydescription-hidden').value = htmlContent;
    const form = document.getElementById('enquiryForm');
    const formData = new FormData(form);
    fetch(`/enquiry/updatedescription/` + enquiryId, {
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
                // Optionally close modal
                $('#editdescriptionModal').modal('hide');
                location.reload(); // This will reload the entire page
            } else {
                toastr.danger('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            toastr.danger('Something went wrong!');
        });
}

let quilln = null;
if ($('#jobnotes').length) {
    quilln = new Quill('#enquirynotes', {
        theme: 'snow',
        placeholder: 'Write note here...',
    });
}


function addNotes(enquiryId) {
    const htmlContent = quilln.root.innerHTML.trim();
    const textContent = quilln.getText().trim();

    if (textContent === '') {
        toastr.error('Note is required.');
        return;
    }

    document.getElementById('enquirynotes-hidden').value = htmlContent;

    $.ajax({
        url: `/enquiries/add-note/` + enquiryId,
        method: "POST",
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        data: {
            note: htmlContent
        },
        success: function (response) {
            toastr.success('Note added successfully');
            window.location.reload();
        },
        error: function (xhr) {
            let message = "Something went wrong.";
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
            }
            toastr.error(message);
        }
    });
}



function deleteEnquiryImage(imageId) {
    const confirmation = confirm('Are you sure you want to delete this image?');

    if (confirmation) {
        $.ajax({
            url: `/enquiries/delete-image/${imageId}`, // Change URL as per your route
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

// let quillm = null;
// if ($('#mailmessage').length) {
//     quillm = new Quill('#mailmessage', {
//         theme: 'snow',
//         placeholder: 'Write your message...',
//     });
// }

// function selectMailTemplate() {
//     const templateId = document.getElementById('mailTemplateSelect').value;
//     if (!templateId) return;

//     $.ajax({
//         url: `/mail-template/${templateId}`,
//         method: 'GET',
//         success: function (res) {
//             quillm.root.innerHTML = res.message;
//         },
//         error: function () {
//             console.error('Failed to load mail template.');
//             toastr.danger('Failed to load mail template.');
//             quillm.root.innerHTML = '';
//         }
//     });
// }

// On form submit
$('form').on('submit', function () {
    $('#mailmessage_input').val(quill.root.innerHTML);
});


$(document).ready(function () {
    let selectedEnquiryId = null;
    let selectedStatus = null;

    // When clicking "SEND to ESTIMATOR"
    $('.convert-to-lead').on('click', function () {
        selectedEnquiryId = $(this).data('id');
        selectedStatus = $(this).data('status');

        // Set hidden values inside modal
        $('#modalEnquiryId').val(selectedEnquiryId);
        $('#modalStatus').val(selectedStatus);

        // Reset previous selections
        $('#leadConversionForm')[0].reset();

        // Show modal
        $('#leadConversionModal').modal('show');
    });

    // Handle modal form submission
    $('#leadConversionForm').on('submit', function (e) {
        e.preventDefault();

        let formData = $(this).serialize();
        let submitButton = $(this).find('button[type="submit"]');
        submitButton.prop('disabled', true).text('Processing...');

        $.ajax({
            url: '/enquiry-to-lead-conversion',
            type: 'POST',
            data: formData,
            success: function (response) {
                $('#leadConversionModal').modal('hide');
                toastr.success(response.message || 'Enquiry sent successfully!');
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
            },
            complete: function () {
                submitButton.prop('disabled', false).text('Send to Estimator');
            }
        });
    });
});



$(document).on('click', '.update-enquiry-status', function (e) {
    e.preventDefault();

    let status = $(this).data('status');
    let enquiryId = $(this).data('id');

    // Confirmation dialog
    if (!confirm(`Are you sure you want to change the status to "${status}"?`)) {
        return; // Stop if user cancels
    }

    $.ajax({
        url: `/enquiry/updatestatus/${enquiryId}`,
        type: 'POST',
        data: {
            status: status,
            _token: $('meta[name="csrf-token"]').attr(
                'content') // Make sure CSRF token is available
        },
        success: function (response) {
            toastr.success('Status updated successfully');
            location.reload();
        },
        error: function (xhr) {
            toastr.danger('Something went wrong');
        }
    });
});

$(document).ready(function () {
    $('.delete-note').click(function () {
        const noteId = $(this).data('id');
        const confirmation = confirm('Are you sure you want to delete this note?');
        if (confirmation) {
            $.ajax({
                url: `/enquiries/delete-note/${noteId}`,
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
    
});
