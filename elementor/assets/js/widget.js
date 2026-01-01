jQuery(document).ready(function($) {
    function refreshElementorUI(model) {
        let checkInterval = setInterval(function() {
            let editorView = elementor.getPanelView();
            if (editorView && editorView.currentPageView) {
                console.log("Forcing Elementor UI Refresh...");
                editorView.currentPageView.render();
                clearInterval(checkInterval);
            } else {
                console.warn("Elementor UI not ready, retrying...");
            }
        }, 200);

        // **Stop checking after 5 seconds to avoid infinite loop**
        setTimeout(() => clearInterval(checkInterval), 5000);
    }

    // **Ensure Initial Rendering on Page Load**
    elementor.hooks.addAction('panel/open_editor/widget', function(panel, model) {
        console.log("Elementor Widget Editor Opened");

        // **Trigger rendering on initial load**
        refreshElementorUI(model);

        panel.$el.on('change', '.elementor-control-selected_menu select', function() {
            var selectedMenuId = $(this).val();
            console.log("Selected Menu ID: ", selectedMenuId);

            if (!selectedMenuId) return;

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'get_menu_items',
                    menu_id: selectedMenuId,
                },
                success: function(response) {
                    console.log("AJAX Response: ", response);

                    if (response.success) {
                        let menuItems = response.data;

                        console.log("Setting Menu Items in Elementor: ", menuItems);

                        let settings = model.get('settings');
                        if (settings) {
                            settings.set('menu_items', JSON.parse(JSON.stringify(menuItems)));
                            model.trigger('change:menu_items');

                            // **Force UI refresh after setting the menu**
                            refreshElementorUI(model);
                        }
                    } else {
                        console.error("AJAX Error: ", response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Failed: ", error);
                }
            });
        });
    });
});
