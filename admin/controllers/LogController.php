<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

require_once realpath(__DIR__) . '/../../vendor/autoload.php';
require_once realpath(__DIR__) . '/LiveUpdatesController.php';
require_once realpath(__DIR__) . '/../../admin/models/LogModel.php';
require_once realpath(__DIR__) . '/../../public/views/pages/log.php';
require_once realpath(__DIR__) . '/../../admin/helpers/utils.php';
require_once realpath(__DIR__) . '/ScheduleTrait.php';
require_once realpath(__DIR__) . '/../../admin/services/email.php';

class DBG_LV_LogController
{
    use DBG_LV_ScheduleTrait;

    const SCHEDULE_MAIL_SEND = 'DBG_LV_NOTIFY_LOG_CONTROLLER';

    private $config_editor;

    public function __construct()
    {
        try {
            $this->config_editor = new WPConfigTransformer(DBG_LV_Constants::get_wp_config_path());
        } catch (Exception $error) {
            // Please, make sure permissions and path is correct.
            // The plugin need an access to wp-config.php to manage debugging constants
        }
    }

    public static function dbg_lv_render_view()
    {
        return DBG_LV_LogView::dbg_lv_render_view();
    }

    public static function dbg_lv_get_debug_file_path()
    {
        if (file_exists(WP_CONTENT_DIR . '/debug.log')) {
            return WP_CONTENT_DIR . '/debug.log';
        }

        return '';
        // For those cases when WP_DEBUG_LOG is setted as a path to debug file (overrided default)
        // @todo: return WP_DEBUG_LOG;
    }

    public static function dbg_lv_get_log_data()
    {
        dbg_lv_verify_nonce(isset($_POST['wp_nonce']) ? sanitize_text_field(wp_unslash($_POST['wp_nonce'])) : '');

        $draw = isset($_POST['draw']) ? (int) sanitize_text_field(wp_unslash($_POST['draw'])) : 0;
        $start = isset($_POST['start']) ? (int) sanitize_text_field(wp_unslash($_POST['start'])) : 1;
        $length = isset($_POST['length']) ? (int) sanitize_text_field(wp_unslash($_POST['length'])) : 25;
        $search_value = isset($_POST['search']['value']) ? sanitize_text_field(wp_unslash($_POST['search']['value'])) : null;



        $storage = [];

        $rows = array_reverse(DBG_LV_LogModel::dbg_lv_parse_log_file());

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
                'datetime'    => DBG_LV_LogModel::dbg_lv_get_datetime_from_row($row),
                'line'        => DBG_LV_LogModel::dbg_lv_get_line_from_log_row($row),
                'file'        => DBG_LV_LogModel::dbg_lv_get_file_from_log_row($row),
                'type'        => DBG_LV_LogModel::dbg_lv_get_type_from_row($row),
                'description' => [
                    'text' => DBG_LV_LogModel::dbg_lv_get_description_from_row($row),
                    'stack_trace' => DBG_LV_LogModel::dbg_lv_get_stack_trace_for_row($row)
                ]
            ];

