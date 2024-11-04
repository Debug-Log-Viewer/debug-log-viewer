<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function dbg_lv_send_log_viewer_email($notification_email, $subject, $template_name, $params)
{
    $template = file_get_contents($template_name);
    $template = str_replace('{{dbg_lv_summary}}', DBG_LV_LogModel::render_log_viewer_errors($params['errors']), $template);
    $template = str_replace('{{dbg_lv_website}}', $params['website'], $template);

    wp_mail(
        $notification_email,
        $subject,
        $template,
        array('Content-Type: text/html; charset=UTF-8')
    );
}
