<?php
/**
Plugin Name: Blogroll Links Renderer
Version: 1.0.1
Description: Renders WordPress Blogroll links to a Page or Post using the shortcode [blogroll-links]. Ideal for creating a custom links page.
Author: phirebase
Author URI: https://phirebase.com/
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: blogroll-links-renderer
 *
 * @package BlogrollLinksRenderer
 */

defined( 'ABSPATH' ) || exit;

/**
 * Conditionally load the plugin's text domain for translations.
 * This ensures compatibility for environments outside WordPress.org.
 */
if ( ! function_exists( 'wp_get_environment_type' ) || wp_get_environment_type() !== 'production' ) {
	/**
	 * Load the plugin's text domain for translations.
	 *
	 * This function ensures that translation files are loaded
	 * for environments outside of WordPress.org, such as GitHub.
	 * The function is executed conditionally based on the environment.
	 *
	 * @return void
	 */
	function blrp_load_textdomain() {
		load_plugin_textdomain(
			'blogroll-links-renderer',
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages'
		);
	}
	add_action( 'init', 'blrp_load_textdomain' );
}

/**
 * Initialize default option value on plugin activation.
 */
function blrp_activate_plugin() {
	if ( get_option( 'blrp_enable_links_manager' ) === false ) {
		// Set default to false if the option does not exist.
		add_option( 'blrp_enable_links_manager', 0 );
	}
}
register_activation_hook( __FILE__, 'blrp_activate_plugin' );

/**
 * Hook to force the Links Manager state on every admin load.
 */
function blrp_force_links_manager_state() {
	$enable_links_manager = get_option( 'blrp_enable_links_manager', false );

	if ( $enable_links_manager ) {
		// Enable the Links Manager.
		add_filter( 'pre_option_link_manager_enabled', '__return_true' );
		update_option( 'link_manager_enabled', 1 );
	} else {
		// Disable the Links Manager.
		add_filter( 'pre_option_link_manager_enabled', '__return_false' );
		delete_option( 'link_manager_enabled' );
	}
}
add_action( 'admin_init', 'blrp_force_links_manager_state' );

/**
 * Hook to handle the saving of settings.
 *
 * This function updates or deletes the Links Manager option
 * based on the new value of the setting.
 *
 * @param mixed $old_value The old value of the option before the update.
 * @param mixed $new_value The new value of the option after the update.
 */
function blrp_save_links_manager_setting( $old_value, $new_value ) {
	// Update Links Manager state immediately after the setting changes.
	if ( $new_value ) {
		update_option( 'link_manager_enabled', 1 );
	} else {
		delete_option( 'link_manager_enabled' );
	}

	// Force the update immediately.
	blrp_force_links_manager_state();
}
add_action( 'update_option_blrp_enable_links_manager', 'blrp_save_links_manager_setting', 10, 2 );

/**
 * Hides the Links menu from the WordPress admin if the Links Manager is disabled.
 */
function blrp_hide_links_menu() {
	$enable_links_manager = get_option( 'blrp_enable_links_manager', false );

	// Check user permissions before removing the menu page.
	if ( current_user_can( 'manage_options' ) && ! $enable_links_manager ) {
		remove_menu_page( 'link-manager.php' );
	}
}
add_action( 'admin_menu', 'blrp_hide_links_menu', 99 );

/**
 * Enqueues custom styles for the plugin in both frontend and admin.
 *
 * This function conditionally loads a custom stylesheet for the
 * Blogroll Links Renderer plugin on the settings page.
 *
 * @param string $hook The current admin page being rendered, passed by WordPress.
 *                     Used to ensure the stylesheet is only loaded on the plugin's settings page.
 */
function blrp_enqueue_styles( $hook ) {
	if ( 'settings_page_blogroll-links-renderer' === $hook ) {
		wp_enqueue_style(
			'blrp-custom-style',
			plugins_url( 'css/blogroll-style.css', __FILE__ ),
			array(),
			'1.0',
			'all'
		);
	}
}
add_action( 'admin_enqueue_scripts', 'blrp_enqueue_styles' );

