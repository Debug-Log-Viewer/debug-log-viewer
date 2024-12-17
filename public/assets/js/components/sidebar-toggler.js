(($) => {
    const content = $('.main-content');
    const sidebar = $('.sidebar');
    const closeIcon = sidebar.find('.close-icon');

    const SECTIONS = {
        '.notification': '.notifications',
        '.debug-constants': '.debug-constants',
        '.settings': '.settings'
    };

    // Key for localStorage
    const SIDEBAR_ACTIVE_SECTION_KEY = 'dbg_lv_sidebarActiveSection';

    // Default section if none is saved
    const DEFAULT_SECTION = '.debug-constants';

    // Function to save the active section
    function saveSidebarState(activeSection) {
        localStorage.setItem(SIDEBAR_ACTIVE_SECTION_KEY, activeSection);
    }

    // Function to load and apply the sidebar state
    function loadSidebarState() {
        const activeSection = localStorage.getItem(SIDEBAR_ACTIVE_SECTION_KEY);
        if (activeSection) {
            if(activeSection !== 'none') {
                toggleSidebar(activeSection);
            }
        } else {
            // Show default section if no state is saved
            toggleSidebar(DEFAULT_SECTION);
        }
    }

    // Function to toggle visibility
    function toggleSidebar(blockSelector) {
        const section = sidebar.find(blockSelector);
        if (section.length === 0) {
            console.error(`Section '${blockSelector}' not found in DOM`);
            return;
        }

        const isVisible = section.hasClass('visible');
        if (isVisible) {
            toggleSideBarSection(blockSelector, false);
            saveSidebarState('none'); // Save empty value
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

        content.toggleClass('expanded', isOpen);
        sidebar.toggleClass('opened', isOpen);

        if (isOpen) {
            sidebar.find('.section').removeClass('visible');
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
        content.toggleClass('expanded', false);
        sidebar.toggleClass('opened', false);
        sidebar.find('.section').removeClass('visible');
        saveSidebarState('none'); // Save empty value
    });

    // Load saved state on page load
    loadSidebarState();
})(jQuery);
