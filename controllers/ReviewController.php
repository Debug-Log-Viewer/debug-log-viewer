<?php

if (!defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}


class DLV_ReviewController
{
    public static function dlv_is_review_delaying_expired()
    {
        $user_id = wp_get_current_user()->ID;

        return time() - DLV_Constants::WEEK_IN_SECONDS > (int) get_user_meta($user_id, 'dlv_next_schedule_review_notice_time', true);
    }

    public static function dlv_is_plugin_page()
    {
        $page = isset($_GET['page']) ? $_GET['page'] : '';

        return strpos($page, 'debug-log-viewer') !== false;
    }

    public static function dlv_ask_to_leave_review_handler()
    {
        if (!self::dlv_is_plugin_page()) {
            return;
        }

        $url = add_query_arg($_GET, admin_url('admin.php'));
?>

        <div class="alert alert-info review" role="alert">
            <a data-rate-action="do-rate" href="<?php echo esc_url(add_query_arg('dlv_review', 'do', $url)); ?>">
                <div class="d-flex justify-content-between">
                    <div>
                        <?php esc_html_e('It would help us a great deal if you could give us your feedback on WP plugin directory. We are hoping we earned your ', 'debug-log-viewer'); ?>
                        <i class="fa-solid fa-star fa-fade fa-2sm"></i>
                        <i class="fa-solid fa-star fa-fade fa-2sm"></i>
                        <i class="fa-solid fa-star fa-fade fa-2sm"></i>
                        <i class="fa-solid fa-star fa-fade fa-2sm"></i>
                        <i class="fa-solid fa-star fa-fade fa-2sm"></i>
                    </div>

                    <span aria-hidden="true">
                        <a data-rate-action="later" href="<?php echo esc_url(add_query_arg('dlv_review', 'later', $url)); ?>">
                            <i class="fa fa-close"></i>
                        </a>
                    </span>
                </div>
            </a>
        </div>
<?php }

    public static function dlv_review_handler()
    {
        $user_id = wp_get_current_user()->ID;

        if ($user_id && isset($_GET['dlv_review'])) {
            $is_next_schedule_review_time_exists = (bool) get_user_meta($user_id, 'dlv_next_schedule_review_notice_time');

            if ($_GET['dlv_review'] === 'later') {
                if ($is_next_schedule_review_time_exists) {
                    update_user_meta($user_id, 'dlv_next_schedule_review_notice_time', time() + DLV_Constants::TWO_MONTH_IN_SECONDS);
                } else {
                    add_user_meta($user_id, 'dlv_next_schedule_review_notice_time', time() + DLV_Constants::TWO_MONTH_IN_SECONDS, true);
                }
            } elseif ($_GET['dlv_review'] === 'do') {
                if ($is_next_schedule_review_time_exists) {
                    update_user_meta($user_id, 'dlv_next_schedule_review_notice_time', time() + DLV_Constants::SIX_MONTH_IN_SECONDS);
                } else {
                    add_user_meta($user_id, 'dlv_next_schedule_review_notice_time', time() + DLV_Constants::SIX_MONTH_IN_SECONDS, true);
                }
                wp_redirect('https://wordpress.org/support/plugin/debug-log-viewer/reviews/#new-post?filter=5');
            }
        }
    }
}