/**
 * Wrapper function for handling both local and external images.
 *
 * This function generates HTML for displaying images associated with a link,
 * handling both local WordPress media library images and external image URLs.
 *
 * @param object $link An object containing link properties. The 'link_image'
 *                     property specifies the image URL, and 'link_name'
 *                     provides the alt text.
 * @return string The HTML markup for the image, or an empty string if no image is set.
 */
function blrp_get_image_html( $link ) {
	$image_html = '';
	if ( ! empty( $link->link_image ) ) {
		$image_id = attachment_url_to_postid( $link->link_image );
		if ( ! empty( $image_id ) && is_numeric( $image_id ) ) {
			// Local images - safe rendering using wp_get_attachment_image.
			$image_html = wp_get_attachment_image(
				$image_id,
				'thumbnail',
				false,
				array(
					'class'  => 'blrp-blogroll-link-image',
					'alt'    => esc_attr( $link->link_name ),
					'width'  => 16,
					'height' => 16,
				)
			);
			$image_html = str_replace( '<img ', '<img width="16" height="16" ', $image_html );
		} else {
			// External images - direct rendering.
			$image_html = sprintf(
				'<img src="%s" alt="%s" class="blrp-blogroll-link-image" loading="lazy" decoding="async" width="16" height="16">', // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
				esc_url( $link->link_image ),
				esc_attr( $link->link_name )
			);
		}
	}
	return $image_html;
}

/**
 * Renders blogroll links using the shortcode [blogroll-links].
 *
 * This function generates a list of links from the WordPress Links Manager,
 * optionally filtering by category and including options to show images and titles.
 *
 * @param array $atts {
 *     Optional. Attributes for the shortcode.
 *
 *     @type string $category    The category to filter links by. Default is empty (no filter).
 *     @type bool   $show_images Whether to display link images. Default is true.
 *     @type bool   $show_titles Whether to display link titles (tooltips). Default is false.
 * }
 * @return string The HTML output of the rendered blogroll links.
 */
function blrp_render_blogroll_links( $atts ) {
	$atts = shortcode_atts(
		array(
			'category'    => '',
			'show_images' => null,
			'show_titles' => 0,
		),
		$atts,
		'blogroll-links'
	);

	$category    = sanitize_text_field( $atts['category'] );
	$show_images = isset( $atts['show_images'] ) ? filter_var( $atts['show_images'], FILTER_VALIDATE_BOOLEAN ) : true;
	$show_titles = filter_var( $atts['show_titles'], FILTER_VALIDATE_BOOLEAN );

	$custom_class = get_option( 'blrp_custom_class', '' );

	$args = array(
		'orderby' => 'name',
		'order'   => 'ASC',
	);
	if ( ! empty( $category ) ) {
		$args['category_name'] = $category;
	}

	$links = get_bookmarks( $args );

	if ( empty( $links ) ) {
		return '<p>' . esc_html__( 'No links found.', 'blogroll-links-renderer' ) . '</p>';
	}

	ob_start();
	echo '<div class="' . esc_attr( trim( 'blogroll-links ' . $custom_class ) ) . '">';

	foreach ( $links as $link ) {
		$title_attribute = ( $show_titles && ! empty( $link->link_description ) ) ? esc_attr( $link->link_description ) : '';
		$image_html      = $show_images ? blrp_get_image_html( $link ) : '';

		printf(
			'<div class="blogroll-link">
                <a href="%s" target="_blank" rel="noopener noreferrer" title="%s">
                    %s <span class="blogroll-link-name">%s</span>
                </a>
            </div>',
			esc_url( $link->link_url ),
			esc_attr( $title_attribute ),
			wp_kses_post( $image_html ),
			esc_html( $link->link_name )
		);
	}

	echo '</div>';
	return ob_get_clean();
}
add_shortcode( 'blogroll-links', 'blrp_render_blogroll_links' );

