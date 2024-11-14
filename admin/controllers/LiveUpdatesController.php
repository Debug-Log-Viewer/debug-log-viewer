<?php


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class DBG_LV_LiveUpdatesController
{

    public function applyHeaders(): void
    {
        // Remove any previously set headers to prevent conflicts.
        header_remove();

        // SSE headers.
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no'); // Disable buffering for Nginx
    }

    public function setExecutionTimeLimit()
    {
        $executionTimeLimit = 60;
        if (function_exists('set_time_limit')) {
            @set_time_limit($executionTimeLimit);
        }
    }

    public function clearDebugLogFileStat(): void
    {
        clearstatcache();
    }

    public function flushingOutputBuffering(): void
    {
        while (ob_get_level() > 0) {
            ob_end_flush();
        }
        flush();
    }

    public function notifyClientAboutUpdates(): void
    {
        $fields = [
            'id'    => time(), // Use a timestamp as a unique event ID.
            'event' => 'updates',
            'data'  => wp_json_encode(['updated' => true]),
            'retry' => 5000,
        ];
        foreach ($fields as $field => $value) {
            echo esc_html("$field: $value" . PHP_EOL);
        }
        echo PHP_EOL;
    }

    public static function actualizeUserActivityTime($response)
    {
        // Get the current screen
        if (is_admin()) {
            global $pagenow;

            // Check if on a plugin page and get the current plugin's slug
            $page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';
            $plugin_slug = 'debug-log-viewer';

            // Check if current page matches the plugin's slug
            if ($page === $plugin_slug && $pagenow === 'admin.php') {
                // Update time when user was active
                $_SESSION['user_active'] = time();
            } else {
                unset($_SESSION['user_active']);
            }
        }

        return $response;
    }
}
