<?php

class DBG_LV_Phrases
{
    public static $domain = 'debug-log-viewer';
    private static $phrases = [];

    // Initialize the phrases
    private static function init()
    {
        if (empty(self::$phrases)) {
            self::$phrases = [
                'loading_in_process' => __('Loading...', self::$domain),
                'search' => __('Search', self::$domain),
                'call_stack' => __('Call Stack', self::$domain),
                'debug_mode' => __('Debug Mode:', self::$domain),
                'request_error' => __('Request Error:', self::$domain),
                'debug_log_scripts' => __('Debug Log Scripts:', self::$domain),
                'logging_enabled_successfully' => __('Logging enabled successfully.', self::$domain),
                'debug_scripts' => __('Debug Scripts:', self::$domain),
                'display_errors' => __('Display Errors:', self::$domain),
                'flush_log_confirmation' => __('Are you sure? This action cannot be undone after flushing the log.', self::$domain),
                'log_was_cleared' => __('The log was cleared.', self::$domain),
                'log_was_refreshed' => __('The log was refreshed.', self::$domain),
                'email_is_not_specified' => __('Email is not specified.', self::$domain),
                'request_error' => __('Request Error.', self::$domain), // Duplicate, consider removing one
                'notifications_disabled' => __('Notifications have been disabled.', self::$domain),
                'disable' => __('Disable', self::$domain),
                'enable' => __('Enable', self::$domain),
                'notifications_enabled' => __('Notifications have been enabled.', self::$domain),
                'columns' => __('Columns', self::$domain),
            ];
        }
    }

    public static function getAllPhrases()
    {
        self::init(); // Ensure phrases are initialized before returning
        return self::$phrases;
    }
}
