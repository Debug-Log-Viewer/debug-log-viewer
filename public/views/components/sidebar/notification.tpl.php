<div class="notifications hidden">
    <h5><?php esc_html_e('Notifications', 'debug-log-viewer'); ?></h5>

    <?php $notificator = new DBG_LV_Notificator(new DBG_LV_LogController()); ?>
    <form class="form-group mt-3" id="dbg_lv_log_viewer_notifications_form" data-notifications-enabled="<?php echo esc_attr($notificator->dbg_lv_is_notification_enabled() ? 'true' : 'false'); ?>">
        <p><?php esc_html_e('You will receive an email notification in case a serious problem is detected on the website', 'debug-log-viewer'); ?></p>
        <p><?php esc_html_e('Monitoring tracks database, fatal, deprecated and parse errors', 'debug-log-viewer'); ?></p>
        <label for="email"><?php esc_html_e('Your Email:', 'debug-log-viewer'); ?></label>
        <input type="email" id="email" value="<?php echo esc_attr($notificator->dbg_lv_get_notification_email()); ?>" />

        <label for="recurrence"><?php esc_html_e('Periodicity:', 'debug-log-viewer'); ?></label>
        <select name="recurrence" id="recurrence">
            <?php $notificator->dbg_lv_get_notification_recurrence(); ?>
        </select>

        <div class="form-check">
            <label class="form-check-label">
                <input type="checkbox" name="send_test_email" id="send_test_email" class="form-check-input">
                <span class="form-check-sign"></span>
                <?php esc_html_e('Send me test Email now', 'debug-log-viewer') ?>
            </label>
        </div>

        <input type="submit" value="<?php esc_attr_e('Loading...', 'debug-log-viewer'); ?>" class="btn btn-secondary btn-sm" disabled />
    </form>
</div>
