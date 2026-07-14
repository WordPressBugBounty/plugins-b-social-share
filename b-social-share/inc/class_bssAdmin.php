<?php
if (!defined('ABSPATH'))
    exit;
if (!class_exists('BSSB_Admin')) {
    class BSSB_Admin
    {
        public function __construct()
        {
            add_action('admin_menu', [$this, 'bssb_social_share_sub_menu']);
            add_filter('manage_social_share_cpt_posts_columns', [$this, 'bssb_set_custom_column_edit']);
            add_action('manage_social_share_cpt_posts_custom_column', [$this, 'bssb_manage_custom_column'], 10, 2);
            add_action('admin_enqueue_scripts', [$this, 'bssb_admin_enqueue_scripts']);
        }
        public function bssb_social_share_sub_menu()
        {
            add_submenu_page(
                'edit.php?post_type=social_share_cpt',
                __('Demo & Help', 'b-social-share'),
                __('Demo & Help', 'b-social-share'),
                'manage_options',
                'social_share_Dashboard',
                [$this, 'bssb_social_share_Dashboard_page']
            );

        }

        public function bssb_social_share_Dashboard_page()
        {
            ?>
            <div id="bssbDashboard" data-info="<?php echo esc_attr(wp_json_encode([
                'version' => BSSB_PLUGIN_VERSION,
                'isPremium' => bssb_fs()->is_premium(),
                'adminUrl' => admin_url(),
                'nonce' => wp_create_nonce('wp_ajax'),
                'licenseActiveNonce' => wp_create_nonce('bssb_license_nonce'),
            ])); ?>">
                <noscript>
                    <div class="notice notice-error">
                        <p><?php esc_html_e('JavaScript is required to load the B Social Share Dashboard.', 'b-social-share'); ?>
                        </p>
                    </div>
                </noscript>
                <p class="bssb-loading-placeholder"><?php esc_html_e('Loading Dashboard...', 'b-social-share'); ?></p>
            </div>
            <?php
        }

        public function bssb_set_custom_column_edit($column)
        {
            unset($column['date']);
            $column['shortcode'] = __('Shortcode', 'b-social-share');
            $column['date'] = __('Date', 'b-social-share');
            return $column;
        }
        public function bssb_manage_custom_column($column_name, $post_id)
        {
            if ($column_name === 'shortcode') {
                printf(
                    '<div class="bssbAdminShortcode" id="bssbAdminShortcode-%1$s">
                        <input value="[social-share id=%1$s]" readonly>
                        <span class="tooltip">%2$s</span>
                    </div>',
                    esc_attr($post_id),
                    esc_html__('Copy to Clipboard', 'b-social-share')
                );
            }
        }

        public function bssb_admin_enqueue_scripts()
        {
            $screen = get_current_screen();

            if ($screen && 'social_share_cpt' === $screen->post_type && in_array($screen->base, ['post', 'edit'], true)) {
                wp_enqueue_script('bssb-shortcode-js', BSSB_DIR_URL . 'build/shortcode.js', ['wp-i18n'], BSSB_PLUGIN_VERSION, true);
                wp_enqueue_style('bssb-shortcode-css', BSSB_DIR_URL . 'build/shortcode.css', [], BSSB_PLUGIN_VERSION);
                wp_set_script_translations('bssb-shortcode-js', 'b-social-share', BSSB_DIR_PATH . 'languages');
            }

            if ($screen && $screen->id === 'social_share_cpt_page_social_share_Dashboard') {
                $asset_file = BSSB_DIR_PATH . 'build/admin-dashboard.asset.php';
                $dependencies = ['wp-util'];
                if (file_exists($asset_file)) {
                    $asset = include $asset_file;
                    if (is_array($asset) && isset($asset['dependencies'])) {
                        $dependencies = array_merge($asset['dependencies'], $dependencies);
                    }
                }
                wp_enqueue_script('bssb-admin-script', BSSB_DIR_URL . 'build/admin-dashboard.js', $dependencies, BSSB_PLUGIN_VERSION, true);
                wp_enqueue_style('bssb-admin-style', BSSB_DIR_URL . 'build/admin-dashboard.css', false, BSSB_PLUGIN_VERSION);
                wp_set_script_translations('bssb-admin-script', 'b-social-share', BSSB_DIR_PATH . 'languages');
            }
        }
    }
}
