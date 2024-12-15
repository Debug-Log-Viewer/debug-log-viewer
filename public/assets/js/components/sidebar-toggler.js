(($) => {
    const sidebar = $('.sidebar');
    const notificationsButton = $('.buttons .notification');
    const toggleButton = $('.toggle');
    const contentWrapper = $('.content-wrapper');
    const closeIcon = sidebar.find('.close-icon');

    // Key for localStorage
    const SIDEBAR_ACTIVE_SECTION_KEY = 'dbg_lv_sidebarActiveSection';

    // Function to save the active section
    function saveSidebarState(activeSection) {
        localStorage.setItem(SIDEBAR_ACTIVE_SECTION_KEY, activeSection || '');
    }

    // Function to load and apply the sidebar state
    function loadSidebarState() {
        const activeSection = localStorage.getItem(SIDEBAR_ACTIVE_SECTION_KEY);

        sidebar.find('.notifications, .settings').addClass('hidden'); // Hide all sections
        if (!activeSection || activeSection === 'settings') {
            // Default or settings
            sidebar.addClass('visible');
            contentWrapper.addClass('expanded');
            sidebar.find('.settings').removeClass('hidden');
        } else if (activeSection === 'notifications') {
            // Notifications
            sidebar.addClass('visible');
            contentWrapper.addClass('expanded');
            sidebar.find('.notifications').removeClass('hidden');
        } else {
            // Sidebar closed
            sidebar.removeClass('visible');
            contentWrapper.removeClass('expanded');
        }
    }

    // Function to toggle visibility
    function toggleSidebar(blockSelector) {
        const block = sidebar.find(blockSelector);
        const isAlreadyVisible = sidebar.hasClass('visible') && block.is(':visible');

        if (isAlreadyVisible) {
            // Close the sidebar
            sidebar.removeClass('visible');
            contentWrapper.removeClass('expanded');
            block.addClass('hidden');
            saveSidebarState('empty'); // Save empty value
        } else {
            // Open and show the selected block
            sidebar.addClass('visible');
            contentWrapper.addClass('expanded');
            sidebar.find('.notifications, .settings').addClass('hidden'); // Hide all sections
            block.removeClass('hidden'); // Show the selected block
            const activeSection = blockSelector === '.notifications' ? 'notifications' : 'settings';
            saveSidebarState(activeSection); // Save active section
        }
    }

    // Click event for notifications button
    notificationsButton.on('click', function () {
        toggleSidebar('.notifications');
    });

    // Click event for toggle button
    toggleButton.on('click', function () {
        toggleSidebar('.settings');
    });

    // Close icon click event
    closeIcon.on('click', function () {
        sidebar.removeClass('visible');
        contentWrapper.removeClass('expanded');
        saveSidebarState('empty'); // Save empty value
    });

    // Load saved state on page load
    loadSidebarState();
})(jQuery);
