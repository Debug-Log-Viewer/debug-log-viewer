<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function dbg_lv_find_string($rows, $search)
{
    $search = trim(strtolower($search));
    $matches = [];

    foreach ($rows as $key => $value) {
        if (is_array($value)) {
            // Recursively search in nested arrays
            $matches = array_merge($matches, dbg_lv_find_string($value, $search));
        } elseif (is_string($value)) {
            // Trim the string before comparison
            $trimmedValue = trim(strtolower($value));

            // Check for a match
            if (strpos($trimmedValue, $search) !== false) {
                $matches[] = $value;
            }
        }
    }

    return $matches;
}

function dbg_lv_verify_nonce($nonce, $action = 'ajax_nonce')
{
    if (!wp_verify_nonce($nonce, $action)) {
        echo wp_json_encode([
            'success' => false,
            'error'   => __('Please refresh the page', 'debug-log-viewer'),
        ]);
        wp_die();
    }
}

function dbg_lv_get_document_root() {
    return isset($_SERVER['DOCUMENT_ROOT']) 
        ? sanitize_text_field(wp_unslash($_SERVER['DOCUMENT_ROOT'])) 
        : '';
}