/**
 * Adds a settings page under the "Settings" menu.
 */
function blrp_add_settings_page() {
	add_options_page(
		__( 'Blogroll Links Renderer Settings', 'blogroll-links-renderer' ),
		__( 'Blogroll Links Renderer', 'blogroll-links-renderer' ),
		'manage_options',
		'blogroll-links-renderer',
		'blrp_settings_page_callback'
	);
}
add_action( 'admin_menu', 'blrp_add_settings_page' );

/**
 * Renders the plugin settings page.
 */
function blrp_settings_page_callback() {
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Blogroll Links Renderer Settings', 'blogroll-links-renderer' ); ?></h1>
		<div class="blrp-blogroll-settings-box">
			<h2><?php esc_html_e( 'How to use:', 'blogroll-links-renderer' ); ?></h2>
			<p><code>[blogroll-links]</code>: <?php esc_html_e( 'Display all links.', 'blogroll-links-renderer' ); ?></p>
			<p><code>[blogroll-links category="MyCategory"]</code>: <?php esc_html_e( 'Filter links by category name.', 'blogroll-links-renderer' ); ?></p>
			<p><code>[blogroll-links show_images="1"]</code>: <?php esc_html_e( 'Show link images/icons if available.', 'blogroll-links-renderer' ); ?></p>
			<p><code>[blogroll-links show_titles="1"]</code>: <?php esc_html_e( 'Add link descriptions as tooltips.', 'blogroll-links-renderer' ); ?></p>
		</div>        
		<form method="post" action="options.php">
			<?php settings_fields( 'blrp_settings_group' ); ?>
			<?php do_settings_sections( 'blrp_settings_group' ); ?>

			<div class="blrp-blogroll-settings-box">
				<h2><?php esc_html_e( 'Enable Links Manager', 'blogroll-links-renderer' ); ?></h2>
				<input type="checkbox" id="blrp_enable_links_manager" name="blrp_enable_links_manager" value="1" <?php checked( get_option( 'blrp_enable_links_manager', false ), true ); ?> />
				<label for="blrp_enable_links_manager"><?php esc_html_e( 'Enable', 'blogroll-links-renderer' ); ?></label>
			</div>


			<div class="blrp-blogroll-settings-box">
				<h2><?php esc_html_e( 'Custom CSS Class', 'blogroll-links-renderer' ); ?></h2>
				<input type="text" name="blrp_custom_class" value="<?php echo esc_attr( get_option( 'blrp_custom_class', '' ) ); ?>" />
			</div>

			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/**
 * Add "Settings" link to plugin list actions.
 *
 * This function adds a "Settings" link to the list of action links
 * displayed under the plugin name on the Plugins admin page.
 *
 * @param array $links An array of existing action links for the plugin.
 *                     Each link is a string of HTML markup.
 * @return array The modified array of action links with the "Settings" link added.
 */
function blrp_add_settings_link( $links ) {
	$settings_link = '<a href="' . admin_url( 'options-general.php?page=blogroll-links-renderer' ) . '">' . __( 'Settings', 'blogroll-links-renderer' ) . '</a>';
	array_unshift( $links, $settings_link );
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'blrp_add_settings_link' );

/**
 * Registers settings.
 */
function blrp_register_settings() {
	register_setting( 'blrp_settings_group', 'blrp_custom_class', 'sanitize_text_field' );
	register_setting(
		'blrp_settings_group',
		'blrp_enable_links_manager',
		function ( $input ) {
			return '1' === $input ? 1 : 0;
		}
	);
}
add_action( 'admin_init', 'blrp_register_settings' );

/**
 * Cleanup options when the plugin is uninstalled.
 */
function blrp_cleanup_options() {
	delete_option( 'blrp_enable_links_manager' );
	delete_option( 'blrp_custom_class' );
}
register_uninstall_hook( __FILE__, 'blrp_cleanup_options' );
?>
