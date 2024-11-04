import { updateEmailNotifications } from '../utils.js';
import { initEmailNotificationsForm } from '../utils.js';
import { initScrollToTopButton } from '../utils.js';
import { generateUUID } from '../utils.js';


(async ($) => {
    const dataTableConfig = {
        serverSide: true,
        stateSave: true,
        retrieve: true,
        bSort: false,
        processing: true,
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> ',
            search: '',
            searchPlaceholder: 'Search',
        },
        pageLength: 25,
        ajax: {
            url: `${ajaxurl}?action=dbg_lv_get_log_data`,
            type: "POST",
            data: function (d) {
                d.wp_nonce = dbg_lv_backend_data.ajax_nonce
            }
        },
        dom: 'lBfrtip',
        buttons: [
            { extend: 'colvis', postfixButtons: ['colvisRestore'], className: 'dt-action-button' },
        ],
        columns: [{
            data: 'type',
            render: function (data) {
                let className = '';
                switch (data) {
                    case 'Notice':
                        className = 'bg-dark';
                        break;
                    case 'Warning':
                        className = 'bg-warning';
                        break;
                    case 'Fatal':
                        className = 'bg-danger';
                        break;
                    case 'Database':
                        className = 'bg-primary';
                        break;
                    case 'Parse':
                        className = 'bg-info';
                        break;
                    default:
                        className = 'bg-secondary';
                }

                return `<span class="badge ${className}">${data}</span>`
            }
        },
        {
            data: 'datetime',
            className: 'datetime'
        },
        {
            data: 'description',
            render: function (description) {
                if (!description.stack_trace) {
                    return description.text;
                } else {
                    const uniqueId = generateUUID();
                    return `<div>${description.text}</div>
                       
                        <a class="call-stack" href="${uniqueId}">Call stack</a>
                        
                        <div class="modal mt-5" tabindex="-1" id="${uniqueId}">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">

                                    <div class="modal-body">
                                        <pre>${description.stack_trace}</pre>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                }
            }
        },
        {
            data: 'file'
        },
        {
            data: 'line'
        }],
        initComplete: function () {
            initScrollToTopButton();
            initLiveUpdates();
        }
    }

    $('#dbg_lv_log-table').DataTable(dataTableConfig);

    function initLiveUpdates() {
        try {
            const source = new EventSource(ajaxurl + '?action=dbg_lv_log_viewer_live_update', {
                withCredentials: true,
            });

            source.addEventListener('updates', (event) => {
                $('#dbg_lv_log-table').DataTable().draw();
            }, false);

        } catch (error) {
            toastr.error(error, 'Error', { timeOut: 5000 });
        }
    }

    function autoEnableDebugMode() {
        const target = $('#dbg_lv_toggle_debug_mode');

        if (target.is(':checked')) {
            return;
        }
        target.off('switchChange.bootstrapSwitch', toggleDebugModeHandler);
        target.bootstrapSwitch('state', true);
        target.on('switchChange.bootstrapSwitch', toggleDebugModeHandler);
    }

    function autoEnableDebugLog() {
        const target = $('#dbg_lv_toggle_debug_log_scripts');

        if (target.is(':checked')) {
            return;
        }
        target.off('switchChange.bootstrapSwitch', toggleDebugLogHandler);
        target.bootstrapSwitch('state', true);
        target.on('switchChange.bootstrapSwitch', toggleDebugLogHandler);
    }

    async function toggleDebugModeHandler() {

        try {
            const rawResponse = await jQuery.post(ajaxurl, {
                action: 'dbg_lv_toggle_debug_mode',
                state: +$(this).is(':checked'),
                wp_nonce: dbg_lv_backend_data.ajax_nonce,
            });

            let response = JSON.parse(rawResponse);

            if (!response.success) {
                throw new Error(response.error);
            }

            toastr.success(`Debug mode: ${response.state}`, 'Success', { timeOut: 5000 });

        } catch (error) {
            toastr.error(error, 'Error', { timeOut: 5000 });
        }
    };

    async function toggleDebugLogHandler() {
        try {
            const state = +$(this).is(':checked');

            if (state) {
                autoEnableDebugMode();
            }

            const rawResponse = await jQuery.post(ajaxurl, {
                action: 'dbg_lv_toggle_debug_log_scripts',
                state,
                wp_nonce: dbg_lv_backend_data.ajax_nonce,
            });

            let response = JSON.parse(rawResponse);

            if (!response.success) {
                throw new Error(`Request error: ${response.error}`);
            }

            toastr.success(`Debug log scripts: ${response.state}`, 'Success', { timeOut: 5000 });

        } catch (error) {
            toastr.error(error, 'Error', { timeOut: 5000 });
        }
    }

    $('#dbg_lv_log-table').on('xhr.dt', function (e, settings, json, xhr) {
        if (json.info) {
            const parent = $('.table-wrapper');
            const element = parent.find('.log-viewer-info');

            if (element.length > 0) {
                $(element).text(json.info);
            } else {
                $(parent).prepend(`<div class="alert alert-warning log-viewer-info" role="alert">${json.info}</div>`);
            }
        }
    });

    $('#dbg_lv_log_viewer_enable_logging').on('click', async function () {
        try {
            const rawResponse = await jQuery.post(ajaxurl, {
                action: 'dbg_lv_log_viewer_enable_logging',
                wp_nonce: dbg_lv_backend_data.ajax_nonce,
            });

            let response = JSON.parse(rawResponse);

            if (!response.success) {
                throw new Error(response.error);
            }

            toastr.success('Logging enabled successfully.', 'Success', { timeOut: 5000 });
            setTimeout(function () {
                location.reload();
            }, 1000);

        } catch (error) {
            toastr.error(error, 'Error', { timeOut: 5000 });
        }
    });

    $('#dbg_lv_toggle_debug_mode').on('switchChange.bootstrapSwitch', toggleDebugModeHandler);

    $('#dbg_lv_toggle_debug_scripts').on('switchChange.bootstrapSwitch', async function () {

        try {
            const rawResponse = await jQuery.post(ajaxurl, {
                action: 'dbg_lv_toggle_debug_scripts',
                state: +$(this).is(':checked'),
                wp_nonce: dbg_lv_backend_data.ajax_nonce,
            });

            let response = JSON.parse(rawResponse);

            if (!response.success) {
                throw new Error(response.error);
            }

            toastr.success(`Debug scripts: ${response.state}`, 'Success', { timeOut: 5000 });

        } catch (error) {
            toastr.error(error, 'Error', { timeOut: 5000 });
        }
    });

    $('#dbg_lv_toggle_debug_log_scripts').on('switchChange.bootstrapSwitch', toggleDebugLogHandler);

    $('#dbg_lv_toggle_display_errors').on('switchChange.bootstrapSwitch', async function () {
        try {
            const state = +$(this).is(':checked');

            if (state) {
                autoEnableDebugMode();
            }

            const rawResponse = await jQuery.post(ajaxurl, {
                action: 'dbg_lv_toggle_display_errors',
                state,
                wp_nonce: dbg_lv_backend_data.ajax_nonce,
            });

            let response = JSON.parse(rawResponse);

            if (!response.success) {
                throw new Error(response.error);
            }

            toastr.success(`Display errors: ${response.state}`, 'Success', { timeOut: 5000 });

        } catch (error) {
            toastr.error(error, 'Error', { timeOut: 5000 });
        }
    });

    $('.bootstrap-switch').each(function () {
        let _this = $(this);
        let dataOnLabel = _this.data('on-label') || '';
        let dataOffLabel = _this.data('off-label') || '';
        let state = !!_this.attr('checked');

        _this.bootstrapSwitch({
            onText: dataOnLabel,
            offText: dataOffLabel,
            state,
        });
    });

    $('.clear-log').on('click', async function () {
        try {
            if (!confirm('Are you sure? After flushing the log, this action can\'t be undone')) {
                return;
            }

            const rawResponse = await jQuery.post(ajaxurl, {
                action: 'dbg_lv_log_viewer_clear_log',
                wp_nonce: dbg_lv_backend_data.ajax_nonce,
            });

            let response = JSON.parse(rawResponse);

            if (!response.success) {
                throw new Error(response.error);
            }

            toastr.success(`Log was cleared`, 'Success', { timeOut: 5000 });

            $('#dbg_lv_log-table').DataTable().ajax.reload();

        } catch (error) {
            toastr.error(error, 'Error', { timeOut: 5000 });
        }
    });

    $('.refresh-log').on('click', async function () {
        try {
            $('#dbg_lv_log-table').DataTable().ajax.reload();

            toastr.success(`Log was refreshed`, 'Success', { timeOut: 5000 });

        } catch (error) {
            toastr.error(error, 'Error', { timeOut: 5000 });
        }
    });

    $('.download-log').on('click', async function () {

        jQuery.post({
            url: ajaxurl,
            data: {
                action: 'dbg_lv_log_viewer_download_log',
                wp_nonce: dbg_lv_backend_data.ajax_nonce
            },
            xhr: function () {
                var xhr = new XMLHttpRequest();
                xhr.responseType = 'blob';
                return xhr;
            },
            success: (response) => {
                const url = URL.createObjectURL(response);
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'debug.log');

                document.body.append(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url);
            },
            error: (xhr, status, error) => {
                toastr.error(error, 'Error', { timeOut: 5000 });
            }
        });
    });


    $('table.dataTable').on('click', '.call-stack', function (e) {
        e.preventDefault();

        const elementUUID = $(this).attr('href');
        new bootstrap.Modal(document.getElementById(elementUUID)).show()
    })

    await initEmailNotificationsForm($('#dbg_lv_log_viewer_notifications_form'));

    $('#dbg_lv_log_viewer_notifications_form').on('submit', async function (e) {
        e.preventDefault();

        const form = $('#dbg_lv_log_viewer_notifications_form');
        const action = 'dbg_lv_change_log_viewer_notifications_status';

        await updateEmailNotifications(form, action);

        if (form.find('input[type="submit"]')?.val() === 'Enable') {
            autoEnableDebugMode();
            autoEnableDebugLog();
        }
    });
})(jQuery)
