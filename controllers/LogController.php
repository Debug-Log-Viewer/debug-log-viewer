<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

require_once realpath(__DIR__) . '/../vendor/autoload.php';
require_once realpath(__DIR__) . '/../models/LogModel.php';
require_once realpath(__DIR__) . '/../views/pages/log.php';
require_once realpath(__DIR__) . '/../helpers/utils.php';
require_once realpath(__DIR__) . '/ScheduleTrait.php';
require_once realpath(__DIR__) . '/../services/email.php';

class DLV_LogController
{
    use ScheduleTrait;

    const SCHEDULE_MAIL_SEND = 'DLV_NOTIFY_LOG_CONTROLLER';

    private $config_editor;

    public function __construct()
    {
        try {
            $this->config_editor = new WPConfigTransformer(DLV_Constants::get_wp_config_path());
        } catch (Exception $error) {
            // Please, make sure permissions and path is correct.
            // The plugin need an access to wp-config.php to manage debugging constants
        }
    }

    public static function dlv_render_view()
    {
        return DLV_LogView::dlv_render_view();
    }

    public static function dlv_get_debug_file_path()
    {
        if (file_exists(WP_CONTENT_DIR . '/debug.log')) {
            return WP_CONTENT_DIR . '/debug.log';
        }

        return '';
        // For those cases when WP_DEBUG_LOG is setted as a path to debug file (overrided default)
        // @todo: return WP_DEBUG_LOG;
    }

    public static function dlv_get_log_data()
    {
        verify_nonce($_POST);

        $draw = isset($_POST['draw']) ? (int) $_POST['draw'] : 0;
        $start = isset($_POST['start']) ? (int) $_POST['start'] : 1;
        $length = isset($_POST['length']) ? (int) $_POST['length'] : 25;
        $search_value = isset($_POST['search']['value']) ? sanitize_text_field($_POST['search']['value']) : null;

        $storage = [];

        $rows = array_reverse(DLV_LogModel::dlv_parse_log_file());

        if (!$rows) {
            echo wp_json_encode([
                'success' => true,
                'data' => [],
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
            ]);
            wp_die();
        }

        $storage = [];
        $rows_count = 0;
        foreach ($rows as $row) {

            if (empty($row)) {
                continue;
            }

            $storage[] = [
                'datetime'    => DLV_LogModel::dlv_get_datetime_from_row($row),
                'line'        => DLV_LogModel::dlv_get_line_from_log_row($row),
                'file'        => DLV_LogModel::dlv_get_file_from_log_row($row),
                'type'        => DLV_LogModel::dlv_get_type_from_row($row),
                'description' => [
                    'text' => DLV_LogModel::dlv_get_description_from_row($row),
                    'stack_trace' => DLV_LogModel::dlv_get_stack_trace_for_row($row)
                ]
            ];

            $rows_count++;
        }

        if ($search_value) {
            $search_string = trim(strtolower($search_value));
            $data = [];
            foreach ($storage as $index => $row) {
                $results = find_string($row, $search_string);

                if ($results) {
                    $data[] = $storage[$index];
                }
            }

            $search_results_length = count($data);

            $storage = array_slice($data, $start, $length);
        } else {
            $storage = array_slice($storage, $start, $length);
        }

        $filesize = DLV_LogModel::dlv_get_log_filesize(['with_measure_units' => true]);

        if (DLV_LogModel::dlv_is_debug_log_too_big()) {
            $template = __("The debug log file is excessively large (%1\$s). We only parse the most recent %2\$d lines, starting from the date %3\$s.", 'debug-log-viewer');
            $info = sprintf($template, $filesize, $rows_count, DLV_LogModel::dlv_get_datetime_from_row($rows[0]));
        } else {
            $info = null;
        }

        echo wp_json_encode([
            'success' => true,
            'data' => $storage ? $storage : [],
            'draw' => $draw,
            'recordsTotal' => $rows_count,
            'recordsFiltered' => $search_value ? $search_results_length : $rows_count,
            'info' => $info,
        ]);
        wp_die();
    }

    public function dlv_log_viewer_enable_logging()
    {
        verify_nonce($_POST);
        try {

            $path = WP_CONTENT_DIR . '/debug.log';
            if (!is_file($path) || !file_exists($path)) {
                // Create debug.log if missing
                $message = 'This is a demo entry. Debugging is now enabled. If any notices, warnings, or errors occur on your site, they will appear here. Remember to refresh the table to view the latest entries';
                $demo_string = "[" . gmdate('d-M-Y H:i:s T') . "] PHP Notice: <b>" . $message  . "</b>  in " . wp_unslash( $_SERVER['DOCUMENT_ROOT']) . "/example.php on line 0\n";
                file_put_contents($path, $demo_string);
            }

            $this->config_editor->update('constant', 'WP_DEBUG', '1');
            $this->config_editor->update('constant', 'WP_DEBUG_LOG', '1');

            echo wp_json_encode([
                'success' => true,
            ]);
            wp_die();
        } catch (Exception $e) {
            echo wp_json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
            wp_die();
        }
    }

