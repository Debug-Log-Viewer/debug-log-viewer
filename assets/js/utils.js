$ = jQuery;


export async function updateEmailNotifications(form, action) {
    const notificationStatus = form.attr('data-notifications-enabled');
    const emailFiled         = form.find('input[type="email"]');
    const recurrenceFiled    = form.find('select[name="recurrence"]');
    const testEmailCheckbox  = form.find('input[name="send_test_email"]');
    const submitButton       = form.find('input[type="submit"]');

    if (!emailFiled?.val()) {
        toastr.warning('Email is not specified', 'Warning', { timeOut: 5000 });
        return
    }

    submitButton.attr('disabled', 'disabled');

    try {
        if (notificationStatus === 'true') {
            const rawNotificationResponse = await jQuery.post(ajaxurl, {
                action,
                status: 'disable',
                email: emailFiled?.val(),
                wp_nonce: dlv_backend_data.ajax_nonce,
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

            toastr.success('Notifications disabled', 'Success', { timeOut: 5000 });

            const rawUserEmailResponse = await jQuery.post(ajaxurl, {
                action: 'dlv_get_current_user_email',
                wp_nonce: dlv_backend_data.ajax_nonce
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
                wp_nonce: dlv_backend_data.ajax_nonce,
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

            toastr.success('Notifications enabled', 'Success', { timeOut: 5000 });
        }
    } catch (error) {
        toastr.error(error, 'Error', { timeOut: 5000 });
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
                action: 'dlv_get_current_user_email',
                wp_nonce: dlv_backend_data.ajax_nonce
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
        toastr.error(error, 'Error', { timeOut: 5000 });
        return
    }
}


export function initScrollToTopButton() {    
    $('body').append(
        `<div class="scroll-top" id="dlv_scrollToTopButton" style="display: none;">
            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512">
                <path fill="#4f6df5" d="M214.6 41.4c-12.5-12.5-32.8-12.5-45.3 0l-160 160c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 141.2V448c0 17.7 14.3 32 32 32s32-14.3 32-32V141.2L329.4 246.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-160-160z"/>
            </svg>
        </div>`
    );

    $(document).on('scroll', function() {
        if ($(this).scrollTop() > 100) {
            $('#dlv_scrollToTopButton').fadeIn();
        } else {
            $('#dlv_scrollToTopButton').fadeOut();
        }
    });

    $('#dlv_scrollToTopButton').on('click', function() {
        $('html, body').animate({scrollTop: 0}, 800, 'linear');
        return false;
    });
};
