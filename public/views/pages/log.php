<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class DBG_LV_LogView
{
    public static function dbg_lv_render_view()
    {
        $path = DBG_LV_LogController::dbg_lv_get_debug_file_path(); ?>
        <div class="container dbg_lv-log-viewer">
            <div class="row main-content">
                <div class="main-table content-wrapper">
                    <?php if ($path && file_exists($path) && is_file($path)) {
                        require_once realpath(__DIR__) . '/../components/control-bar.tpl.php';
                    } ?>

                    <?php
                    if (!DBG_LV_LogModel::dbg_lv_is_log_file_exists()) {
                        require_once realpath(__DIR__) . '/../components/log-missing-debug-file.tpl.php';
                    } else {
                        require_once realpath(__DIR__) . '/../components/log-table.tpl.php';
                    } ?>
                </div>

                <div class="sidebar sidebar-wrapper">
                    <div class="close-icon"><i class="fa fa-times"></i></div>
                    <?php require_once realpath(__DIR__) . '/../components/sidebar/togglers.tpl.php'; ?>
                    <?php require_once realpath(__DIR__) . '/../components/sidebar/notification.tpl.php'; ?>
                    <?php require_once realpath(__DIR__) . '/../components/sidebar/settings.tpl.php'; ?>
                </div>
            </div>
        </div>
        
        <?php require_once realpath(__DIR__) . '/../components/toast.tpl.php'; ?>
<?php
    }
}
