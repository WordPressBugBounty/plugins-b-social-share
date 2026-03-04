<?php

if (!class_exists('bssPlugin')) {
    class bssPlugin
    {
        public function __construct()
        {
            add_action('init', [$this, 'bss_register_block']);
            add_action('plugins_loaded', [$this, 'bss_plugins_dependency']);
            add_action('enqueue_block_assets', [$this, 'bss_enqueue_block_assets']);
            add_action('admin_enqueue_scripts', [$this, 'bss_admin_enqueue_scripts']);
            add_action('enqueue_block_editor_assets', [$this, "bssbEnqueueEditorAssets"]);
            add_action('enqueue_block_assets', [$this, "bssbEnqueueFrontendAssets"]);
            add_shortcode('social-share', [$this, 'bss_shortcode_handler']);
            add_action('wp_enqueue_scripts', [$this, 'bss_frontend_enqueue_block_assets']);
        }

        public function bss_register_block()
        {
            register_block_type(BSSB_DIR_PATH . '/build');

            wp_set_script_translations('bssb-social-share-editor-script', 'social-share', BSSB_DIR_PATH . 'languages');
        }

        public function bss_plugins_dependency()
        {
            require_once BSSB_DIR_PATH . 'inc/function.php';
            require_once BSSB_DIR_PATH . 'inc/class_bssAdmin.php';
        }
        public function bssbEnqueueEditorAssets()
        {
            wp_add_inline_script(
                'bssb-social-share-editor-script',
                'const bssbIsPipeChecker = ' . wp_json_encode(bssbIsPremium()) . ';',
                'before'
            );

        }
        public function bssbEnqueueFrontendAssets()
        {
            wp_add_inline_script(
                'bssb-social-share-view-script',
                'const bssbIsPipeChecker = ' . wp_json_encode(bssbIsPremium()) . ';',
                'before'
            );
        }

        // Enqueue frontend + editor assets
        public function bss_enqueue_block_assets()
        {
            wp_enqueue_style('font-awesome-7', BSSB_DIR_URL . 'assets/css/font-awesome.min.css', [], '7.1.0');
        }
        public function bss_frontend_enqueue_block_assets()
        {
            if (!is_admin()) {
                wp_enqueue_script('goodshare', BSSB_DIR_URL . 'assets/js/goodshare.min.js', [], BSSB_PLUGIN_VERSION, true);
            }
        }
        public function bss_admin_enqueue_scripts($screen)
        {

            global $typenow;
            if ('social_share_cpt' === $typenow) {
                wp_enqueue_script('shortcode-js', BSSB_DIR_URL . '/build/shortcode.js', [], BSSB_PLUGIN_VERSION, true);
                wp_enqueue_style('shortcode-css', BSSB_DIR_URL . '/build/shortcode.css', [], BSSB_PLUGIN_VERSION);
            }

            if ('social_share_cpt_page_social_share_Dashboard' === $screen) {
                $asset = include BSSB_DIR_PATH . 'build/admin-dashboard.asset.php';
                wp_enqueue_script('vgb-admin-script', BSSB_DIR_URL . 'build/admin-dashboard.js', array_merge($asset['dependencies'], ['wp-util']), BSSB_PLUGIN_VERSION, true);

                wp_enqueue_style('vgb-admin-style', BSSB_DIR_URL . '/build/admin-dashboard.css', false, BSSB_PLUGIN_VERSION);
            }

        }
        public function bss_shortcode_handler($atts)
        {
            if (!isset($atts['id'])) {
                return '<p>Please provide a valid ID.</p>';
            }
            $post = get_post($atts['id']);
            if ($post) {
                $blocks = parse_blocks($post->post_content);
                if (!empty($blocks)) {
                    foreach ($blocks as $block) {
                        return render_block($block);
                    }
                }
            } else {
                return '<p>Error: Social Share block with ID ' . esc_html($atts['id']) . ' not found.</p>';
            }
        }
    }
}