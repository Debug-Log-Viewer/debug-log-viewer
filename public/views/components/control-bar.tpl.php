<div class="top-section">
    <div class="log-filepath">
        Path: <span><?php echo esc_html($path); ?></span>
    </div>

    <div class="buttons">
        <button class="btn btn-primary clear-log" title="<?php esc_attr_e('Clear', 'debug-log-viewer'); ?>"><i class="fa fa-solid fa-trash"></i></button>
        <button class="btn btn-primary download-log" title="<?php esc_attr_e('Download', 'debug-log-viewer'); ?>"><i class="fa-solid fa-cloud-arrow-down"></i></button>
        <button class="btn btn-primary refresh-log" title="<?php esc_attr_e('Refresh', 'debug-log-viewer');?>"><i class="fa fa-solid fa-arrows-rotate" aria-hidden="true"></i></button>
  
        <button class="btn btn-success notification" title="<?php esc_attr_e('Notification settings', 'debug-log-viewer'); ?>"><i class="fa-solid fa-bell"></i></button>
        <button class="btn btn-success debug-constants" title="<?php esc_attr_e('Debug constants', 'debug-log-viewer'); ?>"><i class="fa-solid fa-toggle-on"></i></button>
        <button class="btn btn-success settings" title="<?php esc_attr_e('Settings', 'debug-log-viewer'); ?>"><i class="fa-solid fa-gear"></i></button>
  
    </div>
</div>
