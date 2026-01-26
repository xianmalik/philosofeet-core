<?php
/**
 * Polling System for Philosofeet Core
 * 
 * Handles Post Type registration, Admin specific pages, and REST API endpoints.
 * 
 * @package Philosofeet
 */

namespace Philosofeet;

if (!defined('ABSPATH')) {
    exit;
}

class Polling_System {

    private static $_instance = null;

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        add_action('init', [$this, 'register_post_type']);
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post', [$this, 'save_poll_options']);
        add_action('rest_api_init', [$this, 'register_rest_routes']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
    }

    /**
     * Register Custom Post Type 'philosofeet_poll'
     */
    public function register_post_type() {
        $labels = [
            'name'                  => _x('Polls', 'Post Type General Name', 'philosofeet-core'),
            'singular_name'         => _x('Poll', 'Post Type Singular Name', 'philosofeet-core'),
            'menu_name'             => __('Philosofeet Polls', 'philosofeet-core'),
            'name_admin_bar'        => __('Poll', 'philosofeet-core'),
            'archives'              => __('Poll Archives', 'philosofeet-core'),
            'attributes'            => __('Poll Attributes', 'philosofeet-core'),
            'parent_item_colon'     => __('Parent Poll:', 'philosofeet-core'),
            'all_items'             => __('Manage Polls', 'philosofeet-core'),
            'add_new_item'          => __('Add New Poll', 'philosofeet-core'),
            'add_new'               => __('Add New', 'philosofeet-core'),
            'new_item'              => __('New Poll', 'philosofeet-core'),
            'edit_item'             => __('Edit Poll', 'philosofeet-core'),
            'update_item'           => __('Update Poll', 'philosofeet-core'),
            'view_item'             => __('View Poll', 'philosofeet-core'),
            'view_items'            => __('View Polls', 'philosofeet-core'),
            'search_items'          => __('Search Poll', 'philosofeet-core'),
            'not_found'             => __('Not found', 'philosofeet-core'),
            'not_found_in_trash'    => __('Not found in Trash', 'philosofeet-core'),
            'featured_image'        => __('Featured Image', 'philosofeet-core'),
            'set_featured_image'    => __('Set featured image', 'philosofeet-core'),
            'remove_featured_image' => __('Remove featured image', 'philosofeet-core'),
            'use_featured_image'    => __('Use as featured image', 'philosofeet-core'),
            'insert_into_item'      => __('Insert into poll', 'philosofeet-core'),
            'uploaded_to_this_item' => __('Uploaded to this poll', 'philosofeet-core'),
            'items_list'            => __('Polls list', 'philosofeet-core'),
            'items_list_navigation' => __('Polls list navigation', 'philosofeet-core'),
            'filter_items_list'     => __('Filter polls list', 'philosofeet-core'),
        ];
        $args = [
            'label'                 => __('Poll', 'philosofeet-core'),
            'description'           => __('Polls for Philosofeet Widget', 'philosofeet-core'),
            'labels'                => $labels,
            'supports'              => ['title'], // We only need title, options are meta
            'hierarchical'          => false,
            'public'                => false, // Not publicly queryable on frontend as a single page
            'show_ui'               => true,
            'show_in_menu'          => true, // We will customize this momentarily
            'menu_position'         => 50,
            'menu_icon'             => 'dashicons-chart-bar',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'capability_type'       => 'post',
            'show_in_rest'          => true, // Needed for potential Gutenburg or data fetching
        ];
        register_post_type('philosofeet_poll', $args);
    }

    /**
     * Customize Admin Menu
     */
    public function add_admin_menu() {
        // The main menu is already added by register_post_type with 'show_in_menu' => true.
        // However, we want "Manage Polls" and "View Results".
        // "Manage Polls" is the default "All Polls" page.
        // We just need to add "View Results".

        add_submenu_page(
            'edit.php?post_type=philosofeet_poll',
            __('View Results', 'philosofeet-core'),
            __('View Results', 'philosofeet-core'),
            'manage_options',
            'philosofeet-poll-results',
            [$this, 'render_results_page']
        );
    }

    /**
     * Render the Results Page
     */
    public function render_results_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('Poll Results', 'philosofeet-core'); ?></h1>
            <div id="philosofeet-poll-results-app">
                <?php 
                // We'll use a simple PHP loop here for now, or Mount a React App if complex. 
                // Given the requirement "View result page will show poll list and can view individual poll results. show a bar graph for these poll results."
                // I'll render a list of polls. Clicking one shows the results.
                
