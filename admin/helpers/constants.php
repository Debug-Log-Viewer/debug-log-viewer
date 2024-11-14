<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $wpdb;

define('DBG_LV_LOG_FILE_LIMIT', 10 * 1024 * 1024);
define('DBG_LV_DEBUG_LOG_LAST_FILESIZE', 'dbg_lv_dbg_log_last_filesize');
define('DBG_LV_LIVE_UPDATE_INTERVAL', 5);

class DBG_LV_Constants
{

    public static function get_wp_config_path()
    {
        // Starting from the current directory
        $dir = dirname(__FILE__);

        // Traverse up to 10 levels to avoid infinite loops
        for ($i = 0; $i < 10; $i++) {
            if (file_exists($dir . '/wp-config.php')) {
                return realpath($dir . '/wp-config.php');
            }
            // Move up one directory level
            $dir = dirname($dir);
        }
        return 'wp-config.php not found!';
    }

    const WEEK_IN_SECONDS = 604800;
    const TWO_MONTH_IN_SECONDS = 5259492;
    const SIX_MONTH_IN_SECONDS = 15778476;
}

class DBG_LV_LogLevelStatuses
{
    const NOTICE = 'Notice';
    const WARNING = 'Warning';
    const FATAL = 'Fatal';
    const DATABASE = 'Database';
    const PARSE = 'Parse';
    const DEPRECATED = 'Deprecated';
}

$DBG_LV_WP_CRON_SCHEDULE_INTERVALS = [
    'hourly'     => new DateInterval('PT1H'),
    'twicedaily' => new DateInterval('PT12H'),
    'daily'      => new DateInterval('P1D'),
    'weekly'     => new DateInterval('P7D'),
];

$DBG_LV_LOG_VIEWER_EMAIL_LEVELS = [
    DBG_LV_LogLevelStatuses::DATABASE,
    DBG_LV_LogLevelStatuses::FATAL,
    DBG_LV_LogLevelStatuses::PARSE,
    DBG_LV_LogLevelStatuses::DEPRECATED,
];
