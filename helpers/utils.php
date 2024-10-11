<?php

function find_string($rows, $search)
{
    $search = trim(strtolower($search));
    $matches = [];

    foreach ($rows as $key => $value) {
        if (is_array($value)) {
            // Recursively search in nested arrays
            $matches = array_merge($matches, find_string($value, $search));
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

function verify_nonce($form, $action = 'ajax_nonce')
{
    $is_valid_nonce = isset($form['wp_nonce']) && wp_verify_nonce($form['wp_nonce'], $action);

    if (!$is_valid_nonce) {
        echo wp_json_encode([
            'success' => false,
            'error'   => __('Please refresh the page', 'debug-log-viewer'),
        ]);
        wp_die();
    }
}
