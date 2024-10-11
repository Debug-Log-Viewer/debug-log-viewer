<?php

if (!defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}

class DLV_HooksController
{
    public static function dlv_init()
    {
        $dlv_index_file = plugin_dir_path(__DIR__) . 'index.php';
        $log_controller = new DLV_LogController();

        // Include styles and scripts in Debug Log Viewer plugin's pages only
        add_action('admin_enqueue_scripts',                              ['DLV_HooksController', 'dlv_admin_assets_enqueue']);

        register_deactivation_hook($dlv_index_file,                     ['DLV_ServiceController', 'dlv_deactivation_events']);
        register_uninstall_hook($dlv_index_file,                        ['DLV_ServiceController', 'dlv_uninstall_events']);
        add_action('wp_ajax_dlv_get_log_data',                          ['DLV_LogController', 'dlv_get_log_data']);

        add_action('wp_ajax_dlv_log_viewer_clear_log',                   ['DLV_LogController', 'dlv_clear_log']);
        add_action('wp_ajax_dlv_log_viewer_download_log',                ['DLV_LogController', 'dlv_download_log']);
        add_action('wp_ajax_dlv_change_log_viewer_notifications_status', ['DLV_LogController', 'dlv_change_log_notifications_status']);
        add_action('wp_ajax_dlv_get_current_user_email',               ['DLV_LogController', 'dlv_get_current_user_email']);
        add_action('wp_ajax_dlv_log_viewer_live_update',                 ['DLV_LogController', 'dlv_live_update']);

        add_action('wp_ajax_dlv_log_viewer_enable_logging', function () use ($log_controller) {
            $log_controller->dlv_log_viewer_enable_logging();
        });

        add_action('wp_ajax_dlv_toggle_debug_mode', function () use ($log_controller) {
            $log_controller->dlv_toggle_debug_mode();
        });
        add_action('wp_ajax_dlv_toggle_debug_scripts', function () use ($log_controller) {
            $log_controller->dlv_toggle_debug_scripts();
        });
        add_action('wp_ajax_dlv_toggle_debug_log_scripts',  function () use ($log_controller) {
            $log_controller->dlv_toggle_debug_log_scripts();
        });
        add_action('wp_ajax_dlv_toggle_display_errors',  function () use ($log_controller) {
            $log_controller->dlv_toggle_display_errors();
        });

        add_action(DLV_LogController::SCHEDULE_MAIL_SEND, ['DLV_LogController', 'dlv_send_logs_handler'], 10, 1);

        add_action('admin_init', function () {
            if (DLV_ReviewController::dlv_is_review_delaying_expired()) {
                add_action('admin_notices', ['DLV_ReviewController', 'dlv_ask_to_leave_review_handler']);
            }
        });
        add_action('admin_init', ['DLV_ReviewController', 'dlv_review_handler']);
    }

    public static function dlv_admin_assets_enqueue($hook_suffix)
    {
        // echo $hook_suffix;
        // Include styles and scripts in Debug Log Viewer plugin page only

        if (strpos($hook_suffix, 'debug-log-viewer')  !== false) {


            wp_enqueue_script('dlv_toast_js',                  plugins_url('assets/vendor/js/toast.js', __DIR__), array('jquery'));
            wp_enqueue_script('dlv_bootstrap_js',              plugins_url('assets/vendor/js/bootstrap.bundle.min.js', __DIR__));
            wp_enqueue_script('dlv_bootstrap_switch_js',       plugins_url('assets/vendor/js/bootstrap-switch.min.js', __DIR__, array('jquery')));
            wp_enqueue_script('dlv_datatables_js',             plugins_url('assets/vendor/js/jquery.dataTables.min.js', __DIR__), array('jquery'));
            wp_enqueue_script('dlv_app_js',                    plugins_url('assets/js/app.js', __DIR__), array('jquery'));
            wp_enqueue_script('dlv_font-awesome_js',           plugins_url('assets/vendor/js/font-awesome.js', __DIR__));
            wp_enqueue_script('dlv_ua-parser_js',              plugins_url('assets/vendor/js/ua-parser.min.js', __DIR__));
            // DataTables buttons
            wp_enqueue_script('dlv_datatables_buttons_js',     plugins_url('assets/vendor/js/dataTables.buttons.min.js', __DIR__));
            wp_enqueue_script('dlv_zip_js',                    plugins_url('assets/vendor/js/jszip.min.js', __DIR__));
            wp_enqueue_script('dlv_buttons_html5_js',          plugins_url('assets/vendor/js/buttons.html5.min.js', __DIR__));
            wp_enqueue_script('dlv_buttons_print_js',          plugins_url('assets/vendor/js/buttons.print.min.js', __DIR__));
            wp_enqueue_script('dlv_buttons_colvis_js',         plugins_url('assets/vendor/js/buttons.colVis.min.js', __DIR__));

            wp_localize_script('dlv_app_js', 'dlv_backend_data', [
                'ajax_nonce'   => wp_create_nonce('ajax_nonce'),
            ]);
            wp_enqueue_style('dlv_bootstrap_css',              plugins_url('assets/vendor/css/bootstrap.min.css', __DIR__));
            wp_enqueue_style('dlv_toast_css   ',               plugins_url('assets/vendor/css/toast.css', __DIR__));
            wp_enqueue_style('dlv_now-ui_css',                 plugins_url('assets/vendor/css/now-ui-kit.min.css', __DIR__), ['dlv_bootstrap_css']);
            wp_enqueue_style('dlv_datatables_css',             plugins_url('assets/vendor/css/jquery.dataTables.min.css', __DIR__));
            wp_enqueue_style('dlv_datatables_buttons_css',     plugins_url('assets/vendor/css/buttons.dataTables.min.css', __DIR__));
            wp_enqueue_style('dlv_style',                      plugins_url('assets/css/style.css', __DIR__));
        }
    }
}
