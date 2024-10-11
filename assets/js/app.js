
{
    import("./init.js");

    try {
        switch (new URL(location.href).searchParams.get('page')) {
            case 'debug-log-viewer':
                import("./components/log-view.js");
                dlv_sendComponentEvent('log view');
                break;
        }
    } catch (error) {
        console.log(error);
    }

    function dlv_sendComponentEvent(component) {

        if (!dlv_backend_data.is_dev) {
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments); }
            gtag('js', new Date());
            gtag('config', dlv_backend_data.analytics_id);
            gtag('event', 'page_open', {'event_category': 'Component', 'event_label': `Opened ${component}`});
        }
    }
}

