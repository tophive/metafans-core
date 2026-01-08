<?php
class Tophive_Builder_Admin {
    public function __construct() {
        add_action('init', [$this, 'register_custom_post_types']);
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_menu', [$this, 'remove_revision_metabox']);
    }

    public function register_custom_post_types() {
        $this->register_post_type('tophive-header', __('Header', 'textdomain'));
        $this->register_post_type('tophive-footer', __('Footer', 'textdomain'));
        $this->register_post_type('tophive-forms', __('Forms', 'textdomain'));
    }

    private function register_post_type($post_type, $name) {
        $args = [
            'label' => $name,
            'public' => true,
            'has_archive' => true,
            'supports' => ['title', 'editor', 'thumbnail'], // Removed 'revisions'
            'menu_position' => 5,
            'show_in_rest' => true,
        ];
        register_post_type($post_type, $args);
    }

    public function add_admin_menu() {
        add_menu_page(
            __('Tophive Builder', 'textdomain'),
            __('Tophive Builder', 'textdomain'),
            'manage_options',
            'tophive-builder',
            [$this, 'render_admin_page'],
            'dashicons-layout',
            5
        );
    }

    public function render_admin_page() {
        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'header'; // Default to 'header' tab

        echo '<div class="wrap">';
        echo '<h1>' . esc_html(__('Tophive Builder', 'textdomain')) . '</h1>';
        
        // Tab navigation
        echo '<h2 class="nav-tab-wrapper">';
        $tabs = [
            'header' => __('Header', 'textdomain'),
            'footer' => __('Footer', 'textdomain'),
            'forms' => __('Forms', 'textdomain'),
        ];
        foreach ($tabs as $tab => $name) {
            $active_class = ($active_tab === $tab) ? ' nav-tab-active' : '';
            echo '<a class="nav-tab' . esc_attr($active_class) . '" href="' . esc_url(add_query_arg('tab', $tab)) . '">' . esc_html($name) . '</a>';
        }
        echo '</h2>';

        // Section title
        echo '<h2>' . esc_html(ucfirst($active_tab)) . ' <a href="' . esc_url(admin_url('post-new.php?post_type=' . $this->get_post_type($active_tab))) . '" class="page-title-action">' . __('Add New', 'textdomain') . '</a></h2>';


        // Render the appropriate list table based on the active tab
        switch ($active_tab) {
            case 'footer':
                $list_table = new Custom_Post_Type_List_Table('tophive-footer');
                break;
            case 'forms':
                $list_table = new Custom_Post_Type_List_Table('tophive-forms');
                break;
            case 'header':
            default:
                $list_table = new Custom_Post_Type_List_Table('tophive-header');
                break;
        }

        $list_table->prepare_items();

        // Wrap the list table display in a form
        echo '<form id="posts-filter" method="get">';
        echo '<input type="hidden" name="page" value="' . esc_attr($_REQUEST['page']) . '" />';
        $list_table->display();
        echo '</form>';

        echo '</div>';
    }

    // Get post type based on the active tab
    private function get_post_type($tab) {
        switch ($tab) {
            case 'footer':
                return 'tophive-footer';
            case 'forms':
                return 'tophive-forms';
            case 'header':
            default:
                return 'tophive-header';
        }
    }

    // Remove the revisions metabox
    public function remove_revision_metabox() {
        // Remove the revisions metabox from each post type
        remove_meta_box('revisionsdiv', 'tophive-header', 'normal');
        remove_meta_box('revisionsdiv', 'tophive-footer', 'normal');
    }
}

// Initialize the admin class
new Tophive_Builder_Admin();


// Function to display the "Add New" button
function display_add_new_button($post_type) {
    ?>
    <a href="<?php echo admin_url('post-new.php?post_type=' . $post_type); ?>" class="page-title-action">
        <?php esc_html_e('Add New', 'textdomain'); ?>
    </a>
    <?php
}

// Include WP_List_Table if not already included
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
class Custom_Post_Type_List_Table extends WP_List_Table {
    private $post_type;
    private $notices = [];

