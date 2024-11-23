<?php

class DBG_LV_Phrases
{
    private static $domain = 'debug-log-viewer';

    private static $phrases = [];

    // Initialize the phrases
    private static function init()
    {
        if (empty(self::$phrases)) {
            self::$phrases = [
                'loading_in_process' => __('Loading...', self::$domain),
                'search' => __('Search', self::$domain),
                'call_stack' => __('Call stack', self::$domain),
                'debug_mode' => __('Debug mode:', self::$domain),
                'request_error' => __('Request error:', self::$domain),
                'debug_log_scripts' => __('Debug log script:', self::$domain),
                'logging_enabled_successfully' => __('Logging enabled successfully', self::$domain),
                'debug_scripts' => __('Debug scripts:', self::$domain),
                'display_errors' => __('Display errors:', self::$domain),
                'flush_log_confirmation' => __('Are you sure? After flushing the log, this action can\'t be undone', self::$domain),
                'log_was_cleared' => __('Log was cleared', self::$domain),
                'email_is_not_specified' => __('Email is not specified', self::$domain),
                'request_error' => __('Request error', self::$domain),
                'notifications_disabled' => __('Notifications disabled', self::$domain),
                'disable' => __('Disable', self::$domain),
                'enable' => __('Enable', self::$domain),
                'notifications_enabled' => __('Notifications enabled', self::$domain),
            ];
        }
    }

    public static function getAllPhrases()
    {
        self::init(); // Ensure phrases are initialized before returning
        return self::$phrases;
    }
}
