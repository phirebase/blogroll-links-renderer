<?php
/*
Plugin Name: Blogroll Links Renderer
Version: 1.0
Description: Renders WordPress Blogroll links to a Page or Post using the shortcode [blogroll-links]. Ideal for creating a custom links page.
Author: phirebase
Text Domain: blogroll-links-renderer
Author URI: https://phirebase.com/
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

defined('ABSPATH') || exit;

/**
 * Forces the WordPress Links Manager to the desired state.
 */
function blr_force_links_manager_state() {
    $enable_links_manager = get_option('blr_enable_links_manager', false);

    if ($enable_links_manager) {
        add_filter('pre_option_link_manager_enabled', '__return_true');
        update_option('link_manager_enabled', 1);
    } else {
        add_filter('pre_option_link_manager_enabled', '__return_false');
        delete_option('link_manager_enabled');
    }
}
add_action('admin_init', 'blr_force_links_manager_state');

/**
 * Hides the Links menu from the WordPress admin if the Links Manager is disabled.
 */
function blr_hide_links_menu() {
    $enable_links_manager = get_option('blr_enable_links_manager', false);

    if (!$enable_links_manager) {
        remove_menu_page('link-manager.php');
    }
}
add_action('admin_menu', 'blr_hide_links_menu', 99);

/**
 * Adds custom CSS for consistent image size.
 */
function blr_add_custom_styles() {
    echo '<style>
        .blogroll-link-image {
            width: 16px !important;
            height: 16px !important;
        }
    </style>';
}
add_action('wp_head', 'blr_add_custom_styles');

/**
 * Wrapper function for handling both local and external images.
 */
function blr_get_image_html($link) {
    $image_html = '';
    if (!empty($link->link_image)) {
        $image_id = attachment_url_to_postid($link->link_image);
        if (!empty($image_id) && is_numeric($image_id)) {
            // Lokální obrázky - bezpečné vykreslení pomocí wp_get_attachment_image
            $image_html = wp_get_attachment_image(
                $image_id,
                'thumbnail',
                false,
                [
                    'class' => 'blogroll-link-image',
                    'alt'   => esc_attr($link->link_name),
                ]
            );
        } else {
            // Externí obrázky - přímé vykreslení
            $image_html = sprintf(
                '<img src="%s" alt="%s" class="blogroll-link-image" loading="lazy" decoding="async">', // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
                esc_url($link->link_image),
                esc_attr($link->link_name)
            );
        }
    }
    return $image_html;
}

/**
 * Renders blogroll links using the shortcode [blogroll-links].
 */
function blr_render_blogroll_links($atts) {
    $atts = shortcode_atts([
        'category' => '',
        'show_images' => null,
        'show_titles' => 0,
    ], $atts, 'blogroll-links');

    $category = sanitize_text_field($atts['category']);
    $show_images = isset($atts['show_images']) ? filter_var($atts['show_images'], FILTER_VALIDATE_BOOLEAN) : true;
    $show_titles = filter_var($atts['show_titles'], FILTER_VALIDATE_BOOLEAN);

    $custom_class = get_option('blr_custom_class', '');

    $args = [
        'orderby' => 'name',
        'order' => 'ASC',
    ];
    if (!empty($category)) {
        $args['category_name'] = $category;
    }

    $links = get_bookmarks($args);

    if (empty($links)) {
        return '<p>' . esc_html__('No links found.', 'blogroll-links-renderer') . '</p>';
    }

    ob_start();
    echo '<div class="' . esc_attr(trim('blogroll-links ' . $custom_class)) . '">';

    foreach ($links as $link) {
        $title_attribute = ($show_titles && !empty($link->link_description)) ? esc_attr($link->link_description) : '';
        $image_html = $show_images ? blr_get_image_html($link) : '';

        printf(
            '<div class="blogroll-link">
                <a href="%s" target="_blank" rel="noopener noreferrer" title="%s">
                    %s <span class="blogroll-link-name">%s</span>
                </a>
            </div>',
            esc_url($link->link_url),
            esc_attr($title_attribute),
            wp_kses_post($image_html),
            esc_html($link->link_name)
        );
    }

    echo '</div>';
    return ob_get_clean();
}
add_shortcode('blogroll-links', 'blr_render_blogroll_links');

/**
 * Adds a settings page under the "Settings" menu.
 */
function blr_add_settings_page() {
    add_options_page(
        __('Blogroll Links Renderer Settings', 'blogroll-links-renderer'),
        __('Blogroll Links Renderer', 'blogroll-links-renderer'),
        'manage_options',
        'blogroll-links-renderer',
        'blr_settings_page_callback'
    );
}
add_action('admin_menu', 'blr_add_settings_page');

/**
 * Renders the plugin settings page.
 */
function blr_settings_page_callback() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Blogroll Links Renderer Settings', 'blogroll-links-renderer'); ?></h1>
        
        <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; background-color: #f9f9f9; border-radius: 5px;">
            <h2><?php esc_html_e('How to use:', 'blogroll-links-renderer'); ?></h2>
            <p><code>[blogroll-links]</code>: <?php esc_html_e('Display all links.', 'blogroll-links-renderer'); ?></p>
            <p><code>[blogroll-links category="MyCategory"]</code>: <?php esc_html_e('Filter links by category name.', 'blogroll-links-renderer'); ?></p>
            <p><code>[blogroll-links show_images="1"]</code>: <?php esc_html_e('Show link images/icons if available.', 'blogroll-links-renderer'); ?></p>
            <p><code>[blogroll-links show_titles="1"]</code>: <?php esc_html_e('Add link descriptions as tooltips.', 'blogroll-links-renderer'); ?></p>
        </div>

        <form method="post" action="options.php">
            <?php settings_fields('blr_settings_group'); ?>
            <?php do_settings_sections('blr_settings_group'); ?>

            <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; background-color: #f9f9f9; border-radius: 5px;">
                <h2><?php esc_html_e('Enable Links Manager', 'blogroll-links-renderer'); ?></h2>
                <input type="checkbox" name="blr_enable_links_manager" value="1" <?php checked(get_option('blr_enable_links_manager', false), true); ?> />
                <label for="blr_enable_links_manager"><?php esc_html_e('Enable Links Manager', 'blogroll-links-renderer'); ?></label>
            </div>

            <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; background-color: #f9f9f9; border-radius: 5px;">
                <h2><?php esc_html_e('Custom CSS Class', 'blogroll-links-renderer'); ?></h2>
                <input type="text" name="blr_custom_class" value="<?php echo esc_attr(get_option('blr_custom_class', '')); ?>" />
            </div>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * Registers settings.
 */
function blr_register_settings() {
    register_setting('blr_settings_group', 'blr_enable_links_manager');
    register_setting('blr_settings_group', 'blr_custom_class');
}
add_action('admin_init', 'blr_register_settings');
?>