    public function __construct($post_type) {
        parent::__construct();
        $this->post_type = $post_type;
        $this->prepare_items();
    }

    public function get_columns() {
        return [
            'cb' => '<input type="checkbox" />',
            'title' => __('Title', 'textdomain'),
            'date' => __('Date', 'textdomain'),
        ];
    }

    public function get_bulk_actions() {
        $current_status = isset($_GET['post_status']) ? $_GET['post_status'] : '';

        // Define bulk actions based on current status
        $bulk_actions = [];
        
        switch ($current_status) {
            case 'draft':
                $bulk_actions['publish'] = __('Publish', 'textdomain');
                break;
            case 'trash':
                $bulk_actions['restore'] = __('Restore', 'textdomain');
                $bulk_actions['delete'] = __('Delete Permanently', 'textdomain');
                break;
            case '':
            case 'publish':
            default:
                $bulk_actions['trash'] = __('Move to Trash', 'textdomain');
                break;
        }

        return $bulk_actions;
    }

    public function process_bulk_action() {
        if (!isset($_GET['action']) || !isset($_GET['post'])) {
            return; // No action or no posts selected
        }
    
        $post_ids = isset($_GET['post']) ? (array) $_GET['post'] : [];
        
        try {
            switch ($this->current_action()) {
                case 'trash':
                    foreach ($post_ids as $post_id) {
                        wp_trash_post($post_id);
                    }
                    $this->set_notice(__('Selected posts have been moved to trash.', 'textdomain'));
                    break;
    
                case 'delete':
                    foreach ($post_ids as $post_id) {
                        wp_delete_post($post_id, true);
                    }
                    $this->set_notice(__('Selected posts have been permanently deleted.', 'textdomain'));
                    break;
    
                case 'publish':
                    foreach ($post_ids as $post_id) {
                        wp_update_post(['ID' => $post_id, 'post_status' => 'publish']);
                    }
                    $this->set_notice(__('Selected drafts have been published.', 'textdomain'));
                    break;
    
                case 'restore':
                    foreach ($post_ids as $post_id) {
                        wp_update_post(['ID' => $post_id, 'post_status' => 'draft']);
                    }
                    $this->set_notice(__('Selected posts have been restored to drafts.', 'textdomain'));
                    break;
            }
    
            // Redirect to avoid resubmission
            wp_redirect(remove_query_arg(['action', 'post', 'post_status'], $_GET['_wp_http_referer']));
            exit;
        } catch (Exception $e) {
            error_log($e->getMessage());
            wp_die(__('An error occurred while processing the bulk action. Please try again.', 'textdomain'));
        }
    }
    
    private function set_notice($message) {
        // Store notice in a transient
        set_transient('custom_post_notices', $message, 30); // Notice will expire in 30 seconds
    }
    
    
    public function display_notices() {
        $notice = get_transient('custom_post_notices');
        if ($notice) {
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($notice) . '</p></div>';
            delete_transient('custom_post_notices'); // Clear the transient after displaying
        }
    }
    

    public function prepare_items() {
        $this->process_bulk_action();
        $this->_column_headers = [
            $this->get_columns(),
            [], // hidden columns
            $this->get_sortable_columns(),
            $this->get_primary_column_name(),
        ];
        $args = [
            'post_type' => $this->post_type,
            'posts_per_page' => -1,
            'post_status' => 'any',
        ];
        // Check for selected status and adjust the query
        if (!empty($_REQUEST['post_status'])) {
            $post_status = sanitize_text_field($_REQUEST['post_status']);
            $args['post_status'] = $post_status;
        }

        if (!empty($_REQUEST['s'])) {
            $args['s'] = sanitize_text_field($_REQUEST['s']);
        }

        $query = new WP_Query($args);
        $this->items = $query->posts;

        $this->set_pagination_args([
            'total_items' => $query->found_posts,
            'per_page' => 20,
        ]);
    }

    public function column_cb($item) {
        return sprintf('<input type="checkbox" name="post[]" value="%s" />', $item->ID);
    }

