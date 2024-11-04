<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class DBG_LV_MenuController
{
    public static function dbg_lv_init()
    {
        add_action('admin_menu', 'dbg_lv_main_menu', 9, 0);

        function dbg_lv_main_menu()
        {
            $role = 'edit_pages';
            add_menu_page(__('Debug Log Viewer', 'debug-log-viewer'), __('Debug Log Viewer', 'debug-log-viewer'), $role, 'debug-log-viewer', ['DBG_LV_LogController', 'dbg_lv_render_view'],  plugin_dir_url(__FILE__) . '/../../../public/assets/img/logo.svg');
        }
    }
}
