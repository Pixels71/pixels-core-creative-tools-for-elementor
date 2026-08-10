<?php
namespace PixelsCoreCreativeToolsForElementor\Admin;

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
			'sidebarIntroduction'  => __( 'Introduction', 'pixels-core-creative-tools-for-elementor' ),
			'sidebarWidgets'       => __( 'Widgets', 'pixels-core-creative-tools-for-elementor' ),
			'sidebarExtension'     => __( 'Extension', 'pixels-core-creative-tools-for-elementor' ),
			'sidebarFormSettings'  => __( 'Form Settings', 'pixels-core-creative-tools-for-elementor' ),
			'dashboardNavLabel'    => __( 'Dashboard', 'pixels-core-creative-tools-for-elementor' ),

			'tabIntroductionTitle'       => __( 'Introduction', 'pixels-core-creative-tools-for-elementor' ),
			'tabIntroductionDescription' => __( 'Get started with Pixels Core and explore what is available.', 'pixels-core-creative-tools-for-elementor' ),
			'tabWidgetsTitle'            => __( 'Widgets', 'pixels-core-creative-tools-for-elementor' ),
			'tabWidgetsDescription'      => __( 'Browse and manage the widgets available in your dashboard.', 'pixels-core-creative-tools-for-elementor' ),
			'tabExtensionTitle'          => __( 'Extension', 'pixels-core-creative-tools-for-elementor' ),
			'tabExtensionDescription'    => __( 'Enable extensions to unlock extra capabilities.', 'pixels-core-creative-tools-for-elementor' ),
			'tabFormSettingsTitle'       => __( 'Form Settings', 'pixels-core-creative-tools-for-elementor' ),
			'tabFormSettingsDescription' => __( 'Configure reCAPTCHA and Mailchimp settings used by the Form widget.', 'pixels-core-creative-tools-for-elementor' ),

			'search'                 => __( 'Search', 'pixels-core-creative-tools-for-elementor' ),
			'notifications'          => __( 'Notifications', 'pixels-core-creative-tools-for-elementor' ),
			'searchByName'           => __( 'Search by name', 'pixels-core-creative-tools-for-elementor' ),
			'searchWidgetsExtensions'=> __( 'Search widgets and extensions...', 'pixels-core-creative-tools-for-elementor' ),
			'searchModalTitle'       => __( 'Search widgets and extensions', 'pixels-core-creative-tools-for-elementor' ),
			'searchModalDescription' => __( 'Search across widgets and extensions in Pixels Core.', 'pixels-core-creative-tools-for-elementor' ),
			'loadingSearchIndex'     => __( 'Loading search index...', 'pixels-core-creative-tools-for-elementor' ),
			'noSearchResults'        => __( 'No widgets or extensions match your search.', 'pixels-core-creative-tools-for-elementor' ),
			'keyboardShortcut'       => __( 'Ctrl K', 'pixels-core-creative-tools-for-elementor' ),

			'allWidgets'             => __( 'All Widgets', 'pixels-core-creative-tools-for-elementor' ),
			'allExtensions'          => __( 'All Extensions', 'pixels-core-creative-tools-for-elementor' ),
			'allElements'            => __( 'All Widgets', 'pixels-core-creative-tools-for-elementor' ),
			'enabled'                => __( 'Enabled', 'pixels-core-creative-tools-for-elementor' ),
			'disabled'               => __( 'Disabled', 'pixels-core-creative-tools-for-elementor' ),
			'enableAll'              => __( 'Enable All', 'pixels-core-creative-tools-for-elementor' ),
			'enableAllElements'      => __( 'Enable all widgets', 'pixels-core-creative-tools-for-elementor' ),
			'enableAllWidgets'       => __( 'Enable all widgets', 'pixels-core-creative-tools-for-elementor' ),
			'enableAllWidgetsCaption'=> __( 'Enable All Widgets', 'pixels-core-creative-tools-for-elementor' ),
			'enableAllExtensions'    => __( 'Enable all extensions', 'pixels-core-creative-tools-for-elementor' ),
			'enableAllExtensionsCaption' => __( 'Enable All Extensions', 'pixels-core-creative-tools-for-elementor' ),

			'saving'                 => __( 'Saving...', 'pixels-core-creative-tools-for-elementor' ),
			'saveSettings'           => __( 'Save Settings', 'pixels-core-creative-tools-for-elementor' ),
			'settingsSaving'         => __( 'Setting saving...', 'pixels-core-creative-tools-for-elementor' ),
			'settingsSaved'          => __( 'Setting Save Successfully', 'pixels-core-creative-tools-for-elementor' ),
			'settingsSavedDescription' => __( 'Your changes have been saved.', 'pixels-core-creative-tools-for-elementor' ),
			'failedSaveSettings'     => __( 'Failed to save settings.', 'pixels-core-creative-tools-for-elementor' ),

			'loadingWidgets'         => __( 'Loading widgets from WordPress...', 'pixels-core-creative-tools-for-elementor' ),
			'loadingExtensions'      => __( 'Loading extensions from WordPress...', 'pixels-core-creative-tools-for-elementor' ),
			'loadingFormSettings'    => __( 'Loading form settings from WordPress...', 'pixels-core-creative-tools-for-elementor' ),
			'failedLoadWidgets'      => __( 'Failed to load widget settings from WordPress.', 'pixels-core-creative-tools-for-elementor' ),
			'failedSaveWidgets'      => __( 'Failed to save widget settings.', 'pixels-core-creative-tools-for-elementor' ),
			'failedLoadExtensions'   => __( 'Failed to load extensions from WordPress.', 'pixels-core-creative-tools-for-elementor' ),
			'failedSaveExtensions'   => __( 'Failed to save extension settings.', 'pixels-core-creative-tools-for-elementor' ),
			'failedLoadFormSettings' => __( 'Failed to load form settings from WordPress.', 'pixels-core-creative-tools-for-elementor' ),
			'failedSaveFormSettings' => __( 'Failed to save form settings.', 'pixels-core-creative-tools-for-elementor' ),
			'requestFailed'          => __( 'Request failed. Please try again.', 'pixels-core-creative-tools-for-elementor' ),
			'configUnavailable'      => __( 'Pixels Core dashboard config is not available.', 'pixels-core-creative-tools-for-elementor' ),

			'noWidgetsFound'         => __( 'No widgets match your search or filter.', 'pixels-core-creative-tools-for-elementor' ),
			'noExtensionsFound'      => __( 'No extensions match your search or filter.', 'pixels-core-creative-tools-for-elementor' ),

			'pro'                    => __( 'Pro', 'pixels-core-creative-tools-for-elementor' ),
			'upgrade'                => __( 'Upgrade', 'pixels-core-creative-tools-for-elementor' ),
			'widgetsLabel'           => __( 'Widgets', 'pixels-core-creative-tools-for-elementor' ),
			'extensionsLabel'        => __( 'Extensions', 'pixels-core-creative-tools-for-elementor' ),
			'toggleItem'             =>
				/* translators: %s: Widget or extension name. */
				__( 'Toggle %s', 'pixels-core-creative-tools-for-elementor' ),
			'itemDocumentation'      =>
				/* translators: %s: Widget or extension name. */
				__( '%s documentation', 'pixels-core-creative-tools-for-elementor' ),
			'itemDemo'               =>
				/* translators: %s: Widget or extension name. */
				__( '%s demo', 'pixels-core-creative-tools-for-elementor' ),

			'welcomeTitle'           => __( 'Welcome to Pixels Core', 'pixels-core-creative-tools-for-elementor' ),
			'welcomeDescription'     => __( 'Your dashboard is ready. Explore widgets and extensions to build faster.', 'pixels-core-creative-tools-for-elementor' ),
			'licenseFree'            => __( 'Free', 'pixels-core-creative-tools-for-elementor' ),
			'licenseProActive'       => __( 'Pro Active', 'pixels-core-creative-tools-for-elementor' ),
			'licenseProInactive'     => __( 'Pro Inactive', 'pixels-core-creative-tools-for-elementor' ),
			'licenseProTeaser'       => __( 'Pixels Core Pro unlocks premium widgets', 'pixels-core-creative-tools-for-elementor' ),
			'noLicenseKey'           => __( 'No license key', 'pixels-core-creative-tools-for-elementor' ),
			'activate'               => __( 'Activate', 'pixels-core-creative-tools-for-elementor' ),
			'deactivate'             => __( 'Deactivate', 'pixels-core-creative-tools-for-elementor' ),
			'getPro'                 => __( 'Get Pro', 'pixels-core-creative-tools-for-elementor' ),

			'heroTitle'              => __( 'Design Faster With Pixels Core Widgets', 'pixels-core-creative-tools-for-elementor' ),
			'heroDescription'        => __( 'Learn how to use widgets and extensions to ship polished interfaces without reinventing common UI patterns.', 'pixels-core-creative-tools-for-elementor' ),
			'watchTutorials'         => __( 'Watch Tutorials', 'pixels-core-creative-tools-for-elementor' ),

			'quickStartTitle'        => __( 'Quick start', 'pixels-core-creative-tools-for-elementor' ),
			'quickStartDescriptionOne' => __( 'One step to get the most out of Pixels Core.', 'pixels-core-creative-tools-for-elementor' ),
			'quickStartDescriptionTwo' => __( 'Two steps to get the most out of Pixels Core.', 'pixels-core-creative-tools-for-elementor' ),
			'quickStartDescriptionThree' => __( 'Three steps to get the most out of Pixels Core.', 'pixels-core-creative-tools-for-elementor' ),
			'quickStartStep'         =>
				/* translators: %d: Step number. */
				__( 'Step %d', 'pixels-core-creative-tools-for-elementor' ),
			'quickStartWidgetsTitle' => __( 'Enable widgets you need', 'pixels-core-creative-tools-for-elementor' ),
			'quickStartWidgetsDescription' => __( 'Turn on only the Elementor widgets you use to keep the editor lean.', 'pixels-core-creative-tools-for-elementor' ),
			'quickStartExtensionTitle' => __( 'Turn on site utilities', 'pixels-core-creative-tools-for-elementor' ),
			'quickStartExtensionDescription' => __( 'Enable performance, SEO, security, and utility extensions for your site.', 'pixels-core-creative-tools-for-elementor' ),
			'quickStartFormTitle'    => __( 'Protect and connect forms', 'pixels-core-creative-tools-for-elementor' ),
			'quickStartFormDescription' => __( 'Configure reCAPTCHA and Mailchimp for the Contact Form widget.', 'pixels-core-creative-tools-for-elementor' ),

			'whatsNewTitle'          => __( "What's new", 'pixels-core-creative-tools-for-elementor' ),
			'whatsNewDescription'    => __( 'Recent updates across the Pixels Core dashboard.', 'pixels-core-creative-tools-for-elementor' ),

			'helpTitle'              => __( 'Need Any Help?', 'pixels-core-creative-tools-for-elementor' ),
			'communityTitle'         => __( 'Join Our Community', 'pixels-core-creative-tools-for-elementor' ),
			'knowledgeBaseTitle'     => __( 'View Knowledge Base', 'pixels-core-creative-tools-for-elementor' ),

			'statsTotal'             => __( 'Total', 'pixels-core-creative-tools-for-elementor' ),
			'statsActive'            => __( 'Active', 'pixels-core-creative-tools-for-elementor' ),
			'statsInactive'          => __( 'Inactive', 'pixels-core-creative-tools-for-elementor' ),
			'statsCountLabel'        =>
				/* translators: 1: Count number, 2: Stat label such as Total or Active. */
				__( '%1$d %2$s', 'pixels-core-creative-tools-for-elementor' ),

			'requirementsTitle'      => __( 'Requirements', 'pixels-core-creative-tools-for-elementor' ),
			'elementorTitle'         => __( 'Elementor', 'pixels-core-creative-tools-for-elementor' ),
			'elementorActive'        => __( 'Active — widgets are available in the editor.', 'pixels-core-creative-tools-for-elementor' ),
			'elementorInactive'      => __( 'Not active — install and activate Elementor to use widgets.', 'pixels-core-creative-tools-for-elementor' ),
			'nestedElementsTitle'    => __( 'Nested Elements', 'pixels-core-creative-tools-for-elementor' ),
			'nestedElementsActive'   => __( 'Enabled for containers that support nesting.', 'pixels-core-creative-tools-for-elementor' ),
			'nestedElementsInactive' => __( 'Optional — enable in Elementor for nested layouts.', 'pixels-core-creative-tools-for-elementor' ),

			'reviewTitle'            => __( 'Show Your Love', 'pixels-core-creative-tools-for-elementor' ),
			'reviewDescription'      => __( 'Enjoying Pixels Core? A quick review helps the project grow.', 'pixels-core-creative-tools-for-elementor' ),
			'leaveReview'            => __( 'Leave a Review →', 'pixels-core-creative-tools-for-elementor' ),

			'logoAlt'                => __( 'Pixels Core', 'pixels-core-creative-tools-for-elementor' ),
		];

		/**
		 * Filter dashboard UI strings passed to the React app.
		 *
		 * @param array<string, string> $strings
		 */
		return apply_filters( 'pixeccte_dashboard_i18n', $strings );
	}
}
