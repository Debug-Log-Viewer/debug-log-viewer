<div class="settings section">
    <h5><?php esc_html_e('Settings', 'debug-log-viewer'); ?></h5>

    <?php
        $selected_mode = get_option(DBG_LV_LogModel::DBG_LV_LOG_UPDATES_MODE_OPTION_NAME);
    ?>
    <div class="row log-viewer-row mt-3">
        <div class="form-group settings-group">
            <p class="settings-group-title"><?php esc_html_e('Logs updates mode:', 'debug-log-viewer'); ?></p>

            <div class="form-check form-check-radio">
                <label class="form-check-label">
                    <input 
                        class="form-check-input" 
                        type="radio" 
                        name="UpdatesModeRadioOptions" 
                        value="MANUAL" 
                        <?php checked($selected_mode, 'MANUAL'); ?>
                    >
                    <span class="form-check-sign"></span>
                    <?php esc_html_e('Manual updates', 'debug-log-viewer'); ?>
                </label>
            </div>

            <div class="form-check form-check-radio">
                <label class="form-check-label">
                    <input 
                        class="form-check-input" 
                        type="radio" 
                        name="UpdatesModeRadioOptions" 
                        value="AUTO" 
                        <?php checked($selected_mode, 'AUTO'); ?>
                    >
                    <span class="form-check-sign"></span>
                    <?php esc_html_e('Auto updates', 'debug-log-viewer'); ?>
                </label>
            </div>
        </div>
    </div>
</div>
