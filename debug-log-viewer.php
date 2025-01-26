<?php

/**
 * Plugin Name: Debug Log Viewer
 * Description: Simplifies the process of reviewing and managing your WordPress debug.log file.

 * Author: lysyiweb
 * Version: 1.2
 * Tags: debug, logging, WP_DEBUG, error-tracking
 * Requires PHP: 5.4
 * Tested up to: 6.7.1
 * Stable tag: 1.2.1
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
if (!defined('ABSPATH')) {
    exit;
}

require_once realpath(__DIR__) . '/admin/helpers/constants.php';
require_once realpath(__DIR__) . '/admin/translations/phrases.php';

$controllers = [ 'Hooks', 'Menu', 'Log', 'Service', 'Notificator', 'Review', 'Freemius'];

foreach ($controllers as $controller) {
    require_once realpath(__DIR__) . "/admin/controllers/{$controller}Controller.php";
}

DBG_LV_MenuController::dbg_lv_init();
DBG_LV_HooksController::dbg_lv_init();
DBG_LV_FreemiusController::dbg_lv_init();
