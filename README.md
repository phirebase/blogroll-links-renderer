# Blogroll Links Renderer

![WordPress Tested](https://img.shields.io/badge/WordPress-6.9-blue)
![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue)
![License](https://img.shields.io/badge/License-GPLv2-orange)
[![Issues](https://img.shields.io/github/issues/phirebase/blogroll-links-renderer)](https://github.com/phirebase/blogroll-links-renderer/issues)
[![Stars](https://img.shields.io/github/stars/phirebase/blogroll-links-renderer?style=social)](https://github.com/phirebase/blogroll-links-renderer)

Render WordPress Blogroll links anywhere using the `[blogroll-links]` shortcode.

![Plugin banner](./assets/banner-772x250.jpg)

---

## 🧩 Description

**Blogroll Links Renderer** allows you to display WordPress Links (aka Blogroll) on any page or post using the `[blogroll-links]` shortcode.

You can filter links by category, show icons, and add tooltips — plus optionally enable the hidden **Links Manager**, which has been disabled by default since WordPress 3.5.

---

## ✨ Features

- Display Blogroll links using the shortcode `[blogroll-links]`  
- Filter links by **category name**  
- Show **icons/images** next to each link  
- Enable **tooltips** using link descriptions  
- Add a **custom CSS class** for styling output  
- Optional: **Enable Links Manager** via plugin settings  
- Simple and clean settings page under **Settings > Blogroll Links Renderer**

---

## ⚙️ Shortcode Parameters

Use the `[blogroll-links]` shortcode with these optional parameters:

| Parameter     | Description                              | Example                                               |
|---------------|------------------------------------------|-------------------------------------------------------|
| `category`    | Filter links by category name            | `[blogroll-links category="Partners"]`               |
| `show_images` | Show link images/icons if available      | `[blogroll-links show_images="1"]`                   |
| `show_titles` | Show tooltips using link descriptions    | `[blogroll-links show_titles="1"]`                   |

✅ You can combine all options:  
`[blogroll-links category="MyCategory" show_images="1" show_titles="1"]`

---

## 🔧 Installation

1. Upload the plugin folder to `/wp-content/plugins/`  
2. Activate the plugin via the **Plugins** menu in WordPress  
3. Add the `[blogroll-links]` shortcode to any page or post  
4. Configure settings under **Settings > Blogroll Links Renderer**

---

## ❓ FAQ

### How do I enable the WordPress Links Manager?

Navigate to **Settings > Blogroll Links Renderer** and check **Enable Links Manager**. The **Links** menu will appear in your admin sidebar.

### How do I filter links by category?

Use the `category` parameter in the shortcode.  
Example: `[blogroll-links category="MyCategory"]`

### How do I show images or icons for links?

Use the `show_images="1"` parameter.  
Example: `[blogroll-links show_images="1"]`

### How do I show tooltips using link descriptions?

Use the `show_titles="1"` parameter.  
Example: `[blogroll-links show_titles="1"]`

### Can I style the output?

Yes. Add a custom CSS class in the plugin settings and target it with your own CSS.

---

## 📝 Changelog

### 1.0.1

- Updated `README.md`

### 1.0.0

- Initial public release

---

## 📌 Notes

- This plugin restores functionality hidden since WordPress 3.5  
- Compatible with custom link categories and WordPress themes  
- Tested with WordPress 5.5+ up to 6.9

---

## 🙏 Credits

Developed by [David Klhufek](https://phirebase.com)  
Plugin page: <https://wordpress.org/plugins/blogroll-links-renderer/>  
Support the project: [paypal.me/DavidKlhufek](https://paypal.me/DavidKlhufek)

---

## 📄 License

Licensed under the GPLv2 or later. See `LICENSE` file.  
[https://www.gnu.org/licenses/gpl-2.0.html](https://www.gnu.org/licenses/gpl-2.0.html)
