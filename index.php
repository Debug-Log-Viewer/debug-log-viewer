<?php

/**
 * Plugin Name: Debug Log Viewer
 * Description: Debug Log Viewer description
 * Author: lysyiweb
 * Version: 1.0.0
 * Tags: debug, logging, WP_DEBUG, error-tracking
 * Requires PHP: 5.4
 * Tested up to: 6.6.2
 * Stable tag: 1.0.0
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
if (!defined('ABSPATH')) {
    exit;
}

require_once realpath(__DIR__) . '/helpers/constants.php';
require_once realpath(__DIR__) . '/controllers/HooksController.php';
require_once realpath(__DIR__) . '/controllers/MenuController.php';
require_once realpath(__DIR__) . '/controllers/LogController.php';
require_once realpath(__DIR__) . '/controllers/ServiceController.php';
require_once realpath(__DIR__) . '/controllers/NotificatorController.php';
require_once realpath(__DIR__) . '/controllers/ReviewController.php';
DLV_MenuController::dlv_init();
DLV_HooksController::dlv_init();
