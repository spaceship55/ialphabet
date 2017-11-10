<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Container;

/**
 * @property string $file
 * @property string $id
 * @property string $name
 * @property string $path
 * @property string $prefix
 * @property string $url
 * @property string $version
 * @property array $defaults
 * @property array $mceShortcodes
 */
final class App extends Container
{
	const CAPABILITY = 'edit_others_pages';
	const POST_TYPE = 'site-review';
	const TAXONOMY = 'site-review-category';

	protected $defaults;
	protected $file;
	protected $id;
	protected $name;
	protected $path;
	protected $prefix;
	protected $url;
	protected $version;

	public function __construct()
	{
		// hardcoded path to the plugin file
		$file = realpath( dirname( __DIR__ ) . '/site-reviews.php' );

		$data = [
			'id'      => 'Text Domain',
			'name'    => 'Plugin Name',
			'version' => 'Version',
		];

		$plugin = get_file_data( $file, $data, 'plugin' );

		$this->file    = $file;
		$this->id      = 'geminilabs-' . $plugin['id'];
		$this->name    = $plugin['name'];
		$this->path    = plugin_dir_path( $file );
		$this->prefix  = str_replace( '-', '_', $this->id );
		$this->url     = plugin_dir_url( $file );
		$this->version = $plugin['version'];
	}

	/**
	 * This is the Application entry point
	 *
	 * @return void
	 */
	public function init()
	{
		$basename = plugin_basename( $this->file );

		$main = $this->make( 'Controllers\MainController' );
		$review = $this->make( 'Controllers\ReviewController' );
		$router = $this->make( 'Router' );
		$translator = $this->make( 'Translator' );

		// Action Hooks
		add_action( 'plugins_loaded',                        [ $this, 'getDefaultSettings'], 11 );
		add_action( 'plugins_loaded',                        [ $this, 'registerAddons'] );
		add_action( 'plugins_loaded',                        [ $this, 'registerLanguages'] );
		add_action( 'upgrader_process_complete',             [ $this, 'upgrader'], 10, 2 );
		add_action( 'admin_enqueue_scripts',                 [ $main, 'enqueueAssets'] );
		add_action( 'wp_enqueue_scripts',                    [ $main, 'enqueueAssets'], 999 );
		add_action( 'admin_menu',                            [ $main, 'registerMenuCount'] );
		add_action( 'add_meta_boxes',                        [ $main, 'registerMetaBox'] );
		add_action( 'admin_enqueue_scripts',                 [ $main, 'registerPointers'], 13 );
		add_action( 'init',                                  [ $main, 'registerPostType'], 8 );
		add_action( 'admin_init',                            [ $main, 'registerSettings'] );
		add_action( 'admin_init',                            [ $main, 'registerShortcodeButtons'] );
		add_action( 'init',                                  [ $main, 'registerShortcodes'] );
		add_action( 'admin_menu',                            [ $main, 'registerSubMenus'] );
		add_action( 'init',                                  [ $main, 'registerTaxonomy'] );
		add_action( 'widgets_init',                          [ $main, 'registerWidgets'] );
		add_action( 'post_submitbox_misc_actions',           [ $main, 'renderMetaBoxPinned'] );
		add_action( 'edit_form_after_title',                 [ $main, 'renderReview'] );
		add_action( 'edit_form_top',                         [ $main, 'renderReviewNotice'] );
		add_action( 'media_buttons',                         [ $main, 'renderTinymceButton'], 11 );
		add_action( 'wp_footer',                             [ $main, 'renderSchema'] );
		add_action( 'admin_action_approve',                  [ $review, 'approve'] );
		add_action( 'admin_print_scripts',                   [ $review, 'modifyAutosave'], 999 );
		add_action( 'current_screen',                        [ $review, 'modifyFeatures'] );
		add_action( 'admin_menu',                            [ $review, 'removeMetaBoxes'] );
		add_action( 'admin_action_revert',                   [ $review, 'revert'] );
		add_action( 'save_post_' . static::POST_TYPE,        [ $review, 'saveEditedReview'] );
		add_action( 'admin_init',                            [ $review, 'setPermissions'], 999 );
		add_action( 'admin_action_unapprove',                [ $review, 'unapprove'] );
		add_action( "wp_ajax_{$this->prefix}_action",        [ $router, 'routeAjaxRequests'] );
		add_action( "wp_ajax_nopriv_{$this->prefix}_action", [ $router, 'routeAjaxRequests'] );
		add_action( 'admin_init',                            [ $router, 'routePostRequests'] );
		add_action( 'init',                                  [ $router, 'routePublicPostRequests'] );
		add_action( 'admin_init',                            [ $router, 'routeWebhookRequests'] );

		// Filter Hooks
		add_filter( 'script_loader_tag',               [ $main, 'filterEnqueuedScripts'], 10, 2 );
		add_filter( "plugin_action_links_{$basename}", [ $main, 'registerActionLinks'] );
		add_filter( 'dashboard_glance_items',          [ $main, 'registerDashboardGlanceItems'] );
		add_filter( 'post_row_actions',                [ $main, 'registerRowActions'], 10, 2 );
		add_filter( 'wp_editor_settings',              [ $review, 'modifyEditor'] );
		add_filter( 'the_editor',                      [ $review, 'modifyEditorTextarea'] );
		add_filter( 'ngettext',                        [ $review, 'modifyStatusFilter'], 10, 5 );
		add_filter( 'post_updated_messages',           [ $review, 'modifyUpdateMessages'] );
		add_filter( 'bulk_post_updated_messages',      [ $review, 'modifyUpdateMessagesBulk'], 10, 2 );
		add_filter( 'gettext',                         [ $translator, 'translateGettext'], 10, 3 );
		add_filter( 'gettext_with_context',            [ $translator, 'translateGettextWithContext'], 10, 4 );
		add_filter( 'ngettext',                        [ $translator, 'translateNgettext'], 10, 5 );
		add_filter( 'ngettext_with_context',           [ $translator, 'translateNgettextWithContext'], 10, 6 );
	}

