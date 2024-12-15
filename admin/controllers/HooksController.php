<?php

if (!defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}

class DBG_LV_HooksController
{
    public static function dbg_lv_init()
    {
        $dbg_lv_index_file = plugin_dir_path(__DIR__) . 'debug-log-viewer.php';
        $log_controller = new DBG_LV_LogController();

        // Include styles and scripts in Debug Log Viewer plugin's pages only
        add_action('admin_enqueue_scripts',                                 ['DBG_LV_HooksController', 'dbg_lv_admin_assets_enqueue']);

        register_deactivation_hook($dbg_lv_index_file,                      ['DBG_LV_ServiceController', 'dbg_lv_deactivation_events']);
        register_uninstall_hook($dbg_lv_index_file,                         ['DBG_LV_ServiceController', 'dbg_lv_uninstall_events']);
        add_action('wp_ajax_dbg_lv_get_log_data',                           ['DBG_LV_LogController', 'dbg_lv_get_log_data']);

        add_action('wp_ajax_dbg_lv_log_viewer_clear_log',                   ['DBG_LV_LogController', 'dbg_lv_clear_log']);
        add_action('wp_ajax_dbg_lv_log_viewer_download_log',                ['DBG_LV_LogController', 'dbg_lv_download_log']);
        add_action('wp_ajax_dbg_lv_change_log_viewer_notifications_status', ['DBG_LV_LogController', 'dbg_lv_change_log_notifications_status']);
        add_action('wp_ajax_dbg_lv_get_current_user_email',                 ['DBG_LV_LogController', 'dbg_lv_get_current_user_email']);
        add_action('wp_ajax_dbg_lv_log_viewer_live_update',                 ['DBG_LV_LogController', 'dbg_lv_live_update']);
        add_action('wp_ajax_dbg_lv_change_logs_update_mode',                ['DBG_LV_LogController', 'dbg_lv_change_logs_update_mode']);

        add_action('wp_ajax_dbg_lv_log_viewer_enable_logging', function () use ($log_controller) {
            $log_controller->dbg_lv_log_viewer_enable_logging();
        });

        add_action('wp_ajax_dbg_lv_toggle_debug_mode', function () use ($log_controller) {
            $log_controller->dbg_lv_toggle_debug_mode();
        });
        add_action('wp_ajax_dbg_lv_toggle_debug_scripts', function () use ($log_controller) {
            $log_controller->dbg_lv_toggle_debug_scripts();
        });
        add_action('wp_ajax_dbg_lv_toggle_debug_log_scripts',  function () use ($log_controller) {
            $log_controller->dbg_lv_toggle_debug_log_scripts();
        });
        add_action('wp_ajax_dbg_lv_toggle_display_errors',  function () use ($log_controller) {
            $log_controller->dbg_lv_toggle_display_errors();
        });

        add_action(DBG_LV_LogController::SCHEDULE_MAIL_SEND, ['DBG_LV_LogController', 'dbg_lv_send_logs_handler'], 10, 1);

        add_action('admin_init', function () {
            if (DBG_LV_ReviewController::dbg_lv_is_review_delaying_expired()) {
                add_action('admin_notices', ['DBG_LV_ReviewController', 'dbg_lv_ask_to_leave_review_handler']);
            }
        });
        add_action('admin_init', ['DBG_LV_ReviewController', 'dbg_lv_review_handler']);
    }

    public static function dbg_lv_admin_assets_enqueue($hook_suffix)
    {
        // echo $hook_suffix;
        // Include styles and scripts in Debug Log Viewer plugin page only

        if (strpos($hook_suffix, 'debug-log-viewer')  !== false) {
            wp_enqueue_script('dbg_lv_bootstrap_js',              plugins_url('../public/assets/vendor/js/bootstrap.bundle.min.js', __DIR__));
            wp_enqueue_script('dbg_lv_bootstrap_switch_js',       plugins_url('../public/assets/vendor/js/bootstrap-switch.min.js', __DIR__, array('jquery')));
            wp_enqueue_script('dbg_lv_datatables_js',             plugins_url('../public/assets/vendor/js/jquery.dataTables.min.js', __DIR__), array('jquery'));
            wp_enqueue_script('dbg_lv_app_js',                    plugins_url('../public/assets/js/app.js', __DIR__), array('jquery'));
            wp_enqueue_script('dbg_lv_font-awesome_js',           plugins_url('../public/assets/vendor/js/font-awesome.js', __DIR__));
            // DataTables buttons
            wp_enqueue_script('dbg_lv_datatables_buttons_js',     plugins_url('../public/assets/vendor/js/dataTables.buttons.min.js', __DIR__));
            wp_enqueue_script('dbg_lv_zip_js',                    plugins_url('../public/assets/vendor/js/jszip.min.js', __DIR__));
            wp_enqueue_script('dbg_lv_buttons_html5_js',          plugins_url('../public/assets/vendor/js/buttons.html5.min.js', __DIR__));
            wp_enqueue_script('dbg_lv_buttons_print_js',          plugins_url('../public/assets/vendor/js/buttons.print.min.js', __DIR__));
            wp_enqueue_script('dbg_lv_buttons_colvis_js',         plugins_url('../public/assets/vendor/js/buttons.colVis.min.js', __DIR__));

            wp_localize_script('dbg_lv_app_js', 'dbg_lv_backend_data', [
                'ajax_nonce'           => wp_create_nonce('ajax_nonce'),
                'log_updates_mode'     => get_option(DBG_LV_LogModel::DBG_LV_LOG_UPDATES_MODE_OPTION_NAME),
                'log_updates_interval' => DBG_LV_LOG_UPDATES_INTERVAL,
            ]);
            wp_enqueue_style('dbg_lv_bootstrap_css',              plugins_url('../public/assets/vendor/css/bootstrap.min.css', __DIR__));
            wp_enqueue_style('dbg_lv_now-ui_css',                 plugins_url('../public/assets/vendor/css/now-ui-kit.min.css', __DIR__), ['dbg_lv_bootstrap_css']);
            wp_enqueue_style('dbg_lv_datatables_css',             plugins_url('../public/assets/vendor/css/jquery.dataTables.min.css', __DIR__));
            wp_enqueue_style('dbg_lv_datatables_buttons_css',     plugins_url('../public/assets/vendor/css/buttons.dataTables.min.css', __DIR__));
            wp_enqueue_style('dbg_lv_style',                      plugins_url('../public/assets/css/style.css', __DIR__));
        }
    }
}
