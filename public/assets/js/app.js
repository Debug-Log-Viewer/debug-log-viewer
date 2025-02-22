import $ from 'jquery';
import 'datatables.net-bs5';
import 'datatables.net-buttons-bs5';

window.$ = $;
window.jQuery = $;

try {
    switch (new URL(location.href).searchParams.get('page')) {
        case 'debug-log-viewer':
            import("./components/log-view.js");
            import("./components/sidebar-toggler.js");
            break;
    }
} catch (error) {
    console.log(error);
}
