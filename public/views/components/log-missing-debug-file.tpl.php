<?php

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}

?>
<div class="log-not-found">
  <img src="<?php echo esc_url(plugins_url('../assets/img/logo-colorful.svg', __DIR__)); ?>" alt="" style="margin:0 auto; display:block">

  <h5>
    Welcome!
    <br>
    <span> It looks like this is your first time here.</span>
    <br>
    <span>We couldn't find a log file to begin debugging, so let's start fresh</span>
  </h5>

  <div class="row">
    <div class="col-md-12">
      <button id="dbg_lv_log_viewer_enable_logging" class="btn btn-lg btn-primary">
        Start Debugging
      </button>
    </div>
    <div class="col-md-12">
      <p class="manual-debugging-instructions_or">OR</p>
    </div>

    <div class="col-md-12">
      <a class="manual-debugging-instructions" data-bs-toggle="collapse" href="#dbg_lv_debugging_instructions">
        Manual Debugging Instructions
      </a>
    </div>
  </div>
  <div class="row">
    <div class="collapse" id="dbg_lv_debugging_instructions">
      <div class="card card-body">
        <ul style="list-style: number;">
          <li>
            Locate the <code>wp-config.php</code> file in the root folder of your WordPress installation.
            You can access your site files through FTP, cPanel, or any File Manager plugin
          </li>
          <li>
            Open <code>wp-config.php</code> for editing and look for lines like the following:
            <code>define( 'WP_DEBUG', true );</code> or <code>define( 'WP_DEBUG_LOG', true );</code>
          </li>
          <li> Change their values to <code>true</code> if their current values are <code>false</code></li>
          <li>
            If you don't see these lines, add them somewhere in the middle of the file
          </li>
          <li>
            Save the changes. In the event of any exceptional situations occurring on
            the website, you will see a log table on this page
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