    public function column_title($item) {
        $edit_link = get_edit_post_link($item->ID);
        $title = sprintf(
            '<strong><a href="%s">%s</a>%s</strong>', 
            esc_url($edit_link), 
            esc_html($item->post_title),
            $item->post_status === 'draft' ? "<span> — ". __('Draft', 'textdomain')."</span>" : ""
        );

        $actions = $this->row_actions($this->get_row_actions($item));
        return $title . $actions; // Add actions below title
    }

    public function column_date($item) {
        return esc_html(get_the_date('', $item->ID));
    }

    public function no_items() {
        echo '<p>' . __('No items found.', 'textdomain') . '</p>';
    }

    public function get_row_actions($post) {
        $post_type_object = get_post_type_object($post->post_type);
        $actions = [
            'edit' => sprintf('<a href="%s">%s</a>', esc_url(get_edit_post_link($post->ID)), __('Edit', 'textdomain')),
            'trash' => sprintf('<a href="%s" class="submitdelete">%s</a>', esc_url(get_delete_post_link($post->ID)), __('Trash', 'textdomain')),
            'view' => sprintf('<a href="%s" target="_blank">%s</a>', esc_url(get_permalink($post->ID)), __('View', 'textdomain')),
        ];
        
        if( $post->post_status == 'draft' ){
            $actions = [
                'edit' => sprintf('<a href="%s">%s</a>', esc_url(get_edit_post_link($post->ID)), __('Edit', 'textdomain')),
                'trash' => sprintf('<a href="%s" class="submitdelete">%s</a>', esc_url(get_delete_post_link($post->ID)), __('Trash', 'textdomain')),
                'view' => sprintf('<a href="%s" target="_blank">%s</a>', esc_url(get_permalink($post->ID)), __('Preview', 'textdomain')),
            ];
        }
        
        
        if( $post->post_status == 'trash' ){
            $actions = [
                'untrash' => sprintf(
					'<a href="%s">%s</a>',
					wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $post->ID ) ), 'untrash-post_' . $post->ID ),
					__( 'Restore' )
				),
                'delete' => sprintf('<span class="delete"><a href="%s" class="submitdelete">%s</a></span>', esc_url(get_delete_post_link($post->ID, '', true)), __('Delete Permanently', 'textdomain')),
            ];
        }

        return $actions;
    }

    public function display() {
        echo $this->extra_tablenav('top');
        $this->search_box(__('Search', 'textdomain'), 'search');
        parent::display();
    }
    public function extra_tablenav($which) {
    if ($which === 'top') {
        $current_status = isset($_GET['post_status']) ? $_GET['post_status'] : '';

        // Get post counts
        $counts = wp_count_posts($this->post_type);

        // Define statuses
        $statuses = [
            '' => __('All', 'textdomain'),
            'publish' => __('Published', 'textdomain'),
            'draft' => __('Draft', 'textdomain'),
            'pending' => __('Pending', 'textdomain'),
            'trash' => __('Trash', 'textdomain'),
        ];

        $output = '<div class="alignleft actions">';
        $output .= '<ul class="subsubsub">';

        $first_item = true; // Flag to check if it's the first item

        foreach ($statuses as $status => $label) {
            $count = isset($counts->$status) ? $counts->$status : 0; // Safe access to count
            
            // Only show the menu item if count is greater than 0 (except for 'All')
            if ($count > 0 || $status === '') {
                if (!$first_item) {
                    $output .= ' | '; // Add separator before subsequent items
                }
                
                $class = ($status === $current_status) ? 'current' : '';
                $count_display = $count > 0 ? " <span class='count'>($count)</span>" : '';

                $output .= sprintf(
                    '<li class="%s"><a href="%s">%s%s</a>',
                    esc_attr($class),
                    esc_url(add_query_arg('post_status', $status)),
                    esc_html($label),
                    $count_display
                );

                $first_item = false; // After the first item, set the flag to false
            }
        }

        $output .= '</ul>';
        $output .= '</div>';

        return $output; // Return the constructed output
    }
}

    
}
