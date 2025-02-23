=== Debug Log Viewer ===

Contributors: lysyiweb
Tags: wordpress debug log, debugging, error log, debug
Requires at least: 4.6
Tested up to: 6.7.1
Stable tag: 1.2.1
Requires PHP: 5.4
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Effortlessly view, search, and manage your WordPress debug.log right in the admin dashboard. Real-time monitoring, email notifications, and filtering make WordPress debugging easy.

== Tested up to ==
WordPress Version: 6.7.1
PHP Version: 8.2.20

== Description ==

**Debug Log Viewer: Your Essential WordPress Debugging Tool**

Tired of struggling to access and understand your WordPress `debug.log` file?  Debug Log Viewer simplifies WordPress debugging by providing a user-friendly interface to view, search, and manage your debug.log directly within your WordPress admin area.  It's the perfect solution for WordPress developers, site administrators, and anyone needing to quickly identify and resolve website issues.

**Gain Real-Time Insights into Your WordPress Site Health**

This plugin is designed to provide you with instant visibility into the inner workings of your WordPress website. By tracking errors, warnings, and deprecated function notices in real-time, Debug Log Viewer empowers you to proactively maintain a healthy and stable WordPress environment.

**Key Features for Efficient WordPress Debugging:**

*   **Real-Time Log Viewer:**  Monitor your WordPress `debug.log` file in real-time, directly from your WordPress dashboard. No more hunting for files via FTP or cPanel!
*   **Easy Debug Log Access:**  Access and view your full WordPress debug log within a clean and intuitive interface.
*   **Search & Filtering:** Quickly find specific log entries with powerful search and filtering options. Filter by error type, keywords, or date (future feature) to pinpoint issues fast.
*   **Pagination:** Navigate through large debug logs with ease using pagination, ensuring smooth performance even with extensive logs.
*   **Email Notifications for Critical Errors:**  Get immediate email alerts when new errors are logged, allowing you to address critical issues before they impact your users (future feature: configurable severity levels).
*   **Flexible Settings Panel:**
    *   **Control WP_DEBUG Constants:**  Enable or disable `WP_DEBUG` and `WP_DEBUG_LOG` constants directly from the plugin settings, without editing `wp-config.php`.
    *   **Customize Logging Options:** Configure your debug log settings to match your specific needs.
    *   **Tailor Your Error Tracking:**  Personalize your debugging experience through plugin settings.
*   **Custom Log Path Support:**  If you've defined a custom path for your `debug.log` file (e.g., using `define( 'WP_DEBUG_LOG', ABSPATH . 'wp-content/logs/debug.log' );`), Debug Log Viewer automatically detects and reads from it.

**Benefits of Using Debug Log Viewer:**

*   **Save Time & Effort:** Stop wasting time manually accessing and parsing your debug log file. Debug Log Viewer puts all the information you need at your fingertips within WordPress admin.
*   **Faster Error Detection:** Real-time monitoring and email notifications help you catch errors as they happen, minimizing potential downtime.
*   **Simplified WordPress Troubleshooting:**  Quickly identify the source of errors and warnings to streamline your WordPress troubleshooting process.
*   **Improved Website Stability:** Proactive error monitoring and resolution contribute to a more stable and reliable WordPress website.
*   **User-Friendly Interface:**  No coding skills required!  Debug Log Viewer is designed for ease of use, making debug log management accessible to everyone.

**Who is Debug Log Viewer For?**

This plugin is invaluable for:

*   WordPress developers
*   Website administrators
*   Freelancers managing client sites
*   Anyone who wants an easy way to monitor WordPress errors and improve website health

== Installation ==
1.  **Installation:** Install Debug Log Viewer from the WordPress Plugin Directory or upload the plugin zip file through your WordPress admin.
2.  **Activation:** Activate the plugin from your Plugins page.
3.  **Access:**  Navigate to the "Debug Log Viewer" menu in your WordPress dashboard to start monitoring your debug log.

**Future Roadmap:**

We are continuously working to improve Debug Log Viewer.  Here are some features planned for future updates:

*   **Date-based Filtering:**  Filter log entries by date ranges for more efficient analysis of large log files.
*   **Lazy Loading:** Implement lazy loading to ensure optimal performance when viewing very large logs.
*   **Enhanced Error-Type Filtering:**  More granular filtering options to categorize and focus on specific error types.
*   **Configurable Email Notifications:**  Customize email notifications based on error severity levels.


== Screenshots ==

1.  **Debug Log Viewer Dashboard:**  The main view provides a clear and searchable interface for Browse your WordPress debug.log file within the WordPress admin.

== Changelog ==

= 1.2.1 =
* Added handling for a custom log file location. Now, you can set something like ` define( 'WP_DEBUG_LOG', ABSPATH . 'wp-content/logs/debug.log' );` in `wp-config.php` and the plugin will read data from that custom path
* Freemius and WP-Config-Transformer update

= 1.2 =
* Removed SSE implementation for live updates, because of unstable behaviour in some cases
* Implemented automatic live updates based on incremental AJAX ping requests.
* Added translations for the front-end phrases
* Implemented full-container width mode with ability to hide sidebar
* Intergated Freemius to become closer to users: contact us, forum links are added

= 1.1 =
* Fixed SSE streaming. Implemented incremental updates
* Decreased log reading limit from 10Mb to 5Mb
* Added ability to collapse Notification block in sidebar to make workspace more clear
* Fixed regular expression to parse datetime with long timezones

= 1.0.3 =
* Removed Toast plugin, used Bootstrap toasts instead
* Small refactoring

= 1.0.2 =
* Added assets (logo)

= 1.0.1 =
* Fix UUID generation

= 1.0.0 =
* Initial release
