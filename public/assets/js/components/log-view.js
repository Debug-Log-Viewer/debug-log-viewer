/* global jQuery, localStorage, bootstrap, ajaxurl, dbg_lv_backend_data, XMLHttpRequest */
import {
  updateEmailNotifications,
  initEmailNotificationsForm,
  initScrollToTopButton,
  showToast,
  t
} from '../utils.js';

(async ($) => {
  const logsUpdatesIcon = $('.refresh-log i')
  let logsUpdateInterval

  const dataTableConfig = {
    serverSide: false,
    stateSave: true,
    retrieve: true,
    bSort: true,
    processing: true,
    language: {
      processing: `<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">${t('loading_in_process')}</span>`,
      search: '',
      searchPlaceholder: t('search')
    },
    pageLength: 25,
    dom: 'lBfrtip',
    buttons: [
      { extend: 'colvis', postfixButtons: ['colvisRestore'], className: 'dt-action-button' }
    ],
    columns: [
      {
        data: 'timestamp',
        visible: false,
        render: function (data, type, row) {
          // If timestamp is missing, calculate it from datetime
          return data || convertToTimestamp(row.datetime)
        }
      }, // Hidden sortable column for timestamp
      {
        data: 'type',
        render: renderLogTypeBadge
      },
      { data: 'datetime', className: 'datetime' }, // Visible datetime column
      { data: 'description', render: renderDescription, width: '60%' },
      { data: 'file', width: '25%' },
      { data: 'line' }
    ],
    order: [[0, 'desc']], // Order by the hidden timestamp column
    autoWidth: false, // Prevent DataTables from auto-calculating column widths
    initComplete: function () {
      initScrollToTopButton()
      fillInitialData()
      bindDynamicEventHandlers()
    }
  }

  const table = $('#dbg_lv_log-table').DataTable(dataTableConfig)

  function renderLogTypeBadge (data) {
    const typeClasses = {
      Notice: 'bg-dark',
      Warning: 'bg-warning',
      Fatal: 'bg-danger',
      Database: 'bg-primary',
      Parse: 'bg-info'
    }
    const className = typeClasses[data] || 'bg-secondary'
    return `<span class="badge ${className}">${data}</span>`
  }

  function renderDescription (description) {
    const { text, stack_trace } = description

    return stack_trace
      ? `
                <div>${text}</div>
                <a class="call-stack" href="#">${t('call_stack')}</a>
                <div class="stack hidden">
                    <pre>${stack_trace}</pre>
                </div>`
      : text
  }

  function convertToTimestamp (datetimeString) {
    return new Date(datetimeString).getTime()
  }

  async function fillInitialData () {
    try {
      const rawResponse = await jQuery.post(ajaxurl, {
        action: 'dbg_lv_log_viewer_live_update',
        initial: true,
        wp_nonce: dbg_lv_backend_data.ajax_nonce
      })

      if (!rawResponse) {
        return
      }

      const response = JSON.parse(rawResponse)

      if (response.data.length > 0) {
        table.rows.add(response.data).draw()
      }
    } catch (error) {
      showToast(error, 'error')
    }
  }

  function bindDynamicEventHandlers () {
    $('#dbg_lv_log-table tbody').on('click', '.call-stack', function (e) {
      e.preventDefault()
      const stackContent = $(this).next('.stack.hidden').html()
      if (stackContent) {
        const modalElement = $('.modal')
        modalElement.find('.modal-body').html(stackContent)

        // Show the modal using Bootstrap's API
        const bootstrapModal = bootstrap.Modal.getOrCreateInstance(modalElement[0])
        bootstrapModal.show()
      } else {
        console.error('No stack content found.')
      }
    })
  }

  function autoEnableDebugMode () {
    const target = $('#dbg_lv_toggle_debug_mode')

    if (target.is(':checked')) {
      return
    }
    target.off('switchChange.bootstrapSwitch', toggleDebugModeHandler)
    target.bootstrapSwitch('state', true)
    target.on('switchChange.bootstrapSwitch', toggleDebugModeHandler)
  }

  function autoEnableDebugLog () {
    const target = $('#wp_ajax_dbg_lv_toggle_log_in_file')

    if (target.is(':checked')) {
      return
    }
    target.off('switchChange.bootstrapSwitch', toggleDebugLogHandler)
    target.bootstrapSwitch('state', true)
    target.on('switchChange.bootstrapSwitch', toggleDebugLogHandler)
  }

  async function toggleDebugModeHandler () {
    try {
      const rawResponse = await jQuery.post(ajaxurl, {
        action: 'dbg_lv_toggle_debug_mode',
        state: +$(this).is(':checked'),
        wp_nonce: dbg_lv_backend_data.ajax_nonce
      })

      const response = JSON.parse(rawResponse)

      if (!response.success) {
        throw new Error(response.error)
      }
      showToast(`${t('debug_mode')} ${response.state}`, 'success')
    } catch (error) {
      showToast(error, 'error')
    }
  };

  async function toggleDebugLogHandler () {
    try {
      const state = +$(this).is(':checked')

      if (state) {
        autoEnableDebugMode()
      }

      const rawResponse = await jQuery.post(ajaxurl, {
        action: 'wp_ajax_dbg_lv_toggle_log_in_file',
        state,
        wp_nonce: dbg_lv_backend_data.ajax_nonce
      })

      const response = JSON.parse(rawResponse)

      if (!response.success) {
        throw new Error(`${t('request_error')} ${response.error}`)
      }
      showToast(`${t('debug_log_scripts')} ${response.state}`, 'success')
    } catch (error) {
      showToast(error, 'error')
    }
  }

  async function updateLogs () {
    try {
      animateRefreshButton()
      const rawResponse = await jQuery.post(ajaxurl, {
        action: 'dbg_lv_log_viewer_live_update',
        wp_nonce: dbg_lv_backend_data.ajax_nonce
      })
      if (!rawResponse) {
        return
      }

      const response = JSON.parse(rawResponse)

      if (response.action === 'clear') {
        table.clear().draw()
        return
      }

      if (response.data.length > 0) {
        table.rows.add(response.data).draw()
      }
    } catch (error) {
      showToast(error, 'error')
    }
  }

  function startLiveUpdateLogs () {
    const timeout = dbg_lv_backend_data.log_updates_interval * 1000
    logsUpdateInterval = setInterval(updateLogs, timeout)
  }

  function stopLiveUpdateLogs () {
    if (logsUpdateInterval) {
      clearInterval(logsUpdateInterval)
    }
  }

  function animateRefreshButton () {
    logsUpdatesIcon.removeClass('rotate-animation')
    setTimeout(() => logsUpdatesIcon.addClass('rotate-animation'), 100)
  }

  $('.refresh-log').on('click', function () {
    updateLogs()
    animateRefreshButton()
  })

  $('#dbg_lv_log-table').on('xhr.dt', function (e, settings, json, xhr) {
    if (json.info) {
      const parent = $('.table-wrapper')
      const element = parent.find('.log-viewer-info')

      if (element.length > 0) {
        $(element).text(json.info)
      } else {
        $(parent).prepend(`<div class="alert alert-warning log-viewer-info" role="alert">${json.info}</div>`)
      }
    }
  })

  $('#dbg_lv_log_viewer_enable_logging').on('click', async function () {
    try {
      const rawResponse = await jQuery.post(ajaxurl, {
        action: 'dbg_lv_log_viewer_enable_logging',
        wp_nonce: dbg_lv_backend_data.ajax_nonce
      })

      const response = JSON.parse(rawResponse)

      if (!response.success) {
        throw new Error(response.error)
      }
      showToast(t('logging_enabled_successfully'), 'success')
      setTimeout(function () {
        location.reload()
      }, 1000)
    } catch (error) {
      showToast(error, 'error')
    }
  })

  $('#dbg_lv_toggle_debug_mode').on('switchChange.bootstrapSwitch', toggleDebugModeHandler)

  $('#dbg_lv_toggle_debug_scripts').on('switchChange.bootstrapSwitch', async function () {
    try {
      const rawResponse = await jQuery.post(ajaxurl, {
        action: 'dbg_lv_toggle_debug_scripts',
        state: +$(this).is(':checked'),
        wp_nonce: dbg_lv_backend_data.ajax_nonce
      })

      const response = JSON.parse(rawResponse)

      if (!response.success) {
        throw new Error(response.error)
      }
      showToast(`${t('debug_scripts')} ${response.state}`, 'success')
    } catch (error) {
      showToast(error, 'error')
    }
  })

  $('#wp_ajax_dbg_lv_toggle_log_in_file').on('switchChange.bootstrapSwitch', toggleDebugLogHandler)

  $('#dbg_lv_toggle_display_errors').on('switchChange.bootstrapSwitch', async function () {
    try {
      const state = +$(this).is(':checked')

      if (state) {
        autoEnableDebugMode()
      }

      const rawResponse = await jQuery.post(ajaxurl, {
        action: 'dbg_lv_toggle_display_errors',
        state,
        wp_nonce: dbg_lv_backend_data.ajax_nonce
      })

      const response = JSON.parse(rawResponse)

      if (!response.success) {
        throw new Error(response.error)
      }
      showToast(`${t('display_errors')} ${response.state}`, 'success')
    } catch (error) {
      showToast(error, 'error')
    }
  })

  $('.bootstrap-switch').each(function () {
    const _this = $(this)
    const dataOnLabel = _this.data('on-label') || ''
    const dataOffLabel = _this.data('off-label') || ''
    const state = !!_this.attr('checked')

    _this.bootstrapSwitch({
      onText: dataOnLabel,
      offText: dataOffLabel,
      state
    })
  })

  $('.clear-log').on('click', async function () {
    try {
      if (!confirm(t('flush_log_confirmation'))) {
        return
      }
      const rawResponse = await jQuery.post(ajaxurl, {
        action: 'dbg_lv_log_viewer_clear_log',
        wp_nonce: dbg_lv_backend_data.ajax_nonce
      })

      const response = JSON.parse(rawResponse)
      if (!response.success) {
        throw new Error(response.error)
      }
      showToast(t('log_was_cleared'), 'success')
      location.reload()
    } catch (error) {
      showToast(error, 'error')
    }
  })

  $('.download-log').on('click', async function () {
    jQuery.post({
      url: ajaxurl,
      data: {
        action: 'dbg_lv_log_viewer_download_log',
        wp_nonce: dbg_lv_backend_data.ajax_nonce
      },
      xhr: function () {
        const xhr = new XMLHttpRequest()
        xhr.responseType = 'blob'
        return xhr
      },
      success: (response) => {
        const url = URL.createObjectURL(response)
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', 'debug.log')

        document.body.append(link)
        link.click()
        document.body.removeChild(link)
        URL.revokeObjectURL(url)
      },
      error: (xhr, status, error) => {
        showToast(error, 'error')
      }
    })
  })

  await initEmailNotificationsForm($('#dbg_lv_log_viewer_notifications_form'))

  $('#dbg_lv_log_viewer_notifications_form').on('submit', async function (e) {
    e.preventDefault()

    const form = $('#dbg_lv_log_viewer_notifications_form')
    const action = 'dbg_lv_change_log_viewer_notifications_status'

    await updateEmailNotifications(form, action)

    if (form.find('input[type="submit"]')?.val() === 'Enable') {
      autoEnableDebugMode()
      autoEnableDebugLog()
    }
  })

  $('input[name="UpdatesModeRadioOptions"]').on('change', function () {
    const selectedMode = $('input[name="UpdatesModeRadioOptions"]:checked').val()

    try {
      jQuery.post({
        url: ajaxurl,
        data: {
          action: 'dbg_lv_change_logs_update_mode',
          mode: selectedMode,
          wp_nonce: dbg_lv_backend_data.ajax_nonce
        },
        success: (response) => {
          if (selectedMode === 'AUTO') {
            stopLiveUpdateLogs() // Reset old timer
            startLiveUpdateLogs()
          } else {
            stopLiveUpdateLogs()
          }
          showToast(response.data, 'success')
        },
        error: (xhr, status, error) => {
          showToast(error, 'error')
        }
      })
    } catch (error) {
      showToast(error, 'error')
    }
  })

  if (dbg_lv_backend_data.log_updates_mode == 'AUTO') {
    startLiveUpdateLogs()
  }
})(jQuery)
