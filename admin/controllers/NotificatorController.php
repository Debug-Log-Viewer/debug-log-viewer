<?php

if (!defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}

class DBG_LV_Notificator
{
    public $action;
    public $options;
    public $email_recurrences;
    public $send_test_email_handler;

    public function __construct($instance)
    {
        $dbg_lv_log_controller = new DBG_LV_LogController();

        if ($instance instanceof $dbg_lv_log_controller) {
            $this->action = DBG_LV_LogController::SCHEDULE_MAIL_SEND;
            $this->send_test_email_handler = [$this, 'dbg_lv_send_log_viewer_test_email'];
        }
        $this->email_recurrences = [
            'hourly'     => __('Hourly', 'debug-log-viewer'),
            'twicedaily' => __('Twice Daily', 'debug-log-viewer'),
            'daily'      => __('Daily',  'debug-log-viewer'),
            'weekly'     => __('Weekly', 'debug-log-viewer'),
        ];
        $this->options = get_option($this->dbg_lv_build_unique_event_name());
    }

    public function dbg_lv_build_unique_event_name()
    {
        return strtoupper($this->action . '_user_' . wp_get_current_user()->ID);
    }

    public function dbg_lv_get_notification_email()
    {
        if ($this->options && array_key_exists('dbg_lv_notifications_email', $this->options)) {
            return $this->options['dbg_lv_notifications_email'];
        }
    }

    public function dbg_lv_is_notification_enabled()
    {
        return (bool) $this->options;
    }

    public function dbg_lv_get_notification_recurrence()
    {
        foreach ($this->email_recurrences as $key => $value) {
            $selected = $key == $this->dbg_lv_get_notification_recurrences() ? 'selected="selected"' : '';
            echo sprintf('<option value="%s" %s>%s</option>', esc_attr($key), esc_html($selected), esc_html($value));
        }
    }

    private function dbg_lv_get_notification_recurrences()
    {
        if (!$this->options) {
            return null;
        }

        if (array_key_exists('dbg_lv_notifications_email_recurrence', $this->options)) {
            return $this->options['dbg_lv_notifications_email_recurrence'];
        }
        return null;
    }

    private function dbg_lv_send_log_viewer_test_email($args)
    {
        global $DBG_LV_LOG_VIEWER_EMAIL_LEVELS;

        $email = $args['dbg_lv_notifications_email'];

        if (!isset($email)) {
            return;
        }

        $errors = [];
        foreach ($DBG_LV_LOG_VIEWER_EMAIL_LEVELS as $type) {
            $timestamp = new DateTime();
            $datetime = $timestamp->format('Y-m-d H:i:s e');
            $text = $type . ' error test description';
            $hash = md5($text . '::' . $datetime);

            $errors[$type][$hash] = [
                'datetime'    => $datetime,
                'line'        => '1',
                'file'        => 'example.php',
                'type'        => $type,
                'description' => [
                    'text' => $text,
                    'stack_trace' => null,
                ],
                'hits' => 1,
            ];
        }

        dbg_lv_send_log_viewer_email(
            $email,
            __('Debug Log Viewer: Log monitoring test email', 'debug-log-viewer'),
            realpath(__DIR__) . '/../templates/email/log_viewer.tpl',
            [
                'website' => get_site_url(),
                'errors' => $errors,
            ]
        );
    }

    public function dbg_lv_send_test_email($options)
    {
        if (isset($this->send_test_email_handler) && is_callable($this->send_test_email_handler)) {
            call_user_func($this->send_test_email_handler, $options);
        }
    }
}
