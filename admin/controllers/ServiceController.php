<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class DBG_LV_ServiceController
{

    public static function dbg_lv_deactivation_events()
    {
        DBG_LV_LogController::dbg_lv_log_viewer_deactivate();
    }

    public static function dbg_lv_uninstall_events()
    {
        DBG_LV_LogController::dbg_lv_log_viewer_deactivate();
    }
}
