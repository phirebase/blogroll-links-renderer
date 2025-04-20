=== Blogroll Links Renderer ===  
Contributors: brtak  
Tags: blogroll, links, shortcode, renderer   
Tested up to: 6.8   
Stable tag: 1.0.1    
Donate link: https://paypal.me/DavidKlhufek  
License: GPLv2 or later   
License URI: https://www.gnu.org/licenses/gpl-2.0.html    

Render WordPress Blogroll links on any Page or Post using the shortcode `[blogroll-links]`.

== Description ==

Easily display WordPress Blogroll links with the shortcode `[blogroll-links]` and its customizable options.

**Features:**  
- Display WordPress Blogroll links directly on your site.  
- Filter links by category or show only specific links.  
- Optionally display icons/images alongside each link.  
- Customize the layout with a custom CSS class via settings.  
- Simple setup via `Settings > Blogroll Links Renderer`.  

**Shortcode Parameters:**  
- **`category`**: Filter links by category name.  
  Example: `[blogroll-links category="MyCategory"]`.  
- **`show_images`**: Display link images/icons if available.  
  Example: `[blogroll-links show_images="1"]`.  
- **`show_titles`**: Add tooltips using link descriptions.  
  Example: `[blogroll-links show_titles="1"]`.  
- Combine all options for advanced usage.  
  Example: `[blogroll-links category="MyCategory" show_images=1 show_titles=1]`.

**Enable WordPress Links Manager**:  
The WordPress Links Manager, hidden by default since WordPress 3.5, can be reactivated using this plugin.  
To enable it, go to `Settings > Blogroll Links Renderer` and check the option **Enable Links Manager**. Once enabled, manage links via the **Links** menu in the WordPress dashboard.

== Installation ==

1. Upload the plugin to your `wp-content/plugins` directory.  
2. Activate the plugin via the **Plugins** menu in WordPress.  
3. Add the shortcode `[blogroll-links]` to any page or post to display your Blogroll links.  
4. Customize the output through the plugin settings or with CSS.

== Frequently Asked Questions ==

= How do I enable the WordPress Links Manager? =
Navigate to `Settings > Blogroll Links Renderer` and check the box to enable the Links Manager. The **Links** menu will then appear in your WordPress dashboard.

= How do I filter links by category? =
Use the `category` parameter in the shortcode.  
Example: `[blogroll-links category="MyCategory"]`.

= How do I display images/icons for the links? =
Set the `show_images` parameter to `1` in the shortcode.  
Example: `[blogroll-links show_images="1"]`.

= How do I add tooltips to the links? =
Set the `show_titles` parameter to `1` in the shortcode.  
Example: `[blogroll-links show_titles="1"]`.

= Can I style the output? =
Yes, you can add a custom CSS class in the plugin settings. Navigate to `Settings > Blogroll Links Renderer` and enter your desired CSS class.

== Changelog ==

= 1.0.1 =

Updated README.md

= 1.0.0 =
* Initial public release