    public function dlv_toggle_debug_mode()
    {
        verify_nonce($_POST);
        $state = $this->dlv_prepare_state();

        try {
            $this->config_editor->update('constant', 'WP_DEBUG', $state);

            echo wp_json_encode([
                'success' => true,
                'state' => (int) $state ? "ON" : "OFF",
            ]);
            wp_die();
        } catch (Exception $e) {
            echo wp_json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
            wp_die();
        }
    }

    public function dlv_toggle_debug_scripts()
    {
        verify_nonce($_POST);
        $state = $this->dlv_prepare_state();

        try {
            $this->config_editor->update('constant', 'SCRIPT_DEBUG', $state);

            echo wp_json_encode([
                'success' => true,
                'state' => (int) $state ? "ON" : "OFF",
            ]);
            wp_die();
        } catch (Exception $e) {
            echo wp_json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
            wp_die();
        }
    }

    public function dlv_toggle_debug_log_scripts()
    {
        verify_nonce($_POST);
        $state = $this->dlv_prepare_state();

        try {
            if ($state == '1') {
                $this->config_editor->update('constant', 'WP_DEBUG', $state);
            }

            $this->config_editor->update('constant', 'WP_DEBUG_LOG', $state);

            echo wp_json_encode([
                'success' => true,
                'state' => (int) $state ? "ON" : "OFF",
            ]);
            wp_die();
        } catch (Exception $e) {
            echo wp_json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
            wp_die();
        }
    }

    public function dlv_toggle_display_errors()
    {
        verify_nonce($_POST);
        $state = $this->dlv_prepare_state();

        try {
            if ($state == '1') {
                $this->config_editor->update('constant', 'WP_DEBUG', $state);
            }

            $this->config_editor->update('constant', 'WP_DEBUG_DISPLAY', $state);

            echo wp_json_encode([
                'success' => true,
                'state' => (int) $state ? "ON" : "OFF",
            ]);
            wp_die();
        } catch (Exception $e) {
            echo wp_json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
            wp_die();
        }
    }

    public static function dlv_clear_log()
    {
        verify_nonce($_POST);

        try {
            $debug_log_path = DLV_LogController::dlv_get_debug_file_path();

            if (is_file($debug_log_path) && file_exists($debug_log_path)) {

                if (is_writable($debug_log_path)) {
                    file_put_contents($debug_log_path, '');

                    echo wp_json_encode([
                        'success' => true
                    ]);
                    wp_die();
                }

                throw new \Exception('Log file was found but can not to be cleared due to missing write permissions');
            }
            throw new \Exception('Log file is not found and can not to be removed');
        } catch (Exception $e) {
            echo wp_json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
            wp_die();
        }
    }

    public static function dlv_download_log()
    {
        verify_nonce($_POST);
        try {
            $debug_log_path = DLV_LogController::dlv_get_debug_file_path();

            if (is_file($debug_log_path) && file_exists($debug_log_path)) {

                $basename = basename($debug_log_path);
                $filesize = filesize($debug_log_path);

                header('Content-Description: File Transfer');
                header('Content-Type: text/plain');
                header("Cache-Control: no-cache, must-revalidate");
                header("Expires: 0");
                header("Content-Disposition: attachment; filename=$basename");
                header("Content-Length: $filesize");
                header('Pragma: public');

                flush();

                readfile($debug_log_path);
                wp_die();
            }

            throw new \Exception('Log file is not found and can not to be removed');
        } catch (Exception $e) {
            echo wp_json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        } finally {
            wp_die();
        }
    }

    public static function dlv_get_current_user_email()
    {

        verify_nonce($_POST);

        try {
            global $current_user;

            echo wp_json_encode([
                'success' => true,
                'data' => $current_user->user_email
            ]);
            wp_die();
        } catch (Exception $e) {
            echo wp_json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
            wp_die();
        }
    }


    public function dlv_prepare_state()
    {
        verify_nonce($_POST);

        if (!isset($_POST["state"])) {
            throw new \Exception('Empty state passed');
        }

        $state = $_POST["state"];
        switch ((int) $state) {
            case 0:
            case 1:
                return (string) $state;
            default:
                throw new \Exception('Incorrect state value passed');
        }
    }