                $polls = get_posts([
                    'post_type' => 'philosofeet_poll',
                    'numberposts' => -1,
                    'post_status' => 'publish',
                ]);

                if (empty($polls)) {
                    echo '<p>' . esc_html__('No polls found.', 'philosofeet-core') . '</p>';
                } else {
                    echo '<div class="poll-results-container" style="display: flex; gap: 20px;">';
                    
                    // List of Polls
                    echo '<div class="poll-list" style="width: 250px; border-right: 1px solid #ccc; padding-right: 20px;">';
                    echo '<h3>' . esc_html__('Select a Poll', 'philosofeet-core') . '</h3>';
                    echo '<ul>';
                    foreach ($polls as $poll) {
                        $url = add_query_arg(['poll_id' => $poll->ID], menu_page_url('philosofeet-poll-results', false));
                        $active = (isset($_GET['poll_id']) && $_GET['poll_id'] == $poll->ID) ? 'font-weight:bold;' : '';
                        echo '<li><a href="' . esc_url($url) . '" style="' . $active . '">' . esc_html($poll->post_title) . '</a></li>';
                    }
                    echo '</ul>';
                    echo '</div>';

                    // Result Area
                    echo '<div class="poll-graph" style="flex: 1;">';
                    if (isset($_GET['poll_id'])) {
                        $poll_id = intval($_GET['poll_id']);
                        $this->render_individual_poll_result($poll_id);
                    } else {
                        echo '<p>' . esc_html__('Select a poll to view results.', 'philosofeet-core') . '</p>';
                    }
                    echo '</div>';
                    
                    echo '</div>';
                }
                ?>
            </div>
        </div>
        <?php
    }

    private function render_individual_poll_result($poll_id) {
        $poll = get_post($poll_id);
        if (!$poll || $poll->post_type !== 'philosofeet_poll') {
            echo '<p>' . esc_html__('Invalid Poll ID.', 'philosofeet-core') . '</p>';
            return;
        }

        $options = get_post_meta($poll_id, 'philosofeet_poll_options', true);
        if (!is_array($options)) {
            $options = [];
        }

        // Get total votes to calculate percentage
        $total_votes = 0;
        foreach ($options as $opt) {
            $total_votes += isset($opt['votes']) ? intval($opt['votes']) : 0;
        }

        echo '<h2>' . esc_html($poll->post_title) . '</h2>';
        echo '<p>' . sprintf(esc_html__('Total Votes: %d', 'philosofeet-core'), $total_votes) . '</p>';
        echo '<div class="poll-bars">';
        
        foreach ($options as $opt) {
            $votes = isset($opt['votes']) ? intval($opt['votes']) : 0;
            $percentage = $total_votes > 0 ? round(($votes / $total_votes) * 100, 1) : 0;
            $label = isset($opt['label']) ? $opt['label'] : '';
            
            echo '<div style="margin-bottom: 15px;">';
            echo '<div style="display:flex; justify-content:space-between; margin-bottom: 5px;">';
            echo '<strong>' . esc_html($label) . '</strong>';
            echo '<span>' . esc_html($votes) . ' (' . esc_html($percentage) . '%)</span>';
            echo '</div>';
            echo '<div style="background: #f0f0f1; height: 20px; border-radius: 4px; overflow: hidden;">';
            echo '<div style="background: #2271b1; height: 100%; width: ' . esc_attr($percentage) . '%;"></div>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    }

    /**
     * Add Meta Boxes
     */
    public function add_meta_boxes() {
        add_meta_box(
            'philosofeet_poll_options_box',
            __('Poll Options', 'philosofeet-core'),
            [$this, 'render_options_meta_box'],
            'philosofeet_poll',
            'normal',
            'high'
        );
    }

    /**
     * Render Options Meta Box
     */
    public function render_options_meta_box($post) {
        // Enqueue admin script for dynamic fields if needed, or inline JS for simplicity
        $options = get_post_meta($post->ID, 'philosofeet_poll_options', true);
        if (!is_array($options)) {
            $options = [['label' => '', 'votes' => 0]];
        }
        
        wp_nonce_field('philosofeet_poll_save', 'philosofeet_poll_nonce');
        ?>
        <div id="philosofeet-poll-options-wrapper">
            <?php foreach ($options as $index => $option) : ?>
                <div class="poll-option-row" style="margin-bottom: 10px; display: flex; gap: 10px; align-items: center;">
                    <input type="text" name="philosofeet_poll_options[<?php echo $index; ?>][label]" value="<?php echo esc_attr($option['label']); ?>" placeholder="<?php esc_attr_e('Option Label', 'philosofeet-core'); ?>" style="width: 300px;">
                    <input type="hidden" name="philosofeet_poll_options[<?php echo $index; ?>][votes]" value="<?php echo esc_attr(isset($option['votes']) ? $option['votes'] : 0); ?>">
                    <span class="vote-count" style="color: #666;"><?php echo isset($option['votes']) ? $option['votes'] : 0; ?> votes</span>
                    <button type="button" class="button remove-poll-option">x</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="button button-primary" id="add-poll-option"><?php esc_html_e('Add Option', 'philosofeet-core'); ?></button>

        <script>
        jQuery(document).ready(function($) {
            $('#add-poll-option').click(function() {
                var count = $('.poll-option-row').length;
                var html = `
                    <div class="poll-option-row" style="margin-bottom: 10px; display: flex; gap: 10px; align-items: center;">
                        <input type="text" name="philosofeet_poll_options[${count}][label]" value="" placeholder="Option Label" style="width: 300px;">
                        <input type="hidden" name="philosofeet_poll_options[${count}][votes]" value="0">
                        <span class="vote-count" style="color: #666;">0 votes</span>
                        <button type="button" class="button remove-poll-option">x</button>
                    </div>
                `;
                $('#philosofeet-poll-options-wrapper').append(html);
            });

            $(document).on('click', '.remove-poll-option', function() {
                $(this).closest('.poll-option-row').remove();
            });
        });
        </script>
        <?php
    }

    /**
     * Save Meta Box Data
     */
    public function save_poll_options($post_id) {
        if (!isset($_POST['philosofeet_poll_nonce']) || !wp_verify_nonce($_POST['philosofeet_poll_nonce'], 'philosofeet_poll_save')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['philosofeet_poll_options']) && is_array($_POST['philosofeet_poll_options'])) {
            $options = array_values($_POST['philosofeet_poll_options']); // Re-index array
            // Sanitize
            $clean_options = [];
            foreach ($options as $opt) {
                if (!empty($opt['label'])) {
                    $clean_options[] = [
                        'label' => sanitize_text_field($opt['label']),
                        'votes' => isset($opt['votes']) ? intval($opt['votes']) : 0
                    ];
                }
            }
            update_post_meta($post_id, 'philosofeet_poll_options', $clean_options);
        }
    }

    /**
     * Register REST API Routes
     */
    public function register_rest_routes() {
        register_rest_route('philosofeet/v1', '/vote', [
            'methods' => 'POST',
            'callback' => [$this, 'handle_vote'],
            'permission_callback' => '__return_true', // Public endpoint
        ]);
        
        register_rest_route('philosofeet/v1', '/poll/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'get_poll_data'],
            'permission_callback' => '__return_true',
        ]);
    }

    /**
     * Handle Vote REST Request
     */
    public function handle_vote($request) {
        $poll_id = $request->get_param('poll_id');
        $option_index = $request->get_param('option_index');

        if (!$poll_id || $option_index === null) {
            return new \WP_Error('invalid_params', 'Missing poll_id or option_index', ['status' => 400]);
        }

        $options = get_post_meta($poll_id, 'philosofeet_poll_options', true);
        if (!$options || !isset($options[$option_index])) {
            return new \WP_Error('invalid_option', 'Option not found', ['status' => 404]);
        }

        // Increment vote
        $options[$option_index]['votes'] = isset($options[$option_index]['votes']) ? intval($options[$option_index]['votes']) + 1 : 1;
        
        update_post_meta($poll_id, 'philosofeet_poll_options', $options);

        // Optionally store cookie to prevent re-vote (simple protection)
        // Since this is a REST API call, the cookie setting might need to be handled by the frontend or response headers.
        
        return rest_ensure_response([
            'success' => true,
            'message' => 'Vote recorded',
            'results' => $options // Return updated results so frontend can show them immediately if desired
        ]);
    }
    
    /**
     * Get Poll Data via REST
     */
    public function get_poll_data($request) {
        $poll_id = $request->get_param('id');
        $poll = get_post($poll_id);
        
        if (!$poll || $poll->post_type !== 'philosofeet_poll') {
            return new \WP_Error('not_found', 'Poll not found', ['status' => 404]);
        }
        
        $options = get_post_meta($poll_id, 'philosofeet_poll_options', true);
        if (!is_array($options)) {
            $options = [];
        }
        
        return rest_ensure_response([
            'id' => $poll->ID,
            'title' => $poll->post_title,
            'options' => $options
        ]);
    }

    public function enqueue_admin_scripts() {
        // Enqueue generic styles if needed
    }
}

new Polling_System();
