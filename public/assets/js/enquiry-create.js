

$('.new-customer').hide();
document.addEventListener('DOMContentLoaded', function () {
    const editors = [];

    // Initialize all Quill editors with their respective toolbars and inputs
    document.querySelectorAll('.enquiry-editor, .enquiry-description-editor').forEach(function (editorEl) {
        const targetSelector = editorEl.getAttribute('data-target');
        const inputEl = document.querySelector(targetSelector);

        const quill = new Quill(editorEl, {
            theme: 'snow',
            placeholder: 'Write here...',
        });

        editors.push({ quill, inputEl });

        if(inputEl.value){
            const delta = quill.clipboard.convert({html:inputEl.value});
            quill.setContents(delta, 'silent');
        }
    });

    // On form submit, update hidden inputs with editor HTML
    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            let submittype = $('#submittype').val();
            if(submittype=='AJAX'){
                event.preventDefault();
            }
            
            var submitBtn = $('#submitButton');

            editors.forEach(({ quill, inputEl }) => {
                const html = quill.root.innerHTML.trim();
                if (inputEl) {
                    inputEl.value = (html === '<p><br></p>' || html === '') ? '' : html;
                    if(inputEl.name=='enquiry'){
                        //alert('The Customer Enquiry body cannot be empty.');
                        quill.focus();
                    }
                    
                }
            });

            if(submittype=='AJAX'){
                const form = $(this);
                const formData = form.serialize();
                
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    },
                    beforeSend: function() {
                        submitBtn.prop('disabled', true).text('Processing...');
                    },
                    success: function (response) {
                        if (response.success == 'success') {
                            // toastr.success(response.message);
                            
                            // setTimeout(function(){
                            //     window.location.href = response.redirect;
                            // },1000);

                            $('#submittype').val(response.redirect);

                            form.submit();

                        } else {
                            toastr.warning(response.message); // Use the warning/error toastr method
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        
                        // To loop through multiple field errors if provided in the JSON
                        if (jqXHR.responseJSON && jqXHR.responseJSON.errors) {
                            $.each(jqXHR.responseJSON.errors, function(field, message) {
                                // You can show a toast for each specific field error
                                // or handle them differently (e.g., showing near the form field)
                                toastr.error(message, 'Validation Error');
                            });
                        }
                        else{
                            // Handle actual HTTP errors (e.g., 4xx or 5xx status codes)
                            if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                                // Display a specific message from the error JSON response
                                toastr.error(jqXHR.responseJSON.message, 'Error');
                            } else {
                                toastr.error('An unexpected error occurred.', 'Error');
                            }
                        }
                    },
                    complete: function() {
                        // Re-enable the button and restore text after the request is complete
                        submitBtn.prop('disabled', false).text('Save draft');
                    }
                });
            }

        });
    });
});


document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('attachments');
    const previewContainer = document.getElementById('preview');

    if (input) {
        input.addEventListener('change', function (event) {
            previewContainer.innerHTML = ''; // Clear existing previews

            Array.from(this.files).forEach(file => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.height = '80px';
                        img.style.border = '1px solid #ccc';
                        img.style.padding = '2px';
                        img.style.borderRadius = '4px';
                        previewContainer.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    }
});

function toggleNewCustomer(event) {
    if (event) event.preventDefault();

    const $section = $('.new-customer');
    const $button = $('.toggle-customer-btn'); // Use a class to select the button
    const $select = $('#customer'); // Directly using ID

    $section.toggle(); // Show/hide the form

    if ($section.is(':visible')) {
        // If section is visible, change to 'Cancel' with cross icon
        $button.html('<i class="icon-base ti tabler-x icon-md"></i> Cancel');
        $select.prop('disabled', true); // Enable the select field

    } else {
        // If section is hidden, revert to 'Add New Customer' with plus icon
        $button.html('<i class="icon-base ti tabler-plus icon-md"></i> Add New Customer');
        $select.prop('disabled', false); // Enable the select field
    }
}


// Event listener to handle clicking on a suggestion (run this once on page load)
$(document).on('click', '.suggestion-item', function (e) {
    e.preventDefault();
    let selectedAddress = $(this).text();
    let posttown = $('#posttown').val();
    $.ajax({
        url: `/checkposttown/${posttown}?a=${selectedAddress}`,
        method: 'GET',
        success: function (response) {
            if (response && response.address) {

                $('#formtabs-user-company').val('');
                //if($('#formtabs-user-company').is(':visible')){
                    if(response.company_name){
                        $('#formtabs-user-company').val(response.company_name).focus();
                    }
                //}

                $('#formtabs-enquiry-address').val(selectedAddress);
                $('#formtabs-enquiry-county').val(response.address.county);
                $('#formtabs-enquiry-country').val(response.address.country);
                $('#address-suggestions').hide();
            } else {
                $('#formtabs-enquiry-address').val("Error fetching address.");
            }
        },
        error: function () {
            $('#formtabs-enquiry-address').val("Error fetching address.");
        }
    });

});

function checkaddress() {
    let postcode = $('#formtabs-enquiry-postcode').val().replace(/\s/g, '');

    if (!postcode) {
        toastr.error("Please enter a postcode.");
        return;
    }

    $('#loading-spinner').show();
    $('#address-suggestions').hide().empty(); // Clear old suggestions

    $.ajax({
        url: `/checkpostcode/${postcode}`,
        method: 'GET',
        success: function (response) {
            $('#loading-spinner').hide();

            if (!response || response.error || !response.address || !Array.isArray(response.address
                .addresses)) {
                $('#formtabs-enquiry-address').val("No address found.");
                return;
            }

            let suggestions = response.address.addresses;

            if (suggestions.length === 0) {
                $('#formtabs-enquiry-address').val("No address found.");
                return;
            }

            let suggestionList = suggestions.map(address =>
                `<a href="#" class="list-group-item list-group-item-action suggestion-item">${address}</a>`
            ).join('');

            $('#address-suggestions').html(suggestionList).show();
            $('#posttown').val(response.address.post_town);
        },
        error: function () {
            $('#loading-spinner').hide();
            $('#address-suggestions').hide();
            $('#formtabs-enquiry-address').val("Error fetching address.");
        }
    });
}

// Hide suggestions when clicking outside
$(document).click(function (event) {
    if (!$(event.target).closest('#formtabs-enquiry-postcode, #address-suggestions').length) {
        $('#address-suggestions').hide();
    }
});