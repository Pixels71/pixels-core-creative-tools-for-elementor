=== Pixels Core Creative Tools for Elementor ===
Contributors: pixels71
Tags: elementor, widgets, page builder, header footer builder, theme builder
Requires at least: 6.0
Tested up to: 7.0
Requires PHP: 7.4
Requires Plugins: elementor
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Essential Elementor widgets, Live Copy Paste, and a Header/Footer theme builder from Pixels71.

== Description ==

**Pixels Core Creative Tools for Elementor** extends Elementor with a curated set of free widgets, Live Copy Paste, and a lightweight theme builder for headers and footers.

Manage everything from a modern admin dashboard under **Pixels Core** in the WordPress admin menu. Enable or disable individual widgets and extensions without loading unused assets on the frontend.

= Free Widgets =

* Tabs
* Accordion
* Carousel
* Heading
* Countdown Timer
* Progress Bar
* Counter
* Rotator Text
* Orbit Circle
* Button
* Menu
* Site Logo

= Free Extensions =

* **Live Copy Paste** — Paste Elementor sections, containers, and widgets from the clipboard in the editor.

= Theme Builder (Free) =

Build and assign Elementor templates for:

* Header
* Footer

Display rules let you target templates across your site.

= Requirements =

* WordPress 6.0 or higher
* PHP 7.4 or higher
* [Elementor](https://wordpress.org/plugins/elementor/) 3.5.0 or higher

Some nested widgets (Tabs, Accordion, Carousel) require the **Nested Elements** experiment to be enabled in **Elementor → Settings → Features**.

= Source code =

Development source (including the admin dashboard React app and build tools) is available at:
[https://github.com/pixels71/pixels-core-creative-tools-for-elementor](https://github.com/pixels71/pixels-core-creative-tools-for-elementor)

= Translations =

Translation template files are included in the `languages` folder. To contribute a translation, copy `pixels-core-creative-tools-for-elementor.pot` to your locale (for example, `pixels-core-creative-tools-for-elementor-fr_FR.po`), translate the strings, and compile a `.mo` file.

= Credits =

See `CREDITS.txt` for third-party libraries and attribution.

== Installation ==

1. Upload the `pixels-core-creative-tools-for-elementor` folder to `/wp-content/plugins/`, or install the plugin through the WordPress Plugins screen.
2. Activate the plugin through the **Plugins** screen in WordPress.
3. Make sure **Elementor** is installed and activated.
4. After activation, you will be redirected to the dashboard.
5. Enable the widgets and extensions you want to use, then edit pages with Elementor.

== Frequently Asked Questions ==

= Does this plugin work without Elementor? =

No. Pixels Core Creative Tools for Elementor requires Elementor to be installed and active.

= Why are some widgets missing in Elementor? =

Check the Pixels Core dashboard and confirm the widget is enabled. Nested widgets also require Elementor's **Nested Elements** feature.

= How do I use Live Copy Paste? =

Enable **Live Copy Paste** in the Pixels Core dashboard. In the Elementor editor, paste copied Elementor JSON from your clipboard to insert sections, containers, or widgets.

= How do I build a custom header or footer? =

Go to **Theme Builder**, create a Header or Footer template, design it with Elementor, and assign display rules.

== Screenshots ==

1. Pixels Core dashboard — manage widgets and extensions.
2. Widget settings in the Elementor editor.
3. Theme Builder for headers and footers.

== Changelog ==

= 1.0.1 =
* Renamed plugin for WordPress.org distinctiveness and trademark clarity.
* Removed Custom CSS and Custom JS extensions (directory guideline compliance).
* Removed non-GPL animation libraries from the free plugin; Button and Orbit Circle use CSS/WAAPI.
* Escaping, enqueue, AJAX prefix, Select2, and admin menu placement fixes.

= 1.0.0 =
* Initial release.
* Free Elementor widgets, Live Copy Paste, and Header/Footer theme builder.
* Admin dashboard for widget and extension management.
* Translation-ready.
* Redirect to dashboard on plugin activation.
