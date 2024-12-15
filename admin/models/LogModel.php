<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class DBG_LV_LogModel
{
    const DBG_LV_LAST_POSITION_OPTION_NAME = 'dbg_lv_log_last_position';

    public static function dbg_lv_is_log_file_exists()
    {
        $path = DBG_LV_LogController::dbg_lv_get_debug_file_path();

        return $path && file_exists($path) && is_file($path);
    }

    public static function dbg_lv_get_actual_log_limit()
    {
        return defined('DBG_LV_USER_DEFINED_LOG_FILE_LIMIT') ? constant('DBG_LV_USER_DEFINED_LOG_FILE_LIMIT') : DBG_LV_LOG_FILE_LIMIT;
    }

    public static function dbg_lv_is_debug_log_too_big()
    {
        return self::dbg_lv_get_log_filesize(['raw' => true]) > self::dbg_lv_get_actual_log_limit();
    }

    public static function dbg_lv_get_log_content($filename)
    {
        if (self::dbg_lv_is_debug_log_too_big()) {
            $actual_limit = self::dbg_lv_get_actual_log_limit();
            $file_handle = fopen($filename, 'r');
            fseek($file_handle, -$actual_limit, SEEK_END);
            $content = fread($file_handle, $actual_limit);
            fclose($file_handle);
            return $content;
        } else {
            return file_get_contents($filename);
        }
    }

    public static function getNewLogEntries()
    {
        $filename = DBG_LV_LogController::dbg_lv_get_debug_file_path();
        // Handle missing file
        if (!file_exists($filename)) {
            return self::resetLog('File does not exist');
        }

        $file_size = filesize($filename);
        // Handle empty file
        if ($file_size === 0) {
            return self::resetLog();
        }

        $file_handle = fopen($filename, 'r');
        if (!$file_handle) {
            return self::resetLog();
        }
        $last_position = get_option(self::DBG_LV_LAST_POSITION_OPTION_NAME, 0);

        // Handle new or truncated content
        if ($last_position === 0 || $file_size > $last_position) {
            fseek($file_handle, $last_position, SEEK_SET);
            $content = fread($file_handle, $file_size - $last_position);
            $new_position = ftell($file_handle);
            fclose($file_handle);

            update_option(self::DBG_LV_LAST_POSITION_OPTION_NAME, $new_position);

            return [
                'action' => [],
                'data' => self::splitLogToRows($content),
            ];
        }

        // Handle file truncation
        if ($file_size < $last_position) {
            fclose($file_handle);
            return self::resetLog();
        }

        fclose($file_handle);
    }

    private static function resetLog()
    {
        update_option(self::DBG_LV_LAST_POSITION_OPTION_NAME, 0);
        return ['action' => 'clear', 'data' => []];
    }

    public static function parseWholeLogFile()
    {
        $path = DBG_LV_LogController::dbg_lv_get_debug_file_path();

        if (!file_exists($path) || !is_file($path)) {
            return false;
        }

        $content = self::dbg_lv_get_log_content($path);
        return self::splitLogToRows($content);
    }

    private static function splitLogToRows($content)
    {
        $pattern = '/\[[^\]]+\].*? on line \d+/s';
        $count = preg_match_all($pattern, $content, $matches);

        if (!$count) {
            return [];
        }

        return $matches[0];
    }

    public static function dbg_lv_get_datetime_from_row($row)
    {
        preg_match_all('/\[(.*?)\]/m', $row, $matches, PREG_SET_ORDER, 0);
        return isset($matches[0][1]) ? $matches[0][1] : __('N/A', 'debug-log-viewer');
    }

    public static function dbg_lv_get_line_from_log_row($row)
    {
        preg_match_all('/(on line |php:)(\d{1,})/m', $row, $matches, PREG_SET_ORDER, 0);
        return isset($matches[0][2]) ? $matches[0][2] : __('N/A', 'debug-log-viewer');
    }

    public static function dbg_lv_get_file_from_log_row($row)
    {
        preg_match_all('/ in ' . preg_quote(dbg_lv_get_document_root(), '/') . '(.*?)( on line |:)\d{1,}/m', $row, $matches, PREG_SET_ORDER, 0);
        return isset($matches[0][1]) ? $matches[0][1] : __('N/A', 'debug-log-viewer');
    }

    public static function dbg_lv_get_type_from_row($row)
    {
        if (strpos($row, 'PHP Notice:') !== false) {
            return DBG_LV_LogLevelStatuses::NOTICE;
        } elseif (strpos($row, 'PHP Warning:') !== false) {
            return DBG_LV_LogLevelStatuses::WARNING;
        } elseif (strpos($row, 'PHP Fatal error:') !== false) {
            return DBG_LV_LogLevelStatuses::FATAL;
        } elseif (strpos($row, 'WordPress database error') !== false) {
            return DBG_LV_LogLevelStatuses::DATABASE;
        } elseif (strpos($row, 'PHP Parse error:') !== false) {
            return DBG_LV_LogLevelStatuses::PARSE;
        } elseif (strpos($row, 'PHP Deprecated:') !== false) {
            return DBG_LV_LogLevelStatuses::DEPRECATED;
        }
    }

    public static function dbg_lv_get_stack_trace_for_row($row)
    {
        $re = '/Stack trace:\n(.*?)thrown in/s';
        preg_match_all($re, $row, $matches, PREG_SET_ORDER, 0);
        if (isset($matches[0])) {
            return $matches[0][1];
        }
        return null;
    }

    public static function dbg_lv_get_description_from_row($row)
    {
        if (self::dbg_lv_get_type_from_row($row) === 'Database') {

            $re = '/WordPress database error (.*)/m';
            preg_match_all($re, $row, $matches, PREG_SET_ORDER, 0);
            return isset($matches[0]) && $matches[0][1] ? $matches[0][1] : __('N/A', 'debug-log-viewer');
        }

        $re = '/ (PHP Notice:|PHP Warning:|PHP Fatal error:|PHP Parse error:|PHP Deprecated:)(.*?)(\[ | in |on line)/m';
        preg_match_all($re, $row, $matches, PREG_SET_ORDER, 0);
        return isset($matches[0]) && $matches[0][2] ? $matches[0][2] : __('N/A', 'debug-log-viewer');
    }

    public static function dbg_lv_get_log_filesize($params)
    {
        $with_measure_units = isset($params['with_measure_units']) ? $params['with_measure_units'] : null;
        $raw = isset($params['raw']) ? $params['raw'] : null;

        $debug_filepath = DBG_LV_LogController::dbg_lv_get_debug_file_path();

        if (is_file($debug_filepath) && filesize($debug_filepath)) {
            $filesize_in_bytes = filesize($debug_filepath);
            if ($raw) {
                return $filesize_in_bytes;
            }
            $filesize_in_mb = $filesize_in_bytes / 1024 / 1024;
            return $with_measure_units
                ? round($filesize_in_mb, 2) . ' ' . __('Mb', 'debug-log-viewer')
                : round($filesize_in_mb, 2);
        } else {
            return 0;
        }
    }

    public static function render_log_viewer_errors($errors)
    {
        global $DBG_LV_LOG_VIEWER_EMAIL_LEVELS;

        $body = "" .
            "<tr>" .
            "    <td>#</td>" .
            "    <td>Type</td>" .
            "    <td>Description</td>" .
            "    <td>File</td>" .
            "    <td>Line</td>" .
            "    <td>Hits</td>" .
            "</tr>";

        $row = "" .
            "<tr>" .
            "    <td>%s</td>" .
            "    <td>%s</td>" .
            "    <td>%s</td>" .
            "    <td>%s</td>" .
            "    <td>%s</td>" .
            "    <td>%d</td>" .
            "</tr>";

        $index = 1;
        foreach ($DBG_LV_LOG_VIEWER_EMAIL_LEVELS as $level) {
            foreach ($errors[$level] as $hash => $error) {
                $body .= sprintf($row, $index, $error['type'], $error['description']['text'], $error['file'], $error['line'], $error['hits']);
                $index += 1;
            }
        }

        return $body;
    }
}
