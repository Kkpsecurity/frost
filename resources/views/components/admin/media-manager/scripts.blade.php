<script>
    let currentDisk = 'public'; // Start with public disk
    let currentPath = '/';
    let viewMode = 'grid';
    let currentFiles = [];
    let currentDirectories = [];

    // Map disk names to their corresponding grid IDs in the templates
    const diskToGridMap = {
        'public': 'public',
        'local': 'private', // local disk uses 'private' as the grid ID
        's3': 's3'
    };

    $(document).ready(function() {
        // Initialize the media manager
        initializeMediaManager();


    });

    function initializeMediaManager() {
        console.log('Initializing Media Manager...');

        // Debug: List all media manager elements
        console.log('=== DEBUGGING MEDIA MANAGER ELEMENTS ===');
        console.log('All elements with IDs containing "Loading":', $('[id*="Loading"]').map(function() { return this.id; }).get());
        console.log('All elements with IDs containing "UploadArea":', $('[id*="UploadArea"]').map(function() { return this.id; }).get());
        console.log('All elements with IDs containing "EmptyState":', $('[id*="EmptyState"]').map(function() { return this.id; }).get());
        console.log('All elements with IDs containing "Grid":', $('[id*="Grid"]').map(function() { return this.id; }).get());
        console.log('=== END DEBUG ===');

        prepareUiDefaultState();
        bindEventHandlers();
        checkAllDiskStatuses();

        console.log(`Initial state - currentDisk: ${currentDisk}, currentPath: ${currentPath}`);

        // Load initial files
        loadFiles().then(() => {
            console.log('Initial file loading completed');
        }).catch((error) => {
            console.error('Initial file loading failed:', error);
        });
    }

    /**
     * Get all three states: loading, uploader, and file list where the default state is loading
     * and it only hides when the other states are properly loaded and ready
     */
    function prepareUiDefaultState() {
        console.log('Preparing UI default state - showing loading until other states are ready');

        // Hide all sections for all disks initially and show loading
        Object.keys(diskToGridMap).forEach(diskName => {
            const templateDiskId = diskToGridMap[diskName];

            // Get all elements for this disk
            const $grid = $(`#${templateDiskId}Grid`);
            const $uploadArea = $(`#${templateDiskId}UploadArea`);
            const $emptyState = $(`#${templateDiskId}EmptyState`);
            const $loading = $(`#${templateDiskId}Loading`);

            console.log(`=== PREPARING ${diskName.toUpperCase()} (${templateDiskId}) ===`);
            console.log(`Found elements - Grid: ${$grid.length}, Upload: ${$uploadArea.length}, Empty: ${$emptyState.length}, Loading: ${$loading.length}`);

            // NUCLEAR APPROACH - Force hide all content states with multiple methods
            $grid.hide().css({
                'display': 'none !important',
                'visibility': 'hidden',
                'height': '0 !important',
                'min-height': '0 !important',
                'max-height': '0 !important',
                'margin': '0 !important',
                'padding': '0 !important',
                'overflow': 'hidden !important',
                'flex': 'none !important',
                'flex-grow': '0 !important',
                'flex-shrink': '0 !important',
                'flex-basis': '0 !important',
                'align-self': 'auto !important'
            }).removeClass('show active d-flex align-items-center justify-content-center');

            $uploadArea.hide().css({
                'display': 'none !important',
                'visibility': 'hidden',
                'height': '0 !important',
                'min-height': '0 !important',
                'max-height': '0 !important',
                'margin': '0 !important',
                'padding': '0 !important',
                'overflow': 'hidden !important',
                'flex': 'none !important',
                'flex-grow': '0 !important',
                'flex-shrink': '0 !important',
                'flex-basis': '0 !important',
                'align-self': 'auto !important'
            }).removeClass('show active d-flex align-items-center justify-content-center');

            $emptyState.hide().css({
                'display': 'none !important',
                'visibility': 'hidden',
                'height': '0 !important',
                'min-height': '0 !important',
                'max-height': '0 !important',
                'margin': '0 !important',
                'padding': '0 !important',
                'overflow': 'hidden !important',
                'flex': 'none !important',
                'flex-grow': '0 !important',
                'flex-shrink': '0 !important',
                'flex-basis': '0 !important',
                'align-self': 'auto !important'
            }).removeClass('show active d-flex align-items-center justify-content-center');

            // Show loading for all disks initially (will be hidden when content is ready)
            $loading.show().css({
                'display': 'block',
                'visibility': 'visible',
                'height': 'auto',
                'min-height': 'auto',
                'max-height': 'none',
                'margin': '',
                'padding': '',
                'overflow': '',
                'flex': '',
                'flex-grow': '',
                'flex-shrink': '',
                'flex-basis': '',
                'align-self': ''
            }).addClass('show active');            console.log(`Disk ${diskName} (${templateDiskId}): All content hidden, loading shown`);

            // Verify what we just did
            console.log(`Verification - Grid visible: ${$grid.is(':visible')}, Upload visible: ${$uploadArea.is(':visible')}, Empty visible: ${$emptyState.is(':visible')}, Loading visible: ${$loading.is(':visible')}`);
        });

        // Set the active tab
        $('.nav-tabs a').removeClass('active');
        $(`.nav-tabs a[data-disk="${currentDisk}"]`).addClass('active');

        // Hide all tab content and show only the current disk's tab
        $('.tab-pane').removeClass('show active').hide();
        const currentTemplateDiskId = diskToGridMap[currentDisk] || currentDisk;
        const $currentTab = $(`#${currentTemplateDiskId}`);
        if ($currentTab.length > 0) {
            $currentTab.addClass('show active').show();
            console.log(`Activated tab for disk: ${currentDisk}`);
        }

        // Update current disk display
        $('#currentDiskDisplay').text(currentDisk.charAt(0).toUpperCase() + currentDisk.slice(1));

        console.log('UI default state preparation completed - loading states active');

        // Final check of current disk
        setTimeout(() => {
            const $currentGrid = $(`#${currentTemplateDiskId}Grid`);
            const $currentUpload = $(`#${currentTemplateDiskId}UploadArea`);
            const $currentEmpty = $(`#${currentTemplateDiskId}EmptyState`);
            const $currentLoading = $(`#${currentTemplateDiskId}Loading`);

            console.log('=== FINAL STATE CHECK AFTER INITIALIZATION ===');
            console.log(`Current disk (${currentDisk}) elements visible: Grid=${$currentGrid.is(':visible')}, Upload=${$currentUpload.is(':visible')}, Empty=${$currentEmpty.is(':visible')}, Loading=${$currentLoading.is(':visible')}`);

            const visibleCount = [$currentGrid, $currentUpload, $currentEmpty, $currentLoading].filter(el => el.is(':visible')).length;
            console.log(`VISIBLE COUNT AFTER INIT: ${visibleCount} (should be 1 - loading only)`);
        }, 50);
    }

    /**
     * Test if all required components are loaded and ready, then hide loading state
     */
    function testAndHideLoadingWhenReady() {
        const currentTemplateDiskId = diskToGridMap[currentDisk] || currentDisk;
        const $grid = $(`#${currentTemplateDiskId}Grid`);
        const $uploadArea = $(`#${currentTemplateDiskId}UploadArea`);
        const $emptyState = $(`#${currentTemplateDiskId}EmptyState`);
        const $loading = $(`#${currentTemplateDiskId}Loading`);

        console.log('Testing if components are ready to hide loading...');

        // AGGRESSIVE DEBUGGING - Let's see what's really happening
        console.log('=== BEFORE HIDE/SHOW OPERATIONS ===');
        console.log(`Grid - jQuery visible: ${$grid.is(':visible')}, CSS display: ${$grid.css('display')}, jQuery length: ${$grid.length}`);
        console.log(`UploadArea - jQuery visible: ${$uploadArea.is(':visible')}, CSS display: ${$uploadArea.css('display')}, jQuery length: ${$uploadArea.length}`);
        console.log(`EmptyState - jQuery visible: ${$emptyState.is(':visible')}, CSS display: ${$emptyState.css('display')}, jQuery length: ${$emptyState.length}`);
        console.log(`Loading - jQuery visible: ${$loading.is(':visible')}, CSS display: ${$loading.css('display')}, jQuery length: ${$loading.length}`);

        // Check if DOM elements exist
        const elementsExist = $grid.length > 0 && $uploadArea.length > 0 && $emptyState.length > 0;

        // Check if we have data or determined what to show
        const hasDataOrState = currentFiles !== null && currentDirectories !== null;

        // Check if any content state should be shown
        const shouldShowContent = currentFiles.length > 0;
        const shouldShowUpload = currentFiles.length === 0 && canUploadToCurrentDisk();
        const shouldShowEmpty = currentFiles.length === 0 && !canUploadToCurrentDisk();

        console.log(`Elements exist: ${elementsExist}, Has data: ${hasDataOrState}`);
        console.log(`Should show - Content: ${shouldShowContent}, Upload: ${shouldShowUpload}, Empty: ${shouldShowEmpty}`);

        if (elementsExist && hasDataOrState) {
            // NUCLEAR APPROACH - Force hide EVERYTHING first
            console.log('=== NUCLEAR HIDING ALL ELEMENTS ===');

            // Hide with multiple methods AND remove all spacing + DISABLE FLEXBOX
            $grid.hide().css({
                'display': 'none !important',
                'visibility': 'hidden',
                'height': '0 !important',
                'min-height': '0 !important',
                'max-height': '0 !important',
                'margin': '0 !important',
                'padding': '0 !important',
                'overflow': 'hidden !important',
                'flex': 'none !important',
                'flex-grow': '0 !important',
                'flex-shrink': '0 !important',
                'flex-basis': '0 !important',
                'align-self': 'auto !important'
            }).removeClass('show active d-flex align-items-center justify-content-center');

            $uploadArea.hide().css({
                'display': 'none !important',
                'visibility': 'hidden',
                'height': '0 !important',
                'min-height': '0 !important',
                'max-height': '0 !important',
                'margin': '0 !important',
                'padding': '0 !important',
                'overflow': 'hidden !important',
                'flex': 'none !important',
                'flex-grow': '0 !important',
                'flex-shrink': '0 !important',
                'flex-basis': '0 !important',
                'align-self': 'auto !important'
            }).removeClass('show active d-flex align-items-center justify-content-center');

            $emptyState.hide().css({
                'display': 'none !important',
                'visibility': 'hidden',
                'height': '0 !important',
                'min-height': '0 !important',
                'max-height': '0 !important',
                'margin': '0 !important',
                'padding': '0 !important',
                'overflow': 'hidden !important',
                'flex': 'none !important',
                'flex-grow': '0 !important',
                'flex-shrink': '0 !important',
                'flex-basis': '0 !important',
                'align-self': 'auto !important'
            }).removeClass('show active d-flex align-items-center justify-content-center');

            $loading.hide().css({
                'display': 'none !important',
                'visibility': 'hidden',
                'height': '0 !important',
                'min-height': '0 !important',
                'max-height': '0 !important',
                'margin': '0 !important',
                'padding': '0 !important',
                'overflow': 'hidden !important',
                'flex': 'none !important',
                'flex-grow': '0 !important',
                'flex-shrink': '0 !important',
                'flex-basis': '0 !important',
                'align-self': 'auto !important'
            }).removeClass('show active d-flex align-items-center justify-content-center');

            console.log('=== AFTER NUCLEAR HIDING ===');
            console.log(`Grid - visible: ${$grid.is(':visible')}, display: ${$grid.css('display')}`);
            console.log(`UploadArea - visible: ${$uploadArea.is(':visible')}, display: ${$uploadArea.css('display')}`);
            console.log(`EmptyState - visible: ${$emptyState.is(':visible')}, display: ${$emptyState.css('display')}`);
            console.log(`Loading - visible: ${$loading.is(':visible')}, display: ${$loading.css('display')}`);

            // Now show ONLY what we want - RESTORE FULL STYLING
            if (shouldShowContent) {
                console.log('=== SHOWING GRID ONLY ===');
                $grid.css({
                    'display': 'block',
                    'visibility': 'visible',
                    'height': 'auto',
                    'min-height': 'auto',
                    'max-height': 'none',
                    'margin': '',
                    'padding': '',
                    'overflow': '',
                    'flex': '',
                    'flex-grow': '',
                    'flex-shrink': '',
                    'flex-basis': '',
                    'align-self': ''
                }).addClass('show active').show();
                console.log('Loading hidden - showing file grid');
            } else if (shouldShowUpload) {
                console.log('=== SHOWING UPLOAD AREA ONLY ===');
                $uploadArea.css({
                    'display': 'block',
                    'visibility': 'visible',
                    'height': 'auto',
                    'min-height': 'auto',
                    'max-height': 'none',
                    'margin': '',
                    'padding': '',
                    'overflow': '',
                    'flex': '',
                    'flex-grow': '',
                    'flex-shrink': '',
                    'flex-basis': '',
                    'align-self': ''
                }).addClass('show active').show();
                console.log('Loading hidden - showing upload area');
            } else if (shouldShowEmpty) {
                console.log('=== SHOWING EMPTY STATE ONLY ===');
                $emptyState.css({
                    'display': 'block',
                    'visibility': 'visible',
                    'height': 'auto',
                    'min-height': 'auto',
                    'max-height': 'none',
                    'margin': '',
                    'padding': '',
                    'overflow': '',
                    'flex': '',
                    'flex-grow': '',
                    'flex-shrink': '',
                    'flex-basis': '',
                    'align-self': ''
                }).addClass('show active').show();
                console.log('Loading hidden - showing empty state');
            }            // Final verification
            setTimeout(() => {
                console.log('=== FINAL VERIFICATION (after 100ms) ===');
                console.log(`Grid - visible: ${$grid.is(':visible')}, display: ${$grid.css('display')}`);
                console.log(`UploadArea - visible: ${$uploadArea.is(':visible')}, display: ${$uploadArea.css('display')}`);
                console.log(`EmptyState - visible: ${$emptyState.is(':visible')}, display: ${$emptyState.css('display')}`);
                console.log(`Loading - visible: ${$loading.is(':visible')}, display: ${$loading.css('display')}`);

                // Count how many are visible
                const visibleCount = [$grid, $uploadArea, $emptyState, $loading].filter(el => el.is(':visible')).length;
                console.log(`TOTAL VISIBLE ELEMENTS: ${visibleCount} (should be 1)`);

                if (visibleCount > 1) {
                    console.error('❌ MULTIPLE ELEMENTS STILL VISIBLE - CSS CONFLICT DETECTED!');
                } else {
                    console.log('✅ SUCCESS - Only one element visible');
                }

                // DEBUG PARENT CONTAINERS AND SPACING ISSUES
                console.log('=== DEBUGGING LAYOUT SPACING ISSUES ===');

                // Find and log parent containers
                const $tabContent = $('.tab-content');
                const $currentTabPane = $(`#${currentTemplateDiskId}`);

                console.log(`Tab content height: ${$tabContent.height()}px, visible: ${$tabContent.is(':visible')}`);
                console.log(`Current tab pane height: ${$currentTabPane.height()}px, visible: ${$currentTabPane.is(':visible')}`);

                // Check for hidden elements that might be taking up space
                const hiddenButSpacing = [];
                [$grid, $uploadArea, $emptyState, $loading].forEach((el, idx) => {
                    const names = ['Grid', 'UploadArea', 'EmptyState', 'Loading'];
                    if (!el.is(':visible') && el.height() > 0) {
                        hiddenButSpacing.push({
                            name: names[idx],
                            height: el.height(),
                            margin: el.css('margin'),
                            padding: el.css('padding')
                        });
                    }
                });

                if (hiddenButSpacing.length > 0) {
                    console.warn('🚨 HIDDEN ELEMENTS STILL TAKING UP SPACE:', hiddenButSpacing);

                    // FORCE REMOVE SPACING FROM HIDDEN ELEMENTS
                    hiddenButSpacing.forEach(info => {
                        const elementName = info.name.toLowerCase();
                        const $element = $(`#${currentTemplateDiskId}${info.name}`);
                        $element.css({
                            'height': '0 !important',
                            'min-height': '0 !important',
                            'max-height': '0 !important',
                            'margin': '0 !important',
                            'padding': '0 !important',
                            'border': 'none !important',
                            'overflow': 'hidden !important'
                        });
                        console.log(`🔧 FORCED ZERO HEIGHT FOR: ${info.name}`);
                    });
                } else {
                    console.log('✅ No spacing issues detected');
                }

                // Also check for any row/column containers that might have spacing
                const $containers = $currentTabPane.find('.row, .col, .container, .card, .card-body');
                console.log(`Found ${$containers.length} potential spacing containers in tab pane`);

                // FORCE ZERO HEIGHT ON FLEXBOX PARENTS OF HIDDEN ELEMENTS
                const $tabPane = $(`#${currentTemplateDiskId}`);
                if ($tabPane.hasClass('h-100') || $tabPane.css('height') !== 'auto') {
                    console.log('🔧 FOUND TAB PANE WITH FIXED HEIGHT - ADJUSTING...');

                    // If only upload area should be visible, make sure tab pane height adjusts
                    if (shouldShowUpload && $uploadArea.is(':visible')) {
                        $tabPane.css({
                            'height': 'auto !important',
                            'min-height': 'auto !important',
                            'display': 'flex !important',
                            'flex-direction': 'column !important',
                            'align-items': 'stretch !important',
                            'justify-content': 'flex-start !important'
                        });
                        console.log('✅ TAB PANE HEIGHT ADJUSTED FOR UPLOAD AREA');
                    }
                }

                // Check for Bootstrap row containers that might be maintaining height
                const $parentRow = $currentTabPane.closest('.row');
                if ($parentRow.length > 0 && $parentRow.hasClass('h-100')) {
                    console.log('🔧 FOUND PARENT ROW WITH h-100 CLASS');
                    $parentRow.css('height', 'auto !important');
                }

                // Log dimensions of containers to identify spacing culprits
                $containers.each(function(i) {
                    const $container = $(this);
                    const classes = $container.attr('class') || 'no-class';
                    const height = $container.height();
                    if (height > 10) { // Only log containers with significant height
                        console.log(`Container ${i}: ${classes} - Height: ${height}px`);

                        // If container has flex classes and is empty, zero it out
                        if ((classes.includes('d-flex') || classes.includes('align-items') || classes.includes('justify-content')) && $container.children(':visible').length === 0) {
                            console.log(`🔧 ZEROING EMPTY FLEX CONTAINER: ${classes}`);
                            $container.css({
                                'height': '0 !important',
                                'min-height': '0 !important',
                                'padding': '0 !important',
                                'margin': '0 !important'
                            });
                        }
                    }
                });            }, 100);

            return true; // Loading successfully hidden
        } else {
            console.log('Components not ready yet - keeping loading state');
            return false; // Keep loading
        }
    }

    /**
     * Role-based access control - check if user can upload to current disk
     */
    function canUploadToCurrentDisk() {
        // This will be properly implemented when we add the full role checking
        // For now, assume user can upload to public disk
        return currentDisk === 'public';
    }

    function bindEventHandlers() {
        console.log('Binding event handlers...');
        // Event handlers will be implemented here
    }

    function checkAllDiskStatuses() {
        console.log('Checking all disk statuses...');
        // Disk status checking will be implemented here
    }

    /**
     * Load files for the current disk and hide loading when ready
     */
    function loadFiles() {
        console.log(`Loading files for disk: ${currentDisk}, path: ${currentPath}`);

        return new Promise((resolve, reject) => {
            // Simulate loading files - replace with actual AJAX call
            setTimeout(() => {
                // Mock file data for testing
                currentFiles = []; // Empty for now to test upload area
                currentDirectories = [];

                console.log(`Found ${currentFiles.length} files and ${currentDirectories.length} directories`);

                // Test if we can hide loading and show appropriate content
                const loadingHidden = testAndHideLoadingWhenReady();

                if (loadingHidden) {
                    resolve({
                        success: true,
                        files: currentFiles,
                        directories: currentDirectories
                    });
                } else {
                    reject(new Error('Components not ready'));
                }
            }, 1000); // 1 second delay to simulate loading
        });
    }
</script>
