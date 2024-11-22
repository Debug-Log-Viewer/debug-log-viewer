{
    try {
        switch (new URL(location.href).searchParams.get('page')) {
            case 'debug-log-viewer':
                import("./components/log-view.js");
                import("./components/block-toggler.js");
                break;
        }
    } catch (error) {
        console.log(error);
    }
}

