<div class="settings section">
    <h5><?php esc_html_e('Settings', 'debug-log-viewer'); ?></h5>

    <?php
        $selected_mode = get_option(DBG_LV_LogModel::DBG_LV_LOG_UPDATES_MODE_OPTION_NAME);
    ?>
   <div class="row log-viewer-row mt-3">
    <div class="form-group settings-group">
        <fieldset>
            <legend class="settings-group-title"><?php esc_html_e('The log update mode', 'debug-log-viewer'); ?></legend>
            
            <div class="form-check">
                <input 
                    class="form-check-input" 
                    value="<?php echo esc_attr('MANUAL'); ?>" 
                    <?php checked($selected_mode, 'MANUAL'); ?> 
                    type="radio" 
                    name="UpdatesModeRadioOptions" 
                    id="<?php echo esc_attr('UpdatesModeRadioOptionsManual'); ?>"
                >
                <label class="form-check-label" for="<?php echo esc_attr('UpdatesModeRadioOptionsManual'); ?>">
                    <?php esc_html_e('Manual', 'debug-log-viewer'); ?>
                </label>
            </div>

            <div class="form-check">
                <input 
                    class="form-check-input" 
                    value="<?php echo esc_attr('AUTO'); ?>" 
                    <?php checked($selected_mode, 'AUTO'); ?> 
                    type="radio" 
                    name="UpdatesModeRadioOptions" 
                    id="<?php echo esc_attr('UpdatesModeRadioOptionsAuto'); ?>"
                >
                <label class="form-check-label" for="<?php echo esc_attr('UpdatesModeRadioOptionsAuto'); ?>">
                    <?php esc_html_e('Auto', 'debug-log-viewer'); ?>
                </label>
            </div>
        </fieldset>
    </div>
</div>

</div>
