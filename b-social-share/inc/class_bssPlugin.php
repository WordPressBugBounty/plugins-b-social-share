<?php
if (!defined('ABSPATH')) exit;
if (!class_exists('BSSB_Plugin')) {
    class BSSB_Plugin
    {
        public function __construct()
        {
            add_action('init', [$this, 'bssb_load_textdomain']);
            add_action('init', [$this, 'bssb_register_block']);
            add_action('init', [__CLASS__, 'bssb_social_share_block_post_type']);
            add_action('plugins_loaded', [$this, 'bssb_plugins_dependency']);
            add_shortcode('social-share', [$this, 'bssb_shortcode_handler']);
            add_action('save_post_social_share_cpt', [$this, 'bssb_clear_shortcode_cache'], 10, 1);
            add_action('wp_enqueue_scripts', [$this, 'bssb_enqueue_shortcode_assets']);
        }

        public function bssb_load_textdomain()
        {
            load_plugin_textdomain(
                'b-social-share',
                false,
                plugin_basename(dirname(__DIR__)) . '/languages'
            );
        }

        public static function bssb_social_share_block_post_type()
        {
            register_post_type('social_share_cpt', [
                'label' => __('B Social Share', 'b-social-share'),
                'description' => __('This is a Social Share and SEO-friendly card', 'b-social-share'),
                'labels' => [
                    'name' => __('B Social Share', 'b-social-share'),
                    'singular_name' => __('Social Share', 'b-social-share'),
                    'menu_name' => __('B Social Share', 'b-social-share'),
                    'all_items' => __('All Social Shares', 'b-social-share'),
                    'add_new' => __('Add New', 'b-social-share'),
                    'add_new_item' => __('Add New Social Share', 'b-social-share'),
                    'edit_item' => __('Edit Social Share', 'b-social-share'),
                    'new_item' => __('New Social Share', 'b-social-share'),
                    'view_item' => __('View Social Share', 'b-social-share'),
                    'view_items' => __('View Social Shares', 'b-social-share'),
                    'search_items' => __('Search Social Share', 'b-social-share'),
                    'not_found' => __('Sorry, we couldn\'t find the ShortCode you are looking for.', 'b-social-share'),
                    'parent_item' => __('Parent Social Share', 'b-social-share'),
                    'parent_item_colon' => __('Parent Social Share:', 'b-social-share'),
                    'update_item' => __('Update Social Share', 'b-social-share'),
                ],
                'public' => false,
                'publicly_queryable' => false,
                'show_ui' => true,
                'capability_type' => 'post',
                'map_meta_cap' => true,
                'show_in_rest' => true,
                'rest_base' => 'social_share_cpt',
                'rest_namespace' => 'bssb/v1',
                'menu_position' => 20,
                'menu_icon' => 'dashicons-share',
                'template' => [['bssb/social-share']],
                'template_lock' => 'all',
                'hierarchical' => false,
                'has_archive' => false,
                'rewrite' => false,
            ]);
        }

        public function bssb_register_block()
        {
            wp_register_style('bssb-font-awesome', BSSB_DIR_URL . 'assets/css/font-awesome.min.css', [], BSSB_PLUGIN_VERSION);
            register_block_type(BSSB_DIR_PATH . 'build');
            wp_set_script_translations('bssb-social-share-editor-script', 'b-social-share', BSSB_DIR_PATH . 'languages');
            wp_set_script_translations('bssb-social-share-view-script', 'b-social-share', BSSB_DIR_PATH . 'languages');
        }
        public function bssb_plugins_dependency()
        {
            if (is_admin()) {
                require_once BSSB_DIR_PATH . 'inc/class_bssAdmin.php';
                new BSSB_Admin();
            }
        }

        public function bssb_enqueue_shortcode_assets()
        {
            global $post;
            if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'social-share')) {
                wp_enqueue_style('bssb-font-awesome');
                wp_enqueue_style('bssb-social-share-style');
                wp_enqueue_script('bssb-social-share-view-script');
            }
        }

        public function bssb_shortcode_handler($atts)
        {
            wp_enqueue_style('bssb-font-awesome');
            wp_enqueue_style('bssb-social-share-style');
            wp_enqueue_script('bssb-social-share-view-script');

            $atts = shortcode_atts(
                array(
                    'id' => '',
                ),
                $atts,
                'social-share'
            );

            $bssb_post_id = absint($atts['id']);
            if ($bssb_post_id < 1) {
                return '<p>' . esc_html__('Please provide a valid numeric ID.', 'b-social-share') . '</p>';
            }

            $cache_key = 'bssb_shortcode_' . $bssb_post_id;
            $output = get_transient($cache_key);
            if (false !== $output) {
                return $output;
            }

            $post = get_post($bssb_post_id);
            
            if (!$post || $post->post_type !== 'social_share_cpt' || $post->post_status !== 'publish') {
                $output = '';
            } else {
                $blocks = parse_blocks($post->post_content);
                $found = false;
                if (!empty($blocks)) {
                    foreach ($blocks as $block) {
                        if (isset($block['blockName']) && $block['blockName'] === 'bssb/social-share') {
                            $output = render_block($block);
                            $found = true;
                            break;
                        }
                    }
                }
                if (!$found) {
                    $output = '<p>' . sprintf(esc_html__('Error: Social Share block with ID %d not found.', 'b-social-share'), $bssb_post_id) . '</p>';
                }
            }

            set_transient($cache_key, $output, HOUR_IN_SECONDS);
            return $output;
        }

        public function bssb_clear_shortcode_cache($post_id)
        {
            delete_transient('bssb_shortcode_' . $post_id);
         }
    }
}
