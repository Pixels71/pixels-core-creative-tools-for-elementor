<?php
namespace PixelsElementorAddons\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Translatable strings for the React admin dashboard.
 */
final class Dashboard_I18n {

	/**
	 * @return array<string, string>
	 */
	public static function get_strings(): array {
		$strings = [
			'sidebarIntroduction'  => __( 'Introduction', 'pixels-elementor-addons' ),
			'sidebarWidgets'       => __( 'Widgets', 'pixels-elementor-addons' ),
			'sidebarExtension'     => __( 'Extension', 'pixels-elementor-addons' ),
			'sidebarFormSettings'  => __( 'Form Settings', 'pixels-elementor-addons' ),
			'dashboardNavLabel'    => __( 'Dashboard', 'pixels-elementor-addons' ),

			'tabIntroductionTitle'       => __( 'Introduction', 'pixels-elementor-addons' ),
			'tabIntroductionDescription' => __( 'Get started with Pixels Addons and explore what is available.', 'pixels-elementor-addons' ),
			'tabWidgetsTitle'            => __( 'Widgets', 'pixels-elementor-addons' ),
			'tabWidgetsDescription'      => __( 'Browse and manage the widgets available in your dashboard.', 'pixels-elementor-addons' ),
			'tabExtensionTitle'          => __( 'Extension', 'pixels-elementor-addons' ),
			'tabExtensionDescription'    => __( 'Enable extensions to unlock extra capabilities.', 'pixels-elementor-addons' ),
			'tabFormSettingsTitle'       => __( 'Form Settings', 'pixels-elementor-addons' ),
			'tabFormSettingsDescription' => __( 'Configure reCAPTCHA and Mailchimp settings used by the Form widget.', 'pixels-elementor-addons' ),

			'search'                 => __( 'Search', 'pixels-elementor-addons' ),
			'notifications'          => __( 'Notifications', 'pixels-elementor-addons' ),
			'searchByName'           => __( 'Search by name', 'pixels-elementor-addons' ),
			'searchWidgetsExtensions'=> __( 'Search widgets and extensions...', 'pixels-elementor-addons' ),
			'searchModalTitle'       => __( 'Search widgets and extensions', 'pixels-elementor-addons' ),
			'searchModalDescription' => __( 'Search across widgets and extensions in Pixels Addons.', 'pixels-elementor-addons' ),
			'loadingSearchIndex'     => __( 'Loading search index...', 'pixels-elementor-addons' ),
			'noSearchResults'        => __( 'No widgets or extensions match your search.', 'pixels-elementor-addons' ),
			'keyboardShortcut'       => __( 'Ctrl K', 'pixels-elementor-addons' ),

			'allWidgets'             => __( 'All Widgets', 'pixels-elementor-addons' ),
			'allExtensions'          => __( 'All Extensions', 'pixels-elementor-addons' ),
			'allElements'            => __( 'All Widgets', 'pixels-elementor-addons' ),
			'enabled'                => __( 'Enabled', 'pixels-elementor-addons' ),
			'disabled'               => __( 'Disabled', 'pixels-elementor-addons' ),
			'enableAll'              => __( 'Enable All', 'pixels-elementor-addons' ),
			'enableAllElements'      => __( 'Enable all widgets', 'pixels-elementor-addons' ),
			'enableAllWidgets'       => __( 'Enable all widgets', 'pixels-elementor-addons' ),
			'enableAllWidgetsCaption'=> __( 'Enable All Widgets', 'pixels-elementor-addons' ),
			'enableAllExtensions'    => __( 'Enable all extensions', 'pixels-elementor-addons' ),
			'enableAllExtensionsCaption' => __( 'Enable All Extensions', 'pixels-elementor-addons' ),

			'saving'                 => __( 'Saving...', 'pixels-elementor-addons' ),
			'saveSettings'           => __( 'Save Settings', 'pixels-elementor-addons' ),
			'settingsSaved'          => __( 'Settings saved.', 'pixels-elementor-addons' ),

			'loadingWidgets'         => __( 'Loading widgets from WordPress...', 'pixels-elementor-addons' ),
			'loadingExtensions'      => __( 'Loading extensions from WordPress...', 'pixels-elementor-addons' ),
			'loadingFormSettings'    => __( 'Loading form settings from WordPress...', 'pixels-elementor-addons' ),
			'failedLoadWidgets'      => __( 'Failed to load widget settings from WordPress.', 'pixels-elementor-addons' ),
			'failedSaveWidgets'      => __( 'Failed to save widget settings.', 'pixels-elementor-addons' ),
			'failedLoadExtensions'   => __( 'Failed to load extensions from WordPress.', 'pixels-elementor-addons' ),
			'failedSaveExtensions'   => __( 'Failed to save extension settings.', 'pixels-elementor-addons' ),
			'failedLoadFormSettings' => __( 'Failed to load form settings from WordPress.', 'pixels-elementor-addons' ),
			'failedSaveFormSettings' => __( 'Failed to save form settings.', 'pixels-elementor-addons' ),
			'requestFailed'          => __( 'Request failed. Please try again.', 'pixels-elementor-addons' ),
			'configUnavailable'      => __( 'Pixels Addons dashboard config is not available.', 'pixels-elementor-addons' ),

			'noWidgetsFound'         => __( 'No widgets match your search or filter.', 'pixels-elementor-addons' ),
			'noExtensionsFound'      => __( 'No extensions match your search or filter.', 'pixels-elementor-addons' ),

			'pro'                    => __( 'Pro', 'pixels-elementor-addons' ),
			'upgrade'                => __( 'Upgrade', 'pixels-elementor-addons' ),
			'widgetsLabel'           => __( 'Widgets', 'pixels-elementor-addons' ),
			'extensionsLabel'        => __( 'Extensions', 'pixels-elementor-addons' ),
			'toggleItem'             =>
				/* translators: %s: Widget or extension name. */
				__( 'Toggle %s', 'pixels-elementor-addons' ),
			'itemDocumentation'      =>
				/* translators: %s: Widget or extension name. */
				__( '%s documentation', 'pixels-elementor-addons' ),
			'itemDemo'               =>
				/* translators: %s: Widget or extension name. */
				__( '%s demo', 'pixels-elementor-addons' ),

			'welcomeTitle'           => __( 'Welcome to Pixels Addons', 'pixels-elementor-addons' ),
			'welcomeDescription'     => __( 'Your dashboard is ready. Explore widgets and extensions to build faster.', 'pixels-elementor-addons' ),
			'licenseFree'            => __( 'Free', 'pixels-elementor-addons' ),
			'licenseProActive'       => __( 'Pro Active', 'pixels-elementor-addons' ),
			'licenseProInactive'     => __( 'Pro Inactive', 'pixels-elementor-addons' ),
			'licenseProTeaser'       => __( 'Pixels Addons Pro unlocks premium widgets', 'pixels-elementor-addons' ),
			'noLicenseKey'           => __( 'No license key', 'pixels-elementor-addons' ),
			'activate'               => __( 'Activate', 'pixels-elementor-addons' ),
			'deactivate'             => __( 'Deactivate', 'pixels-elementor-addons' ),
			'getPro'                 => __( 'Get Pro', 'pixels-elementor-addons' ),

			'heroTitle'              => __( 'Design Faster With Pixels Addons Widgets', 'pixels-elementor-addons' ),
			'heroDescription'        => __( 'Learn how to use widgets and extensions to ship polished interfaces without reinventing common UI patterns.', 'pixels-elementor-addons' ),
			'watchTutorials'         => __( 'Watch Tutorials', 'pixels-elementor-addons' ),

			'quickStartTitle'        => __( 'Quick start', 'pixels-elementor-addons' ),
			'quickStartDescriptionOne' => __( 'One step to get the most out of Pixels Addons.', 'pixels-elementor-addons' ),
			'quickStartDescriptionTwo' => __( 'Two steps to get the most out of Pixels Addons.', 'pixels-elementor-addons' ),
			'quickStartDescriptionThree' => __( 'Three steps to get the most out of Pixels Addons.', 'pixels-elementor-addons' ),
			'quickStartStep'         =>
				/* translators: %d: Step number. */
				__( 'Step %d', 'pixels-elementor-addons' ),
			'quickStartWidgetsTitle' => __( 'Enable widgets you need', 'pixels-elementor-addons' ),
			'quickStartWidgetsDescription' => __( 'Turn on only the Elementor widgets you use to keep the editor lean.', 'pixels-elementor-addons' ),
			'quickStartExtensionTitle' => __( 'Turn on site utilities', 'pixels-elementor-addons' ),
			'quickStartExtensionDescription' => __( 'Enable performance, SEO, security, and utility extensions for your site.', 'pixels-elementor-addons' ),
			'quickStartFormTitle'    => __( 'Protect and connect forms', 'pixels-elementor-addons' ),
			'quickStartFormDescription' => __( 'Configure reCAPTCHA and Mailchimp for the Contact Form widget.', 'pixels-elementor-addons' ),

			'whatsNewTitle'          => __( "What's new", 'pixels-elementor-addons' ),
			'whatsNewDescription'    => __( 'Recent updates across the Pixels Addons dashboard.', 'pixels-elementor-addons' ),

			'helpTitle'              => __( 'Need Any Help?', 'pixels-elementor-addons' ),
			'communityTitle'         => __( 'Join Our Community', 'pixels-elementor-addons' ),
			'knowledgeBaseTitle'     => __( 'View Knowledge Base', 'pixels-elementor-addons' ),

			'statsTotal'             => __( 'Total', 'pixels-elementor-addons' ),
			'statsActive'            => __( 'Active', 'pixels-elementor-addons' ),
			'statsInactive'          => __( 'Inactive', 'pixels-elementor-addons' ),
			'statsCountLabel'        =>
				/* translators: 1: Count number, 2: Stat label such as Total or Active. */
				__( '%1$d %2$s', 'pixels-elementor-addons' ),

			'requirementsTitle'      => __( 'Requirements', 'pixels-elementor-addons' ),
			'elementorTitle'         => __( 'Elementor', 'pixels-elementor-addons' ),
			'elementorActive'        => __( 'Active — widgets are available in the editor.', 'pixels-elementor-addons' ),
			'elementorInactive'      => __( 'Not active — install and activate Elementor to use widgets.', 'pixels-elementor-addons' ),
			'nestedElementsTitle'    => __( 'Nested Elements', 'pixels-elementor-addons' ),
			'nestedElementsActive'   => __( 'Enabled for containers that support nesting.', 'pixels-elementor-addons' ),
			'nestedElementsInactive' => __( 'Optional — enable in Elementor for nested layouts.', 'pixels-elementor-addons' ),

			'reviewTitle'            => __( 'Show Your Love', 'pixels-elementor-addons' ),
			'reviewDescription'      => __( 'Enjoying Pixels Addons? A quick review helps the project grow.', 'pixels-elementor-addons' ),
			'leaveReview'            => __( 'Leave a Review →', 'pixels-elementor-addons' ),

			'logoAlt'                => __( 'Pixels Addons', 'pixels-elementor-addons' ),
		];

		/**
		 * Filter dashboard UI strings passed to the React app.
		 *
		 * @param array<string, string> $strings
		 */
		return apply_filters( 'pixels_core_dashboard_i18n', $strings );
	}
}
