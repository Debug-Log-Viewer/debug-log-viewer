<div class="settings">
    <h5><?php esc_html_e('Settings', 'debug-log-viewer') ?></h5>

    <div class="row log-viewer-row mt-3">
        <div class="log-info-block">
            <p><?php esc_html_e('Debug mode', 'debug-log-viewer'); ?></p>
            <input id="dbg_lv_toggle_debug_mode" type="checkbox" <?php checked(WP_DEBUG, true); ?> name="debug_mode" class="bootstrap-switch" />
        </div>
        <div class="log-info-block">
            <p><?php esc_html_e('Debug scripts', 'debug-log-viewer'); ?></p>
            <input id="dbg_lv_toggle_debug_scripts" type="checkbox" <?php checked(SCRIPT_DEBUG, true); ?> name="debug_scripts" class="bootstrap-switch" />
        </div>

        <div class="log-info-block">
            <p><?php esc_html_e('Log in file', 'debug-log-viewer'); ?></p>
            <input id="dbg_lv_toggle_debug_log_scripts" type="checkbox" <?php checked(WP_DEBUG_LOG, true); ?> name="debug_log_scripts" class="bootstrap-switch" />
        </div>

        <div class="log-info-block">
            <p><?php esc_html_e('Display errors', 'debug-log-viewer'); ?></p>
            <input id="dbg_lv_toggle_display_errors" type="checkbox" <?php checked(WP_DEBUG_DISPLAY, true); ?> name="display_errors" class="bootstrap-switch" />
        </div>
    </div>
</div>
