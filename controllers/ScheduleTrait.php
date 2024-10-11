<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


trait ScheduleTrait
{
    // Fire if the notification is enabled
    public static function dlv_add_wp_schedule_event($event, $options)
    {
        global $WP_CRON_SCHEDULE_INTERVALS;

        $action = $options['action'];
        $recurrence = $options['notifications_email_recurrence'];

        $time = new DateTime();
        $time->add($WP_CRON_SCHEDULE_INTERVALS[$recurrence]);

        if (!wp_next_scheduled($action)) {
            wp_schedule_event($time->getTimestamp(), $recurrence, $action, array($event));
        }

        update_option($event, $options);
    }

    // Fire if the notification is disabled or on plugin deactivation (uninstalling)
    public static function dlv_delete_wp_schedule_event($event)
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

    public static function dlv_change_notifications_status()
    {
        try {
            verify_nonce($_POST);

            $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : null;
            $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : null;
            $send_test_email = isset($_POST['send_test_email']) ? (bool) sanitize_text_field($_POST['send_test_email']) : null;
            $recurrence = isset($_POST['recurrence']) ? sanitize_text_field($_POST['recurrence']) : null;

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

            $notificator = new DLV_Notificator(new self());

            // Validate field recurrence only if notifications are turn on
            if ($status == 'enable' && (!isset($recurrence) || !array_key_exists($recurrence, $notificator->email_recurrences))) {
                echo wp_json_encode([
                    'success' => false,
                    'error' => __('Invalid value received', 'debug-log-viewer')
                ]);
                wp_die();
            }

            $event = $notificator->dlv_build_unique_event_name();

            $options = [
                'notifications_email' => $email,
                'notifications_email_recurrence' => $recurrence,
                'action' => $notificator->action,
            ];

            switch ($status) {
                case 'enable':
                    if ($send_test_email === true) {
                        $notificator->dlv_send_test_email($options);
                    }
                    self::dlv_add_wp_schedule_event($event, $options);
                    break;
                case 'disable':
                    self::dlv_delete_wp_schedule_event($event);
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
