<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $wpdb;

define('DLV_LOG_FILE_LIMIT', 10 * 1024 * 1024);
define('DLV_DEBUG_LOG_LAST_FILESIZE', 'dlv_dbg_log_last_filesize');

class DLV_Constants
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

class DLV_LogLevelStatuses
{
    const NOTICE = 'Notice';
    const WARNING = 'Warning';
    const FATAL = 'Fatal';
    const DATABASE = 'Database';
    const PARSE = 'Parse';
    const DEPRECATED = 'Deprecated';
}

$WP_CRON_SCHEDULE_INTERVALS = [
    'hourly'     => new DateInterval('PT1H'),
    'twicedaily' => new DateInterval('PT12H'),
    'daily'      => new DateInterval('P1D'),
    'weekly'     => new DateInterval('P7D'),
];

$DLV_LOG_VIEWER_EMAIL_LEVELS = [
    DLV_LogLevelStatuses::DATABASE,
    DLV_LogLevelStatuses::FATAL,
    DLV_LogLevelStatuses::PARSE,
    DLV_LogLevelStatuses::DEPRECATED,
];
