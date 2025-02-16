=== Debug Log Viewer ===

Contributors: lysyiweb
Tags: debug, logs, error tracking, WP_DEBUG
Requires at least: 4.6
Tested up to: 6.7.2
Stable tag: 1.3
Requires PHP: 5.4
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Effortlessly manage your WordPress debug.log. Features include email notifications, search, pagination, and error filtering for effective debugging.


== Tested up to ==
WordPress Version: 6.7.2
PHP Version: 8.2.20

== Description ==

Debug Log Viewer simplifies the process of reviewing and managing your WordPress debug.log file. It’s the perfect tool for developers or site administrators who need real-time insights into the health of their WordPress site by tracking errors, warnings, and deprecated functions.

Key features include:

* Log Viewer: View and search through your debug.log file in a user-friendly interface.
* Pagination & Filters: Quickly navigate and filter logs by error type, making it easy to find the exact issue.
* Email Notifications: Receive alerts when critical issues are logged.
* Settings Panel: Enable or disable logging constants (WP_DEBUG, WP_DEBUG_LOG), choose logging options, and customize your error tracking experience.
* Real-Time Monitoring: Automatically detect and update the log entries as they appear.

With Debug Log Viewer, developers can catch issues early, monitor their site more effectively, and reduce downtime by addressing errors as they arise.

== Installation ==
1. Download the plugin from the WordPress repository.
2. Upload the plugin folder to the /wp-content/plugins/ directory, or install the plugin directly through the WordPress plugin screen.
3. Activate the plugin through the 'Plugins' screen in WordPress.
4. Access the log viewer via the left menu in your WordPress dashboard.


== Future Roadmap == 
* Date-based filtering for improved navigation through large log files.
* Lazy loading support to handle extensive logs without performance hits.
* Error-type filtering for better categorization and focus on specific issues.


== Screenshots ==
1. **Debug Log Viewer** allows a convenient way to browse the debug.log file.

== Changelog ==
= 1.3 =
* Removed outdated and deprecated packages, reducing plugin size
* Parsing custom log records
* Added the ability to filter records by time intervals
* Updated Freemius SDK
* Updated DataTables
* Various UI improvements

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
