<div class="debug-constants section">
    <h5><?php esc_html_e('Debug Constants', 'debug-log-viewer'); ?></h5>

    <div class="row log-viewer-row mt-3">
        <!-- Debug Mode -->
        <div class="log-info-block form-check form-switch">
            <label class="form-check-label" for="dbg_lv_toggle_debug_mode"><?php esc_html_e('Debug mode', DBG_LV_Phrases::$domain); ?></label>
            <input
                id="dbg_lv_toggle_debug_mode"
                type="checkbox"
                <?php checked(WP_DEBUG, true); ?>
                name="debug_mode"
                class="form-check-input" />
        </div>

        <!-- Debug Scripts -->
        <div class="log-info-block form-check form-switch">
            <label class="form-check-label" for="dbg_lv_toggle_debug_scripts"><?php esc_html_e('Debug scripts', DBG_LV_Phrases::$domain); ?></label>
            <input
                id="dbg_lv_toggle_debug_scripts"
                type="checkbox"
                <?php checked(SCRIPT_DEBUG, true); ?>
                name="debug_scripts"
                class="form-check-input" />
        </div>

        <!-- Log in File -->
        <div class="log-info-block form-check form-switch">
            <label class="form-check-label" for="wp_ajax_dbg_lv_toggle_log_in_file"><?php esc_html_e('Log in file', DBG_LV_Phrases::$domain); ?></label>

            <?php
            $isCustomPath = DBG_LV_LogController::dbg_lv_is_custom_logging_path();
            $isDisabled = $isCustomPath;
            $isChecked = $isCustomPath || in_array(WP_DEBUG_LOG, [1, true, '1', 'true'], true) ? 'checked' : '';
            ?>
            <input
                id="wp_ajax_dbg_lv_toggle_log_in_file"
                <?php disabled($isDisabled); ?>
                type="checkbox"
                <?php echo $isChecked; ?>
                name="log_in_file"
                class="form-check-input" />
        </div>

        <!-- Display Errors -->
        <div class=" log-info-block form-check form-switch">
            <label class="form-check-label" for="dbg_lv_toggle_display_errors"><?php esc_html_e('Display errors', DBG_LV_Phrases::$domain); ?></label>
            <input
                id="dbg_lv_toggle_display_errors"
                type="checkbox"
                <?php checked(WP_DEBUG_DISPLAY, true); ?>
                name="display_errors"
                class="form-check-input" />
        </div>
    </div>
</div>
