<div class="debug-constants section">
    <h5><?php esc_html_e('Debug Constants', 'debug-log-viewer'); ?></h5>

    <div class="row log-viewer-row mt-3">
        <!-- Debug Mode -->
        <div class="log-info-block">
            <p><?php esc_html_e('Debug mode', 'debug-log-viewer'); ?></p>
            <input 
                id="dbg_lv_toggle_debug_mode" 
                type="checkbox" 
                <?php checked(WP_DEBUG, true); ?> 
                name="debug_mode" 
                class="bootstrap-switch" 
            />
        </div>

        <!-- Debug Scripts -->
        <div class="log-info-block">
            <p><?php esc_html_e('Debug scripts', 'debug-log-viewer'); ?></p>
            <input 
                id="dbg_lv_toggle_debug_scripts" 
                type="checkbox" 
                <?php checked(SCRIPT_DEBUG, true); ?> 
                name="debug_scripts" 
                class="bootstrap-switch" 
            />
        </div>

        <!-- Log in File -->
        <div class="log-info-block">
            <p><?php esc_html_e('Log in file', 'debug-log-viewer'); ?></p>
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
                class="bootstrap-switch" 
            />
        </div>

        <!-- Display Errors -->
        <div class="log-info-block">
            <p><?php esc_html_e('Display errors', 'debug-log-viewer'); ?></p>
            <input 
                id="dbg_lv_toggle_display_errors" 
                type="checkbox" 
                <?php checked(WP_DEBUG_DISPLAY, true); ?> 
                name="display_errors" 
                class="bootstrap-switch" 
            />
        </div>
    </div>
</div>
