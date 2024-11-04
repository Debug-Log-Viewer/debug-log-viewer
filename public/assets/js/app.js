{
    try {
        switch (new URL(location.href).searchParams.get('page')) {
            case 'debug-log-viewer':
                import("./components/log-view.js");
                break;
        }
    } catch (error) {
        console.log(error);
    }
}

