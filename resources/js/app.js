import $ from 'jquery';
window.$ = window.jQuery = $;
import './bootstrap';

// Allow digits only in field.
$(document).on('input', '.digits-only', function (event) {
    $(this).val($(this).val().replace(/^\+[^0-9]/g, ''));
})

window.alertMessage = {
    success: function (message, elementId = 'default-alert-container', defaultSeconds = 10) {
        $(`#${elementId}`).html(`<div class="alert alert-success alert-dismissible" id="message-container-success">${message}</div>`);
        this.messageTimeout($(`#${elementId}`), defaultSeconds);
    },
    error: function (message, elementId = 'default-alert-container', defaultSeconds = 10) {
        $(`#${elementId}`).html(`<div class="alert alert-danger alert-dismissible" id="message-container-error">${message}</div>`);
        this.messageTimeout($(`#${elementId}`), defaultSeconds);
    },
    messageTimeout: function (messageContainer, defaultSeconds) {
        if (defaultSeconds > 0) {
            const miliSeconds = defaultSeconds * 1000;
            setTimeout(() => {
                messageContainer.html('');
            }, miliSeconds)
        }
    }
}

import './datatables';
import './form-validations';
