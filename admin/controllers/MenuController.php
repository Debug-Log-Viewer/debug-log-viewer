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
            $name = __('Debug Log Viewer', 'debug-log-viewer');
            $slug = 'debug-log-viewer';
            $handler =  ['DBG_LV_LogController', 'dbg_lv_render_view'];
            $icon = plugin_dir_url(__FILE__) . '/../../../public/assets/img/logo-grayscale.svg';
            add_menu_page($name, $name, $role, $slug, $handler, $icon);
        }
    }
}