    public static function dlv_parse_critical_log_errors($recurrence)
    {
        global $WP_CRON_SCHEDULE_INTERVALS;
        global $DLV_LOG_VIEWER_EMAIL_LEVELS;

        $rows = DLV_LogModel::dlv_parse_log_file();

        if (!$rows) {
            return;
        }

        $errors = [];
        foreach ($DLV_LOG_VIEWER_EMAIL_LEVELS as $level) {
            $errors[$level] = [];
        }

        $new_lines = 0;
        foreach ($rows as $row) {
            if (empty($row)) {
                continue;
            }

            $datetime = DLV_LogModel::dlv_get_datetime_from_row($row);
            $datetimeOffset = new DateTime();
            $datetimeOffset->sub($WP_CRON_SCHEDULE_INTERVALS[$recurrence]);
            $datetimeError = new DateTime();
            $datetimeError->setTimestamp(strtotime($datetime));

            if ($datetimeError < $datetimeOffset) {
                continue;
            }

            $type = DLV_LogModel::dlv_get_type_from_row($row);

            if (!in_array($type, $DLV_LOG_VIEWER_EMAIL_LEVELS)) {
                continue;
            }

            $line = DLV_LogModel::dlv_get_line_from_log_row($row);
            $file = DLV_LogModel::dlv_get_file_from_log_row($row);
            $text = DLV_LogModel::dlv_get_description_from_row($row);
            $hash = md5($line . '::' . $file . '::' . $text);

            if (array_key_exists($hash, $errors[$type])) {
                $errors[$type][$hash]['hits'] += 1;
            } else {
                $errors[$type][$hash] = [
                    'datetime'    => $datetime,
                    'line'        => $line,
                    'file'        => $file,
                    'type'        => $type,
                    'description' => [
                        'text' => $text,
                        'stack_trace' => DLV_LogModel::dlv_get_stack_trace_for_row($row),
                    ],
                    'hits' => 1,
                ];
            }
            $new_lines += 1;
        }

        return $new_lines ? $errors : null;
    }

    public static function dlv_send_logs_handler($event)
    {
        $options = get_option($event);

        if (!$options) {
            error_log('Options not found for event ' . $event);
            wp_die();
        }

        if (!array_key_exists('notifications_email', $options)) {
            error_log('Notification email not found in options for event ' . $event);
            wp_die();
        }

        if (!array_key_exists('notifications_email_recurrence', $options)) {
            error_log('Notification email recurrence not found in options for event ' . $event);
            wp_die();
        }

        $notification_email = $options['notifications_email'];
        $recurrence = $options['notifications_email_recurrence'];
        $errors = self::dlv_parse_critical_log_errors($recurrence);

        if ($notification_email && $errors) {
            dlv_send_log_viewer_email(
                $notification_email,
                'Debig Log Viewer: Monitoring detected some problems on the website',
                realpath(__DIR__) . '/../templates/email/log_viewer.tpl',
                [
                    'website' => get_site_url(),
                    'errors' => $errors,
                ]
            );
        }
    }

    public static function dlv_change_log_notifications_status()
    {
        verify_nonce($_POST);

        $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : null;
        if ($status) {
            $config_editor = new WPConfigTransformer(DLV_Constants::get_wp_config_path());

            if (!WP_DEBUG) {
                $config_editor->update('constant', 'WP_DEBUG', '1');
            }

            if (!WP_DEBUG_LOG) {
                $config_editor->update('constant', 'WP_DEBUG_LOG', '1');
            }
        }

        if (!DLV_LogModel::dlv_is_log_file_exists()) {
            echo wp_json_encode([
                'success' => false,
                'error' => __('Unable to set Email notifications as the log file does not exist.', 'debug-log-viewer')
            ]);
            wp_die();
        }

        self::dlv_change_notifications_status();
    }

    public static function dlv_log_viewer_deactivate()
    {
        $notificator = new DLV_Notificator(new self());

        if ($notificator->dlv_is_notification_enabled()) {
            self::dlv_delete_wp_schedule_event($notificator->dlv_build_unique_event_name());
        }

        wp_unschedule_hook(self::SCHEDULE_MAIL_SEND);
        delete_option(DLV_DEBUG_LOG_LAST_FILESIZE);
    }

    public static function dlv_live_update()
    {
        $script_execution_time = 70;

        set_time_limit($script_execution_time);
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('Accept: application/json');

        for ($counter = 0; $counter < $script_execution_time - 10; $counter += 5) {
            clearstatcache();

            $filesize = DLV_LogModel::dlv_get_log_filesize(['raw' => true]);
            $last_filesize = (int) get_option(DLV_DEBUG_LOG_LAST_FILESIZE);

            if ($filesize !== $last_filesize) {

                update_option(DLV_DEBUG_LOG_LAST_FILESIZE, $filesize);

                $fields = [
                    'id'    => $filesize,
                    'event' => 'updates',
                    'data'  => wp_json_encode(['updated' => true]),
                    'retry' => 5000,
                ];

                foreach ($fields as $field => $value) {
                    echo esc_html("$field: $value" . PHP_EOL);
                }

                echo PHP_EOL;
            }

            while (ob_get_level() > 0) {
                ob_end_flush();
            }
            flush();

            sleep(5);
        };
    }
}
