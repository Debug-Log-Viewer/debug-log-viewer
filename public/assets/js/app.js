import $ from 'jquery';
import 'datatables.net-bs5';
import 'datatables.net-buttons-bs5';

window.$ = $;
window.jQuery = $;

try {
    if (page === 'debug-log-viewer') {
        import('./components/log-view.js')
        import('./components/sidebar-toggler.js')
    }
} catch (error) {
    console.log(error);
}
