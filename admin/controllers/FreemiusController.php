<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class DBG_LV_FreemiusController
{
    /**
     * Initialize the Freemius SDK for the plugin.
     */
    public static function dbg_lv_init()
    {
        if (!function_exists('dbg_lv')) {
            // Create a helper function for easy SDK access.
            function dbg_lv()
            {
                global $dbg_lv;

                if (isset($dbg_lv)) {
                    return $dbg_lv;
                }

                // Include Freemius SDK.
                DBG_LV_FreemiusController::include_sdk();

                $dbg_lv = fs_dynamic_init(array(
                    'id'                  => '17350',
                    'slug'                => 'debug-log-viewer',
                    'type'                => 'plugin',
                    'public_key'          => 'pk_d456c712f16510d920c9f4ba4880a',
                    'is_premium'          => false,
                    'has_addons'          => false,
                    'has_paid_plans'      => false,
                    'menu'                => array(
                        'slug'           => 'debug-log-viewer',
                        'first-path'     => 'admin.php?page=debug-log-viewer',
                    ),
                ));

                return $dbg_lv;
            }

            // Initialize Freemius.
            dbg_lv();

            // Signal that the SDK was initiated.
            do_action('dbg_lv_loaded');
        }
    }

    /**
     * Includes the Freemius SDK.
     */
    public static function include_sdk()
    {
        $freemius_path = dirname(__FILE__) . '/../../vendor/freemius/wordpress-sdk/start.php';

        if (file_exists($freemius_path)) {
            require_once $freemius_path;
        } else {
            $fallback_path = dirname(__FILE__) . '/../../freemius/wordpress-sdk/start.php';

            if (file_exists($fallback_path)) {
                require_once $fallback_path;
            } else {
                // Handle the error if neither path exists.
                wp_die('Freemius SDK not found. Please check the installation.');
            }
        }
    }
}
