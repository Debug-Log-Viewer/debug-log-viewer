<?php

if (!defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}

class DBG_LV_ReviewController
{

    public static function dbg_lv_is_review_delaying_expired()
    {
        $user_id = wp_get_current_user()->ID;

        return time() - DBG_LV_Constants::WEEK_IN_SECONDS > (int) get_user_meta($user_id, 'dbg_lv_next_schedule_review_notice_time', true);
    }

    public static function dbg_lv_is_plugin_page()
    {
        // Check if the 'page' parameter is set and sanitize it
        $page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';
        return strpos($page, 'debug-log-viewer') !== false;
    }
    
    public static function dbg_lv_ask_to_leave_review_handler()
    {
        return;
        if (!self::dbg_lv_is_plugin_page()) {
            return;
        }

        $url = add_query_arg($_GET, admin_url('admin.php'));
        ?>

        <div class="alert alert-info review" role="alert">
            <a data-rate-action="do-rate" href="<?php echo esc_url(add_query_arg('dbg_lv_review', 'do', $url)); ?>">
                <div class="d-flex justify-content-between">
                    <div>
                        <?php _e('It would help us a great deal if you could give us your feedback on WP Plugin Directory. We are hoping we earned your ', DBG_LV_Phrases::$domain); ?>
                        <i class="fa-solid fa-star fa-fade fa-2sm"></i>
                        <i class="fa-solid fa-star fa-fade fa-2sm"></i>
                        <i class="fa-solid fa-star fa-fade fa-2sm"></i>
                        <i class="fa-solid fa-star fa-fade fa-2sm"></i>
                        <i class="fa-solid fa-star fa-fade fa-2sm"></i>
                    </div>

                    <span aria-hidden="true">
                        <a data-rate-action="later" href="<?php echo esc_url(add_query_arg('dbg_lv_review', 'later', $url)); ?>">
                            <i class="fa fa-close"></i>
                        </a>
                    </span>
                </div>
            </a>
        </div>
    <?php }

    public static function dbg_lv_review_handler()
    {
        $user_id = wp_get_current_user()->ID;

        // Only perform nonce verification if the 'dbg_lv_review' query parameter is present
        if (isset($_GET['dbg_lv_review'])) {
            // Sanitize the 'dbg_lv_review' query parameter before using it
            $dbg_lv_review = sanitize_text_field(wp_unslash($_GET['dbg_lv_review']));

            if ($user_id && $dbg_lv_review) {
                $is_next_schedule_review_time_exists = (bool) get_user_meta($user_id, 'dbg_lv_next_schedule_review_notice_time');

                if ($dbg_lv_review === 'later') {
                    if ($is_next_schedule_review_time_exists) {
                        update_user_meta($user_id, 'dbg_lv_next_schedule_review_notice_time', time() + DBG_LV_Constants::TWO_MONTH_IN_SECONDS);
                    } else {
                        add_user_meta($user_id, 'dbg_lv_next_schedule_review_notice_time', time() + DBG_LV_Constants::TWO_MONTH_IN_SECONDS, true);
                    }
                } elseif ($dbg_lv_review === 'do') {
                    if ($is_next_schedule_review_time_exists) {
                        update_user_meta($user_id, 'dbg_lv_next_schedule_review_notice_time', time() + DBG_LV_Constants::SIX_MONTH_IN_SECONDS);
                    } else {
                        add_user_meta($user_id, 'dbg_lv_next_schedule_review_notice_time', time() + DBG_LV_Constants::SIX_MONTH_IN_SECONDS, true);
                    }

                    // Redirect to WordPress.org reviews
                    wp_redirect('https://wordpress.org/support/plugin/debug-log-viewer/reviews/#new-post?filter=5');
                    exit;
                }
            }
        }
    }
}
