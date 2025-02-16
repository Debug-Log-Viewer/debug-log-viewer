<div class="debug-constants section">
    <h5><?php esc_html_e('Debug Constants', 'debug-log-viewer') ?></h5>

    <div class="row log-viewer-row mt-3">
        <div class="log-info-block">
            <div class="form-check form-switch">
                <label class="form-check-label" for="dbg_lv_toggle_debug_mode"><?php esc_html_e('Debug mode', 'debug-log-viewer'); ?></label>
                <input id="dbg_lv_toggle_debug_mode" class="form-check-input" type="checkbox" <?php checked(WP_DEBUG, true); ?> name="debug_mode">
            </div>
        </div>
        <div class="log-info-block">
            <div class="form-check form-switch">
                <label class="form-check-label" for="dbg_lv_toggle_debug_scripts"><?php esc_html_e('Debug scripts', 'debug-log-viewer'); ?></label>
                <input id="dbg_lv_toggle_debug_scripts" class="form-check-input" type="checkbox" <?php checked(SCRIPT_DEBUG, true); ?> name="debug_scripts">
            </div>
        </div>
        <div class="log-info-block">
            <div class="form-check form-switch">
            <label class="form-check-label" for="dbg_lv_toggle_debug_log_scripts"><?php esc_html_e('Log in file', 'debug-log-viewer'); ?></label>
                <input id="dbg_lv_toggle_debug_log_scripts" class="form-check-input" type="checkbox" <?php checked(WP_DEBUG_LOG, true); ?> name="debug_log_scripts">
            </div>
        </div>
        <div class="log-info-block">
            <div class="form-check form-switch">
                <label class="form-check-label" for="dbg_lv_toggle_display_errors"><?php esc_html_e('Display errors', 'debug-log-viewer'); ?></label>
                <input id="dbg_lv_toggle_display_errors" class="form-check-input" type="checkbox" <?php checked(WP_DEBUG_DISPLAY, true); ?> name="display_errors">
            </div>
        </div>
    </div>
</div>
