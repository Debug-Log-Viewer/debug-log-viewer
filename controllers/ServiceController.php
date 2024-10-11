<?php

class DLV_ServiceController
{

    public static function dlv_deactivation_events()
    {
        DLV_LogController::dlv_log_viewer_deactivate();
    }

    public static function dlv_uninstall_events()
    {
        DLV_LogController::dlv_log_viewer_deactivate();
    }
}
