$ = jQuery;


export async function updateEmailNotifications(form, action) {
    const notificationStatus = form.attr('data-notifications-enabled');
    const emailFiled         = form.find('input[type="email"]');
    const recurrenceFiled    = form.find('select[name="recurrence"]');
    const testEmailCheckbox  = form.find('input[name="send_test_email"]');
    const submitButton       = form.find('input[type="submit"]');

    if (!emailFiled?.val()) {
        showToast('Email is not specified', 'warning');
        return
    }

    submitButton.attr('disabled', 'disabled');

    try {
        if (notificationStatus === 'true') {
            const rawNotificationResponse = await jQuery.post(ajaxurl, {
                action,
                status: 'disable',
                email: emailFiled?.val(),
                wp_nonce: dbg_lv_backend_data.ajax_nonce,
            });

            const notificationResponse = JSON.parse(rawNotificationResponse);

            if (!notificationResponse.success) {
                throw new Error(notificationResponse.error || 'Request error');
            }

            form.attr('data-notifications-enabled', 'false');
            submitButton
                .removeClass('disable')
                .addClass('enable')
                .val('Enable');

            showToast('Notifications disabled', 'success');

            const rawUserEmailResponse = await jQuery.post(ajaxurl, {
                action: 'dbg_lv_get_current_user_email',
                wp_nonce: dbg_lv_backend_data.ajax_nonce
            });

            let userEmailResponse = JSON.parse(rawUserEmailResponse);

            if (!userEmailResponse.success) {
                throw new Error(userEmailResponse.error || 'Request error');
            }

            emailFiled.val(userEmailResponse.data).prop('disabled', false);;
            recurrenceFiled.prop('disabled', false);;
            testEmailCheckbox.prop('disabled', false).closest('.form-check').show();

        } else {
            const rawResponse = await jQuery.post(ajaxurl, {
                action,
                status: 'enable',
                recurrence: recurrenceFiled?.val(),
                email: emailFiled?.val(),
                send_test_email: +testEmailCheckbox?.is(':checked'),
                wp_nonce: dbg_lv_backend_data.ajax_nonce,
            });

            let response = JSON.parse(rawResponse);

            if (!response.success) {
                throw new Error(response.error || 'Request error');
            }

            emailFiled.prop('disabled', true);
            recurrenceFiled.prop('disabled', true);
            testEmailCheckbox.prop('disabled', true).closest('.form-check').hide();

            submitButton
                .removeClass('enable')
                .addClass('disable')
                .val('Disable')

            form.attr('data-notifications-enabled', 'true');

            showToast('Notifications enabled', 'success');
        }
    } catch (error) {
        showToast(error, 'error');
    }

    submitButton.prop('disabled', false);
}


export async function initEmailNotificationsForm(form) {
    const emailFiled        = form.find('input[type="email"]');
    const recurrenceFiled   = form.find('select[name="recurrence"]');
    const testEmailCheckbox = form.find('input[name="send_test_email"]');
    const submitButton      = form.find('input[type="submit"]');

    try {
        if (form.attr('data-notifications-enabled') === 'true') {
            emailFiled.prop('disabled', true);
            recurrenceFiled.prop('disabled', true);
            testEmailCheckbox.prop('disabled', true).closest('.form-check').hide();
            submitButton
                .prop('disabled', false)
                .removeClass('enabled')
                .addClass('disable')
                .val('Disable');
        } else {
            const rawResponse = await jQuery.post(ajaxurl, { 
                action: 'dbg_lv_get_current_user_email',
                wp_nonce: dbg_lv_backend_data.ajax_nonce
             });

            let response = JSON.parse(rawResponse);

            if (!response.success) {
                throw new Error(response.error || 'Request error');
            }

            emailFiled.val(response.data);
            recurrenceFiled.prop('disabled', false);
            testEmailCheckbox.prop('disabled', false).closest('.form-check').show();
            submitButton
                .prop('disabled', false)
                .removeClass('disable')
                .addClass('enable').val('Enable');
        }
    } catch (error) {
        showToast(error, 'error');
    }
}


export function initScrollToTopButton() {    
    $('body').append(
        `<div class="scroll-top" id="dbg_lv_scrollToTopButton" style="display: none;">
            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512">
                <path fill="#4f6df5" d="M214.6 41.4c-12.5-12.5-32.8-12.5-45.3 0l-160 160c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 141.2V448c0 17.7 14.3 32 32 32s32-14.3 32-32V141.2L329.4 246.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-160-160z"/>
            </svg>
        </div>`
    );

    $(document).on('scroll', function() {
        if ($(this).scrollTop() > 100) {
            $('#dbg_lv_scrollToTopButton').fadeIn();
        } else {
            $('#dbg_lv_scrollToTopButton').fadeOut();
        }
    });

    $('#dbg_lv_scrollToTopButton').on('click', function() {
        $('html, body').animate({scrollTop: 0}, 800, 'linear');
        return false;
    });
};

export function showToast(message, level) {
    const $toast = $('#custom-toast');
    const $toastBody = $('#toast-body');

    // Set the message
    $toastBody.text(message);

    // Determine the level (success, error, warning)
    let toastClass = 'bg-success';
    if (level === 'error') {
        toastClass = 'bg-danger';
    } else if (level === 'warning') {
        toastClass = 'bg-warning';
    }
    // Remove previous level classes and add the new one
    $toastBody.removeClass('bg-success bg-danger bg-warning').addClass(toastClass);
    // Show the toast
    const toast = new bootstrap.Toast($toast[0]);
    toast.show();
}
