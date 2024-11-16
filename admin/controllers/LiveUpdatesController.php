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
        if (function_exists('set_time_limit')) {
            set_time_limit(DBG_LV_LIVE_UPDATE_INTERVAL * DBG_LV_ITERATIONS_PER_SESSION);
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
}
