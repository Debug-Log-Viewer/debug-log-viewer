<?php

/**
 * Plugin Name: Debug Log Viewer
 * Description: Effortlessly view, search, and manage your WordPress debug.log right in the admin dashboard. Real-time monitoring, email notifications, and filtering make WordPress debugging easy.
 * Author: lysyiweb
 * Version: 1.3
 * Tags: wordpress debug log, debugging, error log, debug
 * Requires PHP: 5.4
 * Tested up to: 6.7.2
 * Stable tag: 1.3
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