	/**
	 * Runs on plugin activation
	 *
	 * @return void
	 */
	public function activate()
	{
		$this->upgrade();

		$this->make( 'Database' )->setDefaults();
		$this->make( 'Database' )->setOption( 'logging', 0 );

		// Schedule session purge
		if( !wp_next_scheduled( 'site-reviews/schedule/session/purge' )) {
			wp_schedule_event( time(), 'twicedaily', 'site-reviews/schedule/session/purge' );
		}
	}

	/**
	 * Runs on plugin deactivation
	 *
	 * @return void
	 */
	public function deactivate()
	{
		$events = ['site-reviews/schedule/session/purge'];

		foreach( $events as $event ) {
			wp_unschedule_event( wp_next_scheduled( $event ), $event );
		}
	}

	/**
	 * Get the default settings
	 *
	 * @return array
	 */
	public function getDefaultSettings()
	{
		if( !$this->defaults ) {

			$this->defaults = $this->make( 'Settings' )->getSettings();

			// Allow addons to modify the default settings
			$this->defaults = apply_filters( 'site-reviews/addon/defaults', $this->defaults );

			$this->upgrade();
		}

		return $this->defaults;
	}

	/**
	 * Verify permissions
	 *
	 * @return bool
	 */
	public function hasPermission()
	{
		return !is_admin() || ( is_admin() && current_user_can( static::CAPABILITY ));
	}

	/**
	 * Register available add-ons
	 *
	 * @return void
	 */
	public function registerAddons()
	{
		do_action( 'site-reviews/addon/register', $this );
	}

	/**
	 * Register available languages
	 *
	 * @return void
	 */
	public function registerLanguages()
	{
		load_plugin_textdomain( 'site-reviews', false, plugin_basename( $this->path ) . '/languages/' );
	}

	/**
	 * Update the plugin versions in the database
	 *
	 * @param string $current
	 *
	 * @return void
	 */
	public function updateVersion( $current = '' )
	{
		$db = $this->make( 'Database' );

		if( empty( $current )) {
			$current = $db->getOption( 'version' );
		}

		if( version_compare( $current, $this->version, '<' )) {
			$db->setOption( 'version', $this->version );
		}

		if( $current != $this->version ) {
			$db->setOption( 'version_upgraded_from', $current );
		}
	}

	/**
	 * Upgrade routine
	 *
	 * @return void
	 */
	public function upgrade()
	{
		$version = $this->make( 'Database' )->getOption( 'version' );
		$upgrade = $this->make( 'Upgrade' );

		if( version_compare( $version, '2.0.0', '<' )) {
			$upgrade->options_200();
			$upgrade->reviewSlug_200();
			$upgrade->reviewType_200();
			$upgrade->sidebarWidgets_200();
			$upgrade->themeMods_200();
			$upgrade->widgetSiteReviews_200();
			$upgrade->widgetSiteReviewsForm_200();
		}
		if( version_compare( $version, '2.1.0', '<' )) {
			$upgrade->reviewAssignedTo_210();
		}
		if( version_compare( $version, '2.3.0', '<' )) {
			$upgrade->options_230();
			$upgrade->translations_230();
		}
		if( version_compare( $version, '2.6.0', '<' )) {
			$upgrade->options_260();
		}

		$this->updateVersion( $version );
	}

	/**
	 * Runs on plugin upgrade
	 *
	 * @param mixed $upgrader
	 *
	 * @return null|void
	 */
	public function upgrader( $upgrader, array $data )
	{
		if( !array_key_exists( 'plugins', $data )
			|| !in_array( plugin_basename( $this->file ), $data['plugins'] )
			|| $data['action'] != 'update'
			|| $data['type'] != 'plugin'
		)return;

		$this->upgrade();
	}
}
