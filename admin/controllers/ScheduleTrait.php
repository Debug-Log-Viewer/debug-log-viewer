<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

trait DBG_LV_ScheduleTrait
{
    // Fire if the notification is enabled
    public static function dbg_lv_add_wp_schedule_event($event, $options)
    {
        global $DBG_LV_WP_CRON_SCHEDULE_INTERVALS;

        $action = $options['action'];
        $recurrence = $options['dbg_lv_notifications_email_recurrence'];

        $time = new DateTime();
        $time->add($DBG_LV_WP_CRON_SCHEDULE_INTERVALS[$recurrence]);

        if (!wp_next_scheduled($action)) {
            wp_schedule_event($time->getTimestamp(), $recurrence, $action, array($event));
        }

        update_option($event, $options);
    }

    // Fire if the notification is disabled or on plugin deactivation (uninstalling)
    public static function dbg_lv_delete_wp_schedule_event($event)
    {
        $options = get_option($event);

        if (!$options) {
            error_log('Options not found for event ' . $event);
            echo wp_json_encode([
                'success' => false,
                'error' => __('Internal error', 'debug-log-viewer')
            ]);
            wp_die();
        }

        if (!array_key_exists('action', $options)) {
            error_log('Action not found in options for event ' . $event);
            echo wp_json_encode([
                'success' => false,
                'error' => __('Internal error', 'debug-log-viewer')
            ]);
            wp_die();
        }

        if (wp_next_scheduled($options['action'], array($event))) {
            wp_clear_scheduled_hook($options['action'], array($event));
        }

        delete_option($event);
    }

    public static function dbg_lv_change_notifications_status()
    {
        dbg_lv_verify_nonce(isset($_POST['wp_nonce']) ? sanitize_text_field(wp_unslash($_POST['wp_nonce'])) : '' );

        try {
            $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : null;
            $status = isset($_POST['status']) ? sanitize_text_field(wp_unslash($_POST['status'])) : null;
            $send_test_email = isset($_POST['send_test_email']) ? (bool) sanitize_text_field(wp_unslash($_POST['send_test_email'])) : null;
            $recurrence = isset($_POST['recurrence']) ? sanitize_text_field(wp_unslash($_POST['recurrence'])) : null;

            if (!$email) {
                echo wp_json_encode([
                    'success' => false,
                    'error'   => __('Email was not passed', 'debug-log-viewer'),
                ]);
                wp_die();
            }

            if (!$status) {
                echo wp_json_encode([
                    'success' => false,
                    'error'   => __('Notification status was not passed', 'debug-log-viewer'),
                ]);
                wp_die();
            }

            $notificator = new DBG_LV_Notificator(new self());

            // Validate field recurrence only if notifications are turn on
            if ($status == 'enable' && (!isset($recurrence) || !array_key_exists($recurrence, $notificator->email_recurrences))) {
                echo wp_json_encode([
                    'success' => false,
                    'error' => __('Invalid value received', 'debug-log-viewer')
                ]);
                wp_die();
            }

            $event = $notificator->dbg_lv_build_unique_event_name();

            $options = [
                'dbg_lv_notifications_email' => $email,
                'dbg_lv_notifications_email_recurrence' => $recurrence,
                'action' => $notificator->action,
            ];

            switch ($status) {
                case 'enable':
                    if ($send_test_email === true) {
                        $notificator->dbg_lv_send_test_email($options);
                    }
                    self::dbg_lv_add_wp_schedule_event($event, $options);
                    break;
                case 'disable':
                    self::dbg_lv_delete_wp_schedule_event($event);
                    break;
            }
            echo  wp_json_encode([
                'success' => true,
            ]);
            wp_die();
        } catch (Exception $e) {
            echo wp_json_encode([
                'success' => false,
                'error'   => $e->getMessage(),
            ]);
            wp_die();
        }
    }
}
