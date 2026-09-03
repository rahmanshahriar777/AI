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

const quill = new Quill('#item-desc', {
    theme: 'snow',
    placeholder: 'Write note here...',
});


function addattribute(stockItemId) {
    const stockAttributeId = $('#stock-attribute').val();

    if (!stockAttributeId) {
        toastr.danger("Please select an attribute.");
        return;
    }

    $.ajax({
        url: "/stockitems/add-attribute",
        method: "POST",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Ensure CSRF token is included
        },
        data: {
            stock_item_id: stockItemId,
            stock_attribute_id: stockAttributeId
        },
        success: function (response) {
            if (response.status) {
                toastr.success(data.message);
                window.location.reload();
            } else {
                toastr.danger(data.message);
            }
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            toastr.danger("Error:", error);
        }
    });
}

$(document).ready(function () {
    $('#variantForm').on('submit', function (e) {
        e.preventDefault(); // Prevent form reload

        const form = $(this);
        const formData = form.serialize();
        const stockItemId = form.data('stock-item-id'); // Pass from Blade

        submitVariantForm(stockItemId, formData);
    });
});

function submitVariantForm(stockItemId, formData) {
    $.ajax({
        url: `/stockitems/${stockItemId}/add-variants`,
        method: 'POST',
        data: formData,
        success: function (res) {
            if (res.status) {
                toastr.success(res.message);
                $('#variantModal').modal('hide');
                $('#variantForm')[0].reset(); // Optional: Reset form
                window.location.reload(); // Reload page to reflect changes
            } else {
                toastr.error(res.message);
            }
        },
        error: function (xhr) {
            toastr.error(xhr.responseJSON?.message || 'Something went wrong.');
        }
    });
}


