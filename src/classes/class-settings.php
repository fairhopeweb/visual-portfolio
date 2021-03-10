<?php
/**
 * Plugin Settings
 *
 * @package @@plugin_name
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once visual_portfolio()->plugin_path . 'vendors/class-settings-api.php';

/**
 * Visual Portfolio Settings Class
 */
class Visual_Portfolio_Settings {
    /**
     * Settings API instance
     *
     * @var object
     */
    public static $settings_api;

    /**
     * Visual_Portfolio_Settings constructor.
     */
    public function __construct() {
        self::init_actions();
    }

    /**
     * Get Option Value
     *
     * @param string $option - option name.
     * @param string $section - section name.
     * @param string $deprecated_default - default option value.
     *
     * @return bool|string
     */
    public static function get_option( $option, $section, $deprecated_default = '' ) {
        $options = get_option( $section );
        $result  = '';

        if ( isset( $options[ $option ] ) ) {
            $result = $options[ $option ];
        } else {
            // find default.
            $fields = self::get_settings_fields();

            if ( isset( $fields[ $section ] ) && is_array( $fields[ $section ] ) ) {
                foreach ( $fields[ $section ] as $field_data ) {
                    if ( $option === $field_data['name'] && isset( $field_data['default'] ) ) {
                        $result = $field_data['default'];
                    }
                }
            }
        }

        return 'off' === $result ? false : ( 'on' === $result ? true : $result );
    }

    /**
     * Update Option Value
     *
     * @param string $option - option name.
     * @param string $section - section name.
     * @param string $value - new option value.
     */
    public static function update_option( $option, $section, $value ) {
        $options = get_option( $section );

        if ( ! is_array( $options ) ) {
            $options = array();
        }

        $options[ $option ] = $value;

        update_option( $section, $options );
    }

