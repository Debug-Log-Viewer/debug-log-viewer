<?php


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class DBG_LV_LiveUpdatesController
{

    public function applyHeaders(): void
    {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no'); // Nginx: unbuffered responses suitable for Comet and HTTP streaming applications

    }

    public function clearDebugLogFileStat(): void
    {
        clearstatcache();
    }

    public function getUpdates($updates): string
    {
        $formatted = array_map(function ($row) {
            if (empty($row)) {
                return;
            }
            return [
                'datetime'    => DBG_LV_LogModel::dbg_lv_get_datetime_from_row($row),
                'line'        => DBG_LV_LogModel::dbg_lv_get_line_from_log_row($row),
                'file'        => DBG_LV_LogModel::dbg_lv_get_file_from_log_row($row),
                'type'        => DBG_LV_LogModel::dbg_lv_get_type_from_row($row),
                'description' => [
                    'text' => DBG_LV_LogModel::dbg_lv_get_description_from_row($row),
                    'stack_trace' => DBG_LV_LogModel::dbg_lv_get_stack_trace_for_row($row)
                ]
            ];
        }, $updates['data']);

        return json_encode(['action' => $updates['action'], 'data' => $formatted]);
    }
}