            $rows_count++;
        }

        if ($search_value) {
            $search_string = trim(strtolower($search_value));
            $data = [];
            foreach ($storage as $index => $row) {
                $results = dbg_lv_find_string($row, $search_string);

                if ($results) {
                    $data[] = $storage[$index];
                }
            }

            $search_results_length = count($data);

            $storage = array_slice($data, $start, $length);
        } else {
            $storage = array_slice($storage, $start, $length);
        }

        $filesize = DBG_LV_LogModel::dbg_lv_get_log_filesize(['with_measure_units' => true]);

        if (DBG_LV_LogModel::dbg_lv_is_debug_log_too_big()) {
            $template = __("The debug log file is excessively large (%1\$s). We only parse the most recent %2\$d lines, starting from the date %3\$s.", 'debug-log-viewer');
            $info = sprintf($template, $filesize, $rows_count, DBG_LV_LogModel::dbg_lv_get_datetime_from_row($rows[0]));
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

    public function dbg_lv_log_viewer_enable_logging()
    {
        dbg_lv_verify_nonce(isset($_POST['wp_nonce']) ? sanitize_text_field(wp_unslash($_POST['wp_nonce'])) : '');

        try {

            $path = WP_CONTENT_DIR . '/debug.log';
            if (!is_file($path) || !file_exists($path)) {
                // Create debug.log if missing

                $message = 'This is a demo entry. Debugging is now enabled. If any notices, warnings, or errors occur on your site, they will appear here. Remember to refresh the table to view the latest entries';
                $demo_string = "[" . gmdate('d-M-Y H:i:s T') . "] PHP Notice: <b>" . $message  . "</b>  in " . dbg_lv_get_document_root() . "/example.php on line 0\n";
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

    public function dbg_lv_toggle_debug_mode()
    {
        dbg_lv_verify_nonce(isset($_POST['wp_nonce']) ? sanitize_text_field(wp_unslash($_POST['wp_nonce'])) : '');

        $state = $this->dbg_lv_prepare_state();

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

    public function dbg_lv_toggle_debug_scripts()
    {
        dbg_lv_verify_nonce(isset($_POST['wp_nonce']) ? sanitize_text_field(wp_unslash($_POST['wp_nonce'])) : '');
        $state = $this->dbg_lv_prepare_state();

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

    public function dbg_lv_toggle_debug_log_scripts()
    {
        dbg_lv_verify_nonce(isset($_POST['wp_nonce']) ? sanitize_text_field(wp_unslash($_POST['wp_nonce'])) : '');
        $state = $this->dbg_lv_prepare_state();

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

    public function dbg_lv_toggle_display_errors()
    {
        dbg_lv_verify_nonce(isset($_POST['wp_nonce']) ? sanitize_text_field(wp_unslash($_POST['wp_nonce'])) : '');
        $state = $this->dbg_lv_prepare_state();

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

    public static function dbg_lv_clear_log()
    {
        dbg_lv_verify_nonce(isset($_POST['wp_nonce']) ? sanitize_text_field(wp_unslash($_POST['wp_nonce'])) : '');

        try {
            $debug_log_path = DBG_LV_LogController::dbg_lv_get_debug_file_path();

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

    public static function dbg_lv_download_log()
    {
        dbg_lv_verify_nonce(isset($_POST['wp_nonce']) ? sanitize_text_field(wp_unslash($_POST['wp_nonce'])) : '');

        try {
            $debug_log_path = DBG_LV_LogController::dbg_lv_get_debug_file_path();

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

    public static function dbg_lv_get_current_user_email()
    {
        dbg_lv_verify_nonce(isset($_POST['wp_nonce']) ? sanitize_text_field(wp_unslash($_POST['wp_nonce'])) : '');

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

    public function dbg_lv_prepare_state()
    {
        dbg_lv_verify_nonce(isset($_POST['wp_nonce']) ? sanitize_text_field(wp_unslash($_POST['wp_nonce'])) : '');

        if (!isset($_POST["state"])) {
            throw new \Exception('Empty state passed');
        }

        $state = sanitize_text_field(wp_unslash($_POST["state"]));
        switch ((int) $state) {
            case 0:
            case 1:
                return (string) $state;
            default:
                throw new \Exception('Incorrect state value passed');
        }
    }

    public static function dbg_lv_parse_critical_log_errors($recurrence)
    {
        global $DBG_LV_WP_CRON_SCHEDULE_INTERVALS;
        global $DBG_LV_LOG_VIEWER_EMAIL_LEVELS;

        $rows = DBG_LV_LogModel::dbg_lv_parse_log_file();

        if (!$rows) {
            return;
        }

        $errors = [];
        foreach ($DBG_LV_LOG_VIEWER_EMAIL_LEVELS as $level) {
            $errors[$level] = [];
        }

        $new_lines = 0;
        foreach ($rows as $row) {
            if (empty($row)) {
                continue;
            }

            $datetime = DBG_LV_LogModel::dbg_lv_get_datetime_from_row($row);
            $datetimeOffset = new DateTime();
            $datetimeOffset->sub($DBG_LV_WP_CRON_SCHEDULE_INTERVALS[$recurrence]);
            $datetimeError = new DateTime();
            $datetimeError->setTimestamp(strtotime($datetime));

            if ($datetimeError < $datetimeOffset) {
                continue;
            }

            $type = DBG_LV_LogModel::dbg_lv_get_type_from_row($row);

            if (!in_array($type, $DBG_LV_LOG_VIEWER_EMAIL_LEVELS)) {
                continue;
            }

            $line = DBG_LV_LogModel::dbg_lv_get_line_from_log_row($row);
            $file = DBG_LV_LogModel::dbg_lv_get_file_from_log_row($row);
            $text = DBG_LV_LogModel::dbg_lv_get_description_from_row($row);
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
                        'stack_trace' => DBG_LV_LogModel::dbg_lv_get_stack_trace_for_row($row),
                    ],
                    'hits' => 1,
                ];
            }
            $new_lines += 1;
        }

        return $new_lines ? $errors : null;
    }

    public static function dbg_lv_send_logs_handler($event)
    {
        $options = get_option($event);

        if (!$options) {
            error_log('Options not found for event ' . $event);
            wp_die();
        }

        if (!array_key_exists('dbg_lv_notifications_email', $options)) {
            error_log('Notification email not found in options for event ' . $event);
            wp_die();
        }

        if (!array_key_exists('dbg_lv_notifications_email_recurrence', $options)) {
            error_log('Notification email recurrence not found in options for event ' . $event);
            wp_die();
        }

        $notification_email = $options['dbg_lv_notifications_email'];
        $recurrence = $options['dbg_lv_notifications_email_recurrence'];
        $errors = self::dbg_lv_parse_critical_log_errors($recurrence);

        if ($notification_email && $errors) {
            dbg_lv_send_log_viewer_email(
                $notification_email,
                'Debug Log Viewer: Monitoring detected some problems on the website',
                realpath(__DIR__) . '/../templates/email/log_viewer.tpl',
                [
                    'website' => get_site_url(),
                    'errors' => $errors,
                ]
            );
        }
    }

    public static function dbg_lv_change_log_notifications_status()
    {
        dbg_lv_verify_nonce(isset($_POST['wp_nonce']) ? sanitize_text_field(wp_unslash($_POST['wp_nonce'])) : '');

        $status = isset($_POST['status']) ? sanitize_text_field(wp_unslash($_POST['status'])) : null;
        if ($status) {
            $config_editor = new WPConfigTransformer(DBG_LV_Constants::get_wp_config_path());

            if (!WP_DEBUG) {
                $config_editor->update('constant', 'WP_DEBUG', '1');
            }

            if (!WP_DEBUG_LOG) {
                $config_editor->update('constant', 'WP_DEBUG_LOG', '1');
            }
        }

        if (!DBG_LV_LogModel::dbg_lv_is_log_file_exists()) {
            echo wp_json_encode([
                'success' => false,
                'error' => __('Unable to set Email notifications as the log file does not exist.', 'debug-log-viewer')
            ]);
            wp_die();
        }

        self::dbg_lv_change_notifications_status();
    }

    public static function dbg_lv_log_viewer_deactivate()
    {
        $notificator = new DBG_LV_Notificator(new self());

        if ($notificator->dbg_lv_is_notification_enabled()) {
            self::dbg_lv_delete_wp_schedule_event($notificator->dbg_lv_build_unique_event_name());
        }

        wp_unschedule_hook(self::SCHEDULE_MAIL_SEND);
        delete_option(DBG_LV_DEBUG_LOG_LAST_FILESIZE);
    }

    public static function dbg_lv_live_update()
    {
        $liveUpdates = new DBG_LV_LiveUpdatesController();
        $liveUpdates->applyHeaders();
        $liveUpdates->setExecutionTimeLimit();

        for ($i = 0; $i < DBG_LV_ITERATIONS_PER_SESSION; $i++) {
            $liveUpdates->clearDebugLogFileStat();
            // Get current log file size
            $current_filesize = DBG_LV_LogModel::dbg_lv_get_log_filesize(['raw' => true]);
            $latest_filesize = (int) get_option(DBG_LV_DEBUG_LOG_LAST_FILESIZE);

            // Only send an update if the filesize has changed.
            if ($current_filesize !== $latest_filesize) {
                update_option(DBG_LV_DEBUG_LOG_LAST_FILESIZE, $current_filesize);
                $liveUpdates->notifyClientAboutUpdates();
            }
            $liveUpdates->flushingOutputBuffering();
            // Sleep for 5 seconds before the next update.
            sleep(DBG_LV_LIVE_UPDATE_INTERVAL);
        }
        // Exit over iterations limit reached.
        // After this auto-reconnect will be triggered om the front.
        exit; 
    }
}