    /**
     * Init actions
     */
    public static function init_actions() {
        self::$settings_api = new Visual_Portfolio_Settings_API();

        add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );
        add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ), 11 );
    }

    /**
     * Initialize the settings
     *
     * @return void
     */
    public static function admin_init() {
        // set the settings.
        self::$settings_api->set_sections( self::get_settings_sections() );
        self::$settings_api->set_fields( self::get_settings_fields() );

        // initialize settings.
        self::$settings_api->admin_init();
    }

    /**
     * Register the admin settings menu
     *
     * @return void
     */
    public static function admin_menu() {
        add_submenu_page(
            'edit.php?post_type=portfolio',
            esc_html__( 'Settings', '@@text_domain' ),
            esc_html__( 'Settings', '@@text_domain' ),
            'manage_options',
            'visual-portfolio-settings',
            array( __CLASS__, 'print_settings_page' )
        );
    }

    /**
     * Plugin settings sections
     *
     * @return array
     */
    public static function get_settings_sections() {
        $sections = array(
            array(
                'id'    => 'vp_general',
                'title' => esc_html__( 'General', '@@text_domain' ),
                'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" /></svg>',
            ),
            array(
                'id'    => 'vp_images',
                'title' => esc_html__( 'Images', '@@text_domain' ),
                'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>',
            ),
            array(
                'id'    => 'vp_popup_gallery',
                'title' => esc_html__( 'Popup Gallery', '@@text_domain' ),
                'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>',
            ),
            array(
                'id'    => 'vp_watermarks',
                'title' => esc_html__( 'Watermarks', '@@text_domain' ),
                'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>',
            ),
            array(
                'id'    => 'vp_social_integrations',
                'title' => esc_html__( 'Social Integrations', '@@text_domain' ),
                'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" /></svg>',
            ),
            array(
                'id'    => 'vp_white_label',
                'title' => esc_html__( 'White Label', '@@text_domain' ),
                'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" /></svg>',
            ),
        );

        return apply_filters( 'vpf_settings_sections', $sections );
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    public static function get_settings_fields() {
        $default_breakpoints = Visual_Portfolio_Breakpoints::get_default_breakpoints();

        $settings_fields = array(
            'vp_general' => array(
                array(
                    'name'    => 'portfolio_slug',
                    'label'   => esc_html__( 'Portfolio Slug', '@@text_domain' ),
                    'type'    => 'text',
                    'default' => 'portfolio',
                ),
                array(
                    'name'    => 'filter_taxonomies',
                    'label'   => esc_html__( 'Filter Taxonomies', '@@text_domain' ),
                    'desc'    => esc_html__( 'You can show custom taxonomies in the portfolio Filter. Enter some taxonomies by "," separating values. Example: "product_cat,product_tag"', '@@text_domain' ),
                    'type'    => 'text',
                    'default' => '',
                ),
                array(
                    'name'    => 'no_image',
                    'label'   => esc_html__( 'No Image', '@@text_domain' ),
                    'desc'    => esc_html__( 'This image used if the featured image of a post is not specified.', '@@text_domain' ),
                    'type'    => 'image',
                    'default' => '',
                    'options' => array(
                        'button_label' => esc_html__( 'Choose image', '@@text_domain' ),
                    ),
                ),

                // AJAX Caching and Preloading.
                array(
                    'name'    => 'ajax_caching',
                    'label'   => esc_html__( 'AJAX Cache and Preload', '@@text_domain' ),
                    'desc'    => esc_html__( 'Reduce AJAX calls request time.', '@@text_domain' ),
                    'type'    => 'toggle',
                    'default' => ! class_exists( 'Visual_Portfolio_Pro' ) ? 'off' : 'on',
                    'is_pro'  => true,
                ),

                // Breakpoints.
                array(
                    'name'   => 'breakpoints_title',
                    'label'  => esc_html__( 'Responsive Breakpoints', '@@text_domain' ),
                    'type'   => 'section_title',
                    'is_pro' => true,
                ),
                array(
                    'name'        => 'breakpoint_xl',
                    'label'       => esc_html__( 'Extra Large', '@@text_domain' ),
                    'type'        => 'number',
                    'min'         => (float) $default_breakpoints['lg'] + 1,
                    'max'         => 3840,
                    'placeholder' => (string) $default_breakpoints['xl'],
                    'default'     => (float) $default_breakpoints['xl'],
                    // translators: %1$s - default breakpoint.
                    'desc'        => sprintf( wp_kses_post( __( 'Sets the breakpoint on extra large screen sizes (Default: %1$spx).', '@@text_domain' ) ), $default_breakpoints['xl'] ),
                    'is_pro'      => true,
                ),
                array(
                    'name'        => 'breakpoint_lg',
                    'label'       => esc_html__( 'Large', '@@text_domain' ),
                    'type'        => 'number',
                    'min'         => (float) $default_breakpoints['md'] + 1,
                    'max'         => (float) $default_breakpoints['xl'] - 1,
                    'placeholder' => (string) $default_breakpoints['lg'],
                    'default'     => (float) $default_breakpoints['lg'],
                    // translators: %1$s - default breakpoint.
                    'desc'        => sprintf( wp_kses_post( __( 'Sets the breakpoint on large screen sizes (Default: %1$spx).', '@@text_domain' ) ), $default_breakpoints['lg'] ),
                    'is_pro'      => true,
                ),
                array(
                    'name'        => 'breakpoint_md',
                    'label'       => esc_html__( 'Medium', '@@text_domain' ),
                    'type'        => 'number',
                    'min'         => (float) $default_breakpoints['sm'] + 1,
                    'max'         => (float) $default_breakpoints['lg'] - 1,
                    'placeholder' => (string) $default_breakpoints['md'],
                    'default'     => (float) $default_breakpoints['md'],
                    // translators: %1$s - default breakpoint.
                    'desc'        => sprintf( wp_kses_post( __( 'Sets the breakpoint on medium screen sizes (Default: %1$spx).', '@@text_domain' ) ), $default_breakpoints['md'] ),
                    'is_pro'      => true,
                ),
                array(
                    'name'        => 'breakpoint_sm',
                    'label'       => esc_html__( 'Small', '@@text_domain' ),
                    'type'        => 'number',
                    'min'         => (float) $default_breakpoints['xs'] + 1,
                    'max'         => (float) $default_breakpoints['md'] - 1,
                    'placeholder' => (string) $default_breakpoints['sm'],
                    'default'     => (float) $default_breakpoints['sm'],
                    // translators: %1$s - default breakpoint.
                    'desc'        => sprintf( wp_kses_post( __( 'Sets the breakpoint on small screen sizes (Default: %1$spx).', '@@text_domain' ) ), $default_breakpoints['sm'] ),
                    'is_pro'      => true,
                ),
                array(
                    'name'        => 'breakpoint_xs',
                    'label'       => esc_html__( 'Extra Small', '@@text_domain' ),
                    'type'        => 'number',
                    'min'         => 1,
                    'max'         => (float) $default_breakpoints['sm'] - 1,
                    'placeholder' => (string) $default_breakpoints['xs'],
                    'default'     => (float) $default_breakpoints['xs'],
                    // translators: %1$s - default breakpoint.
                    'desc'        => sprintf( wp_kses_post( __( 'Sets the breakpoint on extra small screen sizes (Default: %1$spx).', '@@text_domain' ) ), $default_breakpoints['xs'] ),
                    'is_pro'      => true,
                ),
            ),
            'vp_images' => array(
                array(
                    'name'    => 'lazy_loading',
                    'label'   => esc_html__( 'Lazy Loading', '@@text_domain' ),
                    // translators: %s - plugin brand name.
                    'desc'    => sprintf( esc_html__( 'Enable lazy loading for %s layouts only or for the whole website.', '@@text_domain' ), visual_portfolio()->plugin_name ),
                    'type'    => 'select',
                    'default' => 'vp',
                    'options' => array(
                        ''     => esc_html__( 'Disabled', '@@text_domain' ),
                        // translators: %s - plugin brand name.
                        'vp'   => sprintf( esc_html__( '%s Only', '@@text_domain' ), visual_portfolio()->plugin_name ),
                        'full' => esc_html__( 'All images', '@@text_domain' ),
                    ),
                ),

                array(
                    'name'    => 'images_layouts_title',
                    'label'   => esc_html__( 'Layouts Image Sizes', '@@text_domain' ),
                    'desc'    => __( 'Image sizes used in portfolio layouts.', '@@text_domain' ),
                    'type'    => 'section_title',
                ),
                array(
                    'name'        => 'sm',
                    'label'       => esc_html__( 'Small', '@@text_domain' ),
                    'type'        => 'number',
                    'placeholder' => '500',
                    'default'     => 500,
                ),
                array(
                    'name'        => 'md',
                    'label'       => esc_html__( 'Medium', '@@text_domain' ),
                    'type'        => 'number',
                    'placeholder' => '800',
                    'default'     => 800,
                ),
                array(
                    'name'        => 'lg',
                    'label'       => esc_html__( 'Large', '@@text_domain' ),
                    'type'        => 'number',
                    'placeholder' => '1280',
                    'default'     => 1280,
                ),
                array(
                    'name'        => 'xl',
                    'label'       => esc_html__( 'Extra Large', '@@text_domain' ),
                    'type'        => 'number',
                    'placeholder' => '1920',
                    'default'     => 1920,
                ),
                array(
                    'name'    => 'images_popup_title',
                    'label'   => esc_html__( 'Popup Gallery Image Sizes', '@@text_domain' ),
                    'desc'    => __( 'Image sizes used in popup gallery images.', '@@text_domain' ),
                    'type'    => 'section_title',
                ),
                array(
                    'name'        => 'sm_popup',
                    'label'       => esc_html__( 'Small', '@@text_domain' ),
                    'type'        => 'number',
                    'placeholder' => '500',
                    'default'     => 500,
                ),
                array(
                    'name'        => 'md_popup',
                    'label'       => esc_html__( 'Medium', '@@text_domain' ),
                    'type'        => 'number',
                    'placeholder' => '800',
                    'default'     => 800,
                ),
                array(
                    'name'        => 'xl_popup',
                    'label'       => esc_html__( 'Large', '@@text_domain' ),
                    'type'        => 'number',
                    'placeholder' => '1920',
                    'default'     => 1920,
                ),
                array(
                    'name'    => 'images_sizes_note',
                    // translators: %s: regenerate thumbnails url.
                    'desc'    => sprintf( __( 'After publishing your changes, new image sizes may not be shown until you <a href="%s" target="_blank">Regenerate Thumbnails</a>.', '@@text_domain' ), 'https://wordpress.org/plugins/regenerate-thumbnails/' ),
                    'type'    => 'html',
                ),
            ),
            'vp_popup_gallery' => array(
                // Vendor.
                array(
                    'name'    => 'vendor',
                    'label'   => esc_html__( 'Vendor Script', '@@text_domain' ),
                    'type'    => 'select',
                    'options' => array(
                        'fancybox'   => esc_html__( 'Fancybox', '@@text_domain' ),
                        'photoswipe' => esc_html__( 'PhotoSwipe', '@@text_domain' ),
                    ),
                    'default' => 'fancybox',
                ),

                // Default WordPress Images.
                array(
                    'name'    => 'enable_on_wordpress_images',
                    'label'   => esc_html__( 'WordPress Images', '@@text_domain' ),
                    'desc'    => esc_html__( 'Enable popup for WordPress images and galleries.', '@@text_domain' ),
                    'type'    => 'toggle',
                    'default' => 'off',
                ),

                // Section divider.
                array(
                    'name'   => 'popup_general_divider_title',
                    'type'   => 'section_title',
                ),

                // Deeplinking.
                array(
                    'name'    => 'deep_linking',
                    'label'   => esc_html__( 'Deep Linking', '@@text_domain' ),
                    'desc'    => esc_html__( 'Makes URL automatically change to reflect the current opened popup, and you can easily link directly to that image or video.', '@@text_domain' ),
                    'type'    => 'toggle',
                    'default' => ! class_exists( 'Visual_Portfolio_Pro' ) ? 'off' : 'on',
                    'is_pro'  => true,
                ),
                array(
                    'name'    => 'deep_linking_url_to_share_images',
                    'label'   => esc_html__( 'Use Deep Linking URL to Share Images', '@@text_domain' ),
                    'desc'    => esc_html__( 'Check to share Deep Linking URLs when sharing images. When disabled, all galleries will share direct links to image files.', '@@text_domain' ),
                    'type'    => 'toggle',
                    'default' => 'off',
                    'is_pro'  => true,
                ),

                // Loop.
                array(
                    'name'    => 'loop',
                    'label'   => esc_html__( 'Loop', '@@text_domain' ),
                    'type'    => 'toggle',
                    'default' => 'on',
                    'is_pro'  => true,
                ),

                // Click to Zoom.
                array(
                    'name'    => 'click_to_zoom',
                    'label'   => esc_html__( 'Click to Zoom', '@@text_domain' ),
                    'type'    => 'toggle',
                    'default' => 'on',
                ),

                // UI Elements.
                array(
                    'name'  => 'popup_ui_elements_title',
                    'label' => esc_html__( 'UI Elements', '@@text_domain' ),
                    'type'  => 'section_title',
                ),
                array(
                    'name'    => 'show_arrows',
                    'label'   => esc_html__( 'Display Arrows', '@@text_domain' ),
                    'desc'    => esc_html__( 'Arrows to navigate between images.', '@@text_domain' ),
                    'type'    => 'toggle',
                    'default' => 'on',
                ),
                array(
                    'name'    => 'show_counter',
                    'label'   => esc_html__( 'Display Images Counter', '@@text_domain' ),
                    'desc'    => esc_html__( 'On the top left corner will be showed images counter.', '@@text_domain' ),
                    'type'    => 'toggle',
                    'default' => 'on',
                ),
                array(
                    'name'    => 'show_zoom_button',
                    'label'   => esc_html__( 'Display Zoom Button', '@@text_domain' ),
                    'type'    => 'toggle',
                    'default' => 'on',
                ),
                array(
                    'name'    => 'show_fullscreen_button',
                    'label'   => esc_html__( 'Display Fullscreen Button', '@@text_domain' ),
                    'type'    => 'toggle',
                    'default' => 'on',
                ),
                array(
                    'name'    => 'show_share_button',
                    'label'   => esc_html__( 'Display Share Button', '@@text_domain' ),
                    'type'    => 'toggle',
                    'default' => 'on',
                ),
                array(
                    'name'    => 'show_close_button',
                    'label'   => esc_html__( 'Display Close Button', '@@text_domain' ),
                    'type'    => 'toggle',
                    'default' => 'on',
                ),

                // Fancybox Popup Settings.
                array(
                    'name'      => 'show_thumbs',
                    'label'     => esc_html__( 'Display Thumbnails', '@@text_domain' ),
                    'type'      => 'toggle',
                    'default'   => 'on',
                    'condition' => array(
                        array(
                            'control'  => '[name="vp_popup_gallery[vendor]"]',
                            'operator' => '===',
                            'value'    => 'fancybox',
                        ),
                    ),
                ),
                array(
                    'name'      => 'thumbs_auto_start',
                    'label'     => esc_html__( 'Thumbnails Opened At Startup', '@@text_domain' ),
                    'type'      => 'toggle',
                    'default'   => 'off',
                    'is_pro'    => true,
                    'condition' => array(
                        array(
                            'control' => '[type="checkbox"][name="vp_popup_gallery[show_thumbs]"]',
                        ),
                        array(
                            'control'  => '[name="vp_popup_gallery[vendor]"]',
                            'operator' => '===',
                            'value'    => 'fancybox',
                        ),
                    ),
                ),
                array(
                    'name'      => 'thumbs_position',
                    'label'     => esc_html__( 'Thumbnails Position', '@@text_domain' ),
                    'type'      => 'select',
                    'default'   => 'vertical',
                    'options'   => array(
                        'vertical'   => esc_html__( 'Vertical', '@@text_domain' ),
                        'horizontal' => esc_html__( 'Horizontal', '@@text_domain' ),
                    ),
                    'is_pro'    => true,
                    'condition' => array(
                        array(
                            'control' => '[type="checkbox"][name="vp_popup_gallery[show_thumbs]"]',
                        ),
                        array(
                            'control'  => '[name="vp_popup_gallery[vendor]"]',
                            'operator' => '===',
                            'value'    => 'fancybox',
                        ),
                    ),
                ),
                array(
                    'name'      => 'show_download_button',
                    'label'     => esc_html__( 'Display Download Button', '@@text_domain' ),
                    'type'      => 'toggle',
                    'default'   => 'off',
                    'condition' => array(
                        array(
                            'control'  => '[name="vp_popup_gallery[vendor]"]',
                            'operator' => '===',
                            'value'    => 'fancybox',
                        ),
                    ),
                ),
                array(
                    'name'      => 'show_slideshow',
                    'label'     => esc_html__( 'Display Slideshow', '@@text_domain' ),
                    'type'      => 'toggle',
                    'default'   => 'off',
                    'condition' => array(
                        array(
                            'control'  => '[name="vp_popup_gallery[vendor]"]',
                            'operator' => '===',
                            'value'    => 'fancybox',
                        ),
                    ),
                ),

                // Misc settings.
                array(
                    'name'  => 'popup_misc_title',
                    'label' => esc_html__( 'Misc', '@@text_domain' ),
                    'type'  => 'section_title',
                ),
                array(
                    'name'    => 'background_color',
                    'label'   => esc_html__( 'Background Color', '@@text_domain' ),
                    'type'    => 'color',
                    'default' => '#1e1e1e',
                ),
                array(
                    'name'    => 'pages_iframe_custom_css',
                    'label'   => esc_html__( 'Pages iFrame Custom CSS', '@@text_domain' ),
                    'desc'    => esc_html__( 'When you display pages in popup iframe, you may not need some page elements like header and footer. Hide it using custom CSS with classname `.vp-popup-iframe`.', '@@text_domain' ),
                    'type'    => 'textarea',
                    'default' => ! class_exists( 'Visual_Portfolio_Pro' ) ? '' : '
.vp-popup-iframe #site-header,
.vp-popup-iframe #masthead,
.vp-popup-iframe #site-footer,
.vp-popup-iframe #colophon {
    display: none;
}',
                    'is_pro'  => true,
                ),
            ),
            'vp_watermarks' => array(
                array(
                    'name'    => 'pro_info',
                    'desc'    => '
                        <div class="vpf-settings-info-pro">
                            <h3>' . esc_html__( 'Pro Feature', '@@text_domain' ) . '</h3>
                            <div>
                                <p>' . esc_html__( 'Protect your works using watermarks', '@@text_domain' ) . '</p>
                                <a class="vpf-settings-info-pro-button" target="_blank" rel="noopener noreferrer" href="https://visualportfolio.co/pro/?utm_source=freeplugin&utm_medium=link&utm_campaign=plugin_settings&utm_content=@@plugin_version">' . esc_html__( 'Read More', '@@text_domain' ) . '</a>
                            </div>
                        </div>
                    ',
                    'type'    => 'html',
                ),
            ),
            'vp_social_integrations' => array(
                array(
                    'name'    => 'pro_info',
                    'desc'    => '
                        <div class="vpf-settings-info-pro">
                            <h3>' . esc_html__( 'Pro Feature', '@@text_domain' ) . '</h3>
                            <div>
                                <p>' . esc_html__( 'Social feeds such as Instagram, Youtube, Flickr, Twitter, etc...', '@@text_domain' ) . '</p>
                                <a class="vpf-settings-info-pro-button" target="_blank" rel="noopener noreferrer" href="https://visualportfolio.co/pro/?utm_source=freeplugin&utm_medium=link&utm_campaign=plugin_settings&utm_content=@@plugin_version">' . esc_html__( 'Read More', '@@text_domain' ) . '</a>
                            </div>
                        </div>
                    ',
                    'type'    => 'html',
                ),
            ),
            'vp_white_label' => array(
                array(
                    'name'    => 'pro_info',
                    'desc'    => '
                        <div class="vpf-settings-info-pro">
                            <h3>' . esc_html__( 'Pro Feature', '@@text_domain' ) . '</h3>
                            <div>
                                <p>' . esc_html__( 'Remove our plugin brand and logos from Front and Admin areas', '@@text_domain' ) . '</p>
                                <a class="vpf-settings-info-pro-button" target="_blank" rel="noopener noreferrer" href="https://visualportfolio.co/pro/?utm_source=freeplugin&utm_medium=link&utm_campaign=plugin_settings&utm_content=@@plugin_version">' . esc_html__( 'Read More', '@@text_domain' ) . '</a>
                            </div>
                        </div>
                    ',
                    'type'    => 'html',
                ),
            ),
        );

        return apply_filters( 'vpf_settings_fields', $settings_fields );
    }

    /**
     * The plugin page handler
     *
     * @return void
     */
    public static function print_settings_page() {
        self::$settings_api->admin_enqueue_scripts();

        echo '<div class="wrap">';
        echo '<h2>' . esc_html__( 'Settings', '@@text_domain' ) . '</h2>';

        self::$settings_api->show_navigation();
        self::$settings_api->show_forms();

        echo '</div>';

        ?>
        <script>
            (function( $ ) {
                // Don't allow adding input number values that > then max attribute and < min attribute.
                $('form').on('input', '[type="number"]', function(e) {
                    var current = parseFloat( this.value );
                    var min = parseFloat(this.min);
                    var max = parseFloat(this.max);

                    if ('' !== this.value) {
                        if (!Number.isNaN(min) && current < min) {
                            this.value = min;
                        }
                        if (!Number.isNaN(max) && current > max) {
                            this.value = max;
                        }
                    }
                });

                <?php if ( ! class_exists( 'Visual_Portfolio_Pro' ) ) : ?>
                    // disable pro inputs.
                    $('.vpf-settings-control-pro').find('input, textarea').attr('disabled', 'disabled');
                <?php endif; ?>
            })(jQuery);
        </script>
        <?php
    }
}

new Visual_Portfolio_Settings();
