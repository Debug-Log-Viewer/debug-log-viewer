<?php

/**
 * Plugin Name: Debug Log Viewer
 * Description: Debug Log Viewer description
 * Author: lysyiweb
 * Version: 1.0.2
 * Tags: debug, logging, WP_DEBUG, error-tracking
 * Requires PHP: 5.4
 * Tested up to: 6.6.2
 * Stable tag: 1.0.1
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
if (!defined('ABSPATH')) {
    exit;
}

require_once realpath(__DIR__) . '/admin/helpers/constants.php';
require_once realpath(__DIR__) . '/admin/controllers/HooksController.php';
require_once realpath(__DIR__) . '/admin/controllers/MenuController.php';
require_once realpath(__DIR__) . '/admin/controllers/LogController.php';
require_once realpath(__DIR__) . '/admin/controllers/ServiceController.php';
require_once realpath(__DIR__) . '/admin/controllers/NotificatorController.php';
require_once realpath(__DIR__) . '/admin/controllers/ReviewController.php';
DBG_LV_MenuController::dbg_lv_init();
DBG_LV_HooksController::dbg_lv_init();
