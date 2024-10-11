<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
class DLV_MenuController
{
    public static function dlv_init()
    {
        add_action('admin_menu', 'dlv_main_menu', 9, 0);

        function dlv_main_menu()
        {
            $role = 'edit_pages';
            add_menu_page(__('Debug Log Viewer', 'debug-log-viewer'), __('Debug Log Viewer', 'debug-log-viewer'), $role, 'debug-log-viewer', ['DLV_LogController', 'dlv_render_view'],  plugin_dir_url(__FILE__) . '/../../assets/img/logo.svg');
        }
    }
}
