(($) => {
    const contentWrapper = $('.content-wrapper');
    const sidebarWrapper = $('.sidebar-wrapper');
    const closeIcon = sidebarWrapper.find('.close-icon');

    const SECTIONS = {
        '.notification': '.notifications',
        '.debug-constants': '.debug-constants',
        '.settings': '.settings'
    };

    // Key for localStorage
    const SIDEBAR_ACTIVE_SECTION_KEY = 'dbg_lv_sidebarActiveSection';

    // Function to save the active section
    function saveSidebarState(activeSection) {
        localStorage.setItem(SIDEBAR_ACTIVE_SECTION_KEY, activeSection || '');
    }

    // Function to load and apply the sidebar state
    function loadSidebarState() {
        const activeSection = localStorage.getItem(SIDEBAR_ACTIVE_SECTION_KEY);
        if (activeSection && activeSection !== 'empty') {
            toggleSidebar(activeSection);
        }
    }

    // Function to toggle visibility
    function toggleSidebar(blockSelector) {
        const section = sidebarWrapper.find(blockSelector);
        if (section.length === 0) {
            console.error(`Section '${sectionSelector}' not found in DOM`);
            return;
        }

        const isVisible = section.hasClass('visible');
        if (isVisible) {
            toggleSideBarSection(blockSelector, false);
            saveSidebarState('empty'); // Save empty value
        } else {
            toggleSideBarSection(blockSelector, true);
            saveSidebarState(blockSelector); // Save active section
        }
    }

    function toggleSideBarSection(sectionName, isOpen) {
        const section = $(sectionName);
        if (!section.length) {
            console.error(`Section with name '${sectionName}' not found in DOM`);
            return;
        }

        contentWrapper.toggleClass('expanded', isOpen);
        sidebarWrapper.toggleClass('opened', isOpen);

        if (isOpen) {
            sidebarWrapper.find('.section').removeClass('visible');
            section.addClass('visible');
        } else {
            section.removeClass('visible');
        }
    }

    // Attach click events to buttons to toggle their corresponding sidebar sections
    $.each(SECTIONS, function (sectionClass, sectionSelector) {
        $(`.buttons ${sectionClass}`).on('click', function () {
            toggleSidebar(sectionSelector);
        });
    });

    // Close icon click event
    closeIcon.on('click', function () {
        contentWrapper.toggleClass('expanded', false);
        sidebarWrapper.toggleClass('opened', false);
        sidebarWrapper.find('.section').removeClass('visible');
        saveSidebarState('empty'); // Save empty value
    });

    // Load saved state on page load
    loadSidebarState();
})(jQuery);
