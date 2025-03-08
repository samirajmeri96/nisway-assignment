import 'jquery-validation'

window.addEventListener('DOMContentLoaded', function () {
    const jQueryValidateConfigs = {
        errorElement: 'div',
        validClass : 'is-valid',
        errorClass: 'is-invalid text-danger'
    };

    // XML file validation
    $.validator.addMethod("xmlFile", function (value, element) {
        if (element?.files?.length > 0) {
            let file = element.files[0];
            let fileName = file.name.toLowerCase();
            let fileType = file.type;

            return fileName.endsWith(".xml") || ['application/xml', 'text/xml'].includes(fileType);
        }
    }, "Please upload a valid xml file.");

    $.validator.addMethod("mobileNumber", function (value, element) {
        if (value) {
            return /\+91[0-9]{10}/.test(value);
        }
        return true;
    }, "The phone number must be starts with +91, for example: +919876543210");

    const addEditContactForm = $('#add-edit-contact-form');
    if (addEditContactForm) {
        addEditContactForm.validate({
            ...jQueryValidateConfigs,
            rules: {
                first_name: {
                    required: true,
                    maxlength: 40
                },
                last_name: {
                    required: true,
                    maxlength: 40
                },
                phone: {
                    required: true,
                    mobileNumber: true,
                    maxlength: 13,
                    minlength: 13
                }
            }
        });
    }

    const importContactForm = $('#import-contact-form');
    if (importContactForm) {
        importContactForm.validate({
            ...jQueryValidateConfigs,
            rules: {
                contacts: {
                    required: true,
                    xmlFile: true
                }
            },
            submitHandler: function (form, event) {
                event.preventDefault();
                const formData = new FormData(form);

                $.ajax({
                    type: 'post',
                    url: $(form).attr('action'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") // Add CSRF token
                    },
                    success: function (response) {
                        if(response.status === 'success') {
                            alertMessage.success(response?.message);
                            var importContactModal = bootstrap.Modal.getInstance(document.getElementById("import-contact-modal"));
                            importContactModal.hide();
                            let contactsTable = $('#contacts-table').DataTable();
                            contactsTable.draw();
                        } else {
                            let messageString = '';
                            if (response?.validation_errors) {
                                messageString += `<p>${response?.message || 'Please solve the errors in file.'}</p>`;
                                messageString += '<ul>';
                                response?.validation_errors.forEach((errorMessage) => {
                                    messageString += `<li>${errorMessage}</li>`;
                                });
                                messageString += '</ul>';
                            } else {
                                messageString += response?.message || 'Something went wrong, please try again!';
                            }
                            alertMessage.error(messageString, 'import-message-container');
                        }
                    },
                    error: function (JsXHR, status, errorMessage) {
                        alertMessage.error(errorMessage || 'Something went wrong, please type again.')
                    }
                });
            }
        });
    }
})
