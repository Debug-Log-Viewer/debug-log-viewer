<?php
    if (!defined('ABSPATH')) {
        exit; // Exit if accessed directly
    }
?>
<div class="log-not-writable">
    <img src="<?php echo esc_url(plugins_url('../assets/img/logo-broken.png', __DIR__)); ?>" class="mx-auto mt-3" style="display: block">

    <p class="text-center mt-3 fw-bolder">
        The Debug Log Viewer plugin is unable to function correctly due to issues with the configuration file, which is not writable.
    </p>
    <p class="text-center">
        Please ensure that permissions and the file path are correct; otherwise, the plugin won't be able to manage debugging constants.</br>
        Path: <code><?php echo esc_html(DBG_LV_Constants::get_wp_config_path()); ?></code>
    </p>
</div>
