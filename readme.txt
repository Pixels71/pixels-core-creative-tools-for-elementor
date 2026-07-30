=== Pixels Addons for Elementor ===
Contributors: pixels71
Tags: elementor, widgets, page builder, header footer builder, custom css
Requires at least: 6.0
Tested up to: 7.0
Requires PHP: 7.4
Requires Plugins: elementor
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Essential Elementor widgets, Custom CSS/JS, Live Copy Paste, and a Header/Footer theme builder from Pixels71.

== Description ==

**Pixels Addons for Elementor** extends Elementor with a curated set of free widgets, developer-friendly extensions, and a lightweight theme builder for headers and footers.

Manage everything from a modern admin dashboard under **Pixels Addons** in the WordPress admin menu. Enable or disable individual widgets and extensions without loading unused assets on the frontend.

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
* Menu
* Site Logo

= Free Extensions =

* **Custom CSS** — Add custom CSS to any Elementor element from the Advanced tab.
* **Custom JS** — Add custom JavaScript to any Elementor element from the Advanced tab.
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
[Pixels Addons for Elementor](https://github.com/Pixels71/pixels-elementor-addons)

= Translations =

Translation template files are included in the `languages` folder. To contribute a translation, copy `pixels-elementor-addons.pot` to your locale (for example, `pixels-elementor-addons-fr_FR.po`), translate the strings, and compile a `.mo` file.

= Credits =

See `CREDITS.txt` for third-party libraries and attribution.

== Installation ==

1. Upload the `pixels-elementor-addons` folder to `/wp-content/plugins/`, or install the plugin through the WordPress Plugins screen.
2. Activate the plugin through the **Plugins** screen in WordPress.
3. Make sure **Elementor** is installed and activated.
4. After activation, you will be redirected to the Pixels Addons dashboard.
5. Enable the widgets and extensions you want to use, then edit pages with Elementor.

== Frequently Asked Questions ==

= Does this plugin work without Elementor? =

No. Pixels Addons for Elementor requires Elementor to be installed and active.

= Why are some widgets missing in Elementor? =

Check the Pixels Addons dashboard and confirm the widget is enabled. Nested widgets also require Elementor's **Nested Elements** feature.

= Where do I add custom CSS or JavaScript? =

Enable the **Custom CSS** and **Custom JS** extensions in the dashboard, then use the controls in the Elementor Advanced tab on any element.

= How do I use Live Copy Paste? =

Enable **Live Copy Paste** in the Pixels Addons dashboard. In the Elementor editor, paste copied Elementor JSON from your clipboard to insert sections, containers, or widgets.

= How do I build a custom header or footer? =

Go to **Pixels Addons → Theme Builder**, create a Header or Footer template, design it with Elementor, and assign display rules.

== Changelog ==

= 1.0.0 =
* Initial release.
* Free Elementor widgets, Custom CSS/JS, Live Copy Paste, and Header/Footer theme builder.
* Admin dashboard for widget and extension management.
* Orbit Circle uses CSS animation.
* Translation-ready with `languages/pixels-elementor-addons.pot`.

== Upgrade Notice ==

= 1.0.0 =
Initial release of Pixels Addons for Elementor.
