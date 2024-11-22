jQuery(document).ready(function ($) {
    const toggleLink = $('a[href="#dbg_lv_notificationsContent"]');
    const icon = toggleLink.find('.rotate-icon');
    const content = $('#dbg_lv_notificationsContent');
    const storageKey = 'notificationsBlockState';

    // Restore state from localStorage
    const savedState = localStorage.getItem(storageKey);
    if (savedState === 'collapsed') {
        content.removeClass('show');
        icon.removeClass('expanded');
    } else {
        content.addClass('show');
        icon.addClass('expanded');
    }

    // Use Bootstrap collapse events
    content.on('shown.bs.collapse', function () {
        icon.addClass('expanded');
        localStorage.setItem(storageKey, 'expanded');
    });

    content.on('hidden.bs.collapse', function () {
        icon.removeClass('expanded');
        localStorage.setItem(storageKey, 'collapsed');
    });
});
