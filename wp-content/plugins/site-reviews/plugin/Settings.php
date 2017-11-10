<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\App;
use GeminiLabs\SiteReviews\Html;
use GeminiLabs\SiteReviews\Translator;
use ReflectionClass;
use ReflectionMethod;

class Settings
{
	/**
	 * @var App
	 */
	protected $app;

	/**
	 * @var Html
	 */
	protected $html;

	/**
	 * @var array
	 */
	protected $settings;

	public function __construct( App $app, Html $html )
	{
		$this->app      = $app;
		$this->html     = $html;
		$this->settings = [];
	}

	/**
	 * Add a setting default
	 *
	 * @param string $formId
	 *
	 * @return void
	 */
	public function addSetting( $formId, array $args )
	{
		$args = $this->normalizePaths( $formId, $args );

		if( isset( $args['name'] )) {
			$this->settings[ $args['name']] = $this->getDefault( $args );
		}

		$this->html->addfield( $formId, $args );
	}

	/**
	 * Get the default field value
	 *
	 * @return string
	 */
	public function getDefault( array $args )
	{
		isset( $args['default'] ) ?: $args['default'] = '';
		isset( $args['placeholder'] ) ?: $args['placeholder'] = '';

		if( $args['default'] === ':placeholder' ) {
			$args['default'] = $args['placeholder'];
		}

		if( strpos( $args['type'], 'yesno' ) !== false && empty( $args['default'] )) {
			$args['default'] = 'no';
		}

		return $args['default'];
	}

	/**
	 * Get the default settings
	 *
	 * @return array
	 */
	public function getSettings()
	{
		$this->register();

		return $this->settings;
	}

	/**
	 * @param string $path
	 * @param string $prefix
	 *
	 * @return string
	 */
	public function normalizePath( $path, $prefix )
	{
		return substr( $path, 0, strlen( $prefix )) != $prefix
			? sprintf( '%s.%s', $prefix, $path )
			: $path;
	}

	/**
	 * @param string $formId
	 *
	 * @return array
	 */
	public function normalizePaths( $formId, array $args )
	{
		$prefix = strtolower( str_replace( '/', '.', $formId ));

		if( isset( $args['name'] ) && is_string( $args['name'] )) {
			$args['name'] = $this->normalizePath( $args['name'], $prefix );
		}

		if( isset( $args['depends'] ) && is_array( $args['depends'] )) {
			$depends = [];
			foreach( $args['depends'] as $path => $value ) {
				$depends[ $this->normalizePath( $path, $prefix ) ] = $value;
			}
			$args['depends'] = $depends;
		}

		return $args;
	}

	/**
	 * Register the settings for each form
	 *
	 * @return void
	 *
	 * @action admin_init
	 */
	public function register()
	{
		if( !empty( $this->settings ))return;

		$methods = (new ReflectionClass( __CLASS__ ))->getMethods( ReflectionMethod::IS_PROTECTED );

		foreach( $methods as $method ) {
			if( substr( $method->name, 0, 3 ) === 'set' ) {
				$this->{$method->name}();
			}
		}
	}

	/**
	 * @return void
	 */
	protected function setGeneral()
	{
		$formId = 'settings/general';

		$this->html->createForm( $formId, [
			'action' => admin_url( 'options.php' ),
			'nonce'  => $this->app->id . '-settings',
			'submit' => __( 'Save Settings', 'site-reviews' ),
		]);

		$this->addSetting( $formId, [
			'type'    => 'yesno_inline',
			'name'    => 'require.approval',
			'label'   => __( 'Require approval', 'site-reviews' ),
			'default' => 'yes',
			'desc'    => __( 'Set the status of new review submissions to pending.', 'site-reviews' ),
		]);

		$this->addSetting( $formId, [
			'type'  => 'yesno_inline',
			'name'  => 'require.login',
			'label' => __( 'Require login', 'site-reviews' ),
			'desc'  => __( 'Only allow review submissions from registered users.', 'site-reviews' ),
		]);

		$this->addSetting( $formId, [
			'type'    => 'yesno_inline',
			'name'    => 'require.login_register',
			'label'   => __( 'Show registration link', 'site-reviews' ),
			'depends' => [
				'require.login' => 'yes',
			],
			'desc' => sprintf( __( 'Show a link for a new user to register. The %s Membership option must be enabled in General Settings for this to work.', 'site-reviews' ),
				sprintf( '<a href="%s">%s</a>', admin_url( 'options-general.php' ), __( 'Anyone can register', 'site-reviews' ))
			),
		]);

		$this->addSetting( $formId, [
			'type'    => 'radio',
			'name'    => 'notification',
			'label'   => __( 'Notifications', 'site-reviews' ),
			'default' => 'none',
			'options' => [
				'none'    => __( 'Do not send review notifications', 'site-reviews' ),
				'default' => __( 'Send to administrator', 'site-reviews' ) . sprintf( ' <code>%s</code>', get_option( 'admin_email' )),
				'custom'  => __( 'Send to one or more email addresses', 'site-reviews' ),
				'webhook' => sprintf( __( 'Send to %s', 'site-reviews' ), '<a href="https://slack.com/">Slack</a>' ),
			],
		]);

		$this->addSetting( $formId, [
			'type'    => 'text',
			'name'    => 'notification_email',
			'label'   => __( 'Send notification emails to', 'site-reviews' ),
			'depends' => [
				'notification' => 'custom',
			],
			'placeholder' => __( 'Separate multiple emails with a comma', 'site-reviews' ),
		]);

		$this->addSetting( $formId, [
			'type'    => 'url',
			'name'    => 'webhook_url',
			'label'   => __( 'Webhook URL', 'site-reviews' ),
			'depends' => [
				'notification' => 'webhook',
			],
			'desc' => sprintf( __( 'To send notifications to Slack, create a new %s and then paste the provided Webhook URL in the field above.', 'site-reviews' ),
				sprintf( '<a href="%s">%s</a>', esc_url( 'https://slack.com/apps/new/A0F7XDUAZ-incoming-webhooks' ), __( 'Incoming WebHook', 'site-reviews' ))
			),
		]);

		$this->addSetting( $formId, [
			'type'    => 'code',
			'name'    => 'notification_message',
			'label'   => __( 'Notification template', 'site-reviews' ),
			'rows'    => 9,
			'depends' => [
				'notification' => ['custom', 'default', 'webhook'],
			],
			'default' => $this->html->renderTemplate( 'email/templates/review-notification', [] ),
			'desc' => 'To restore the default text, save an empty template.
				If you are sending notifications to Slack then this template will only be used as a fallback in the event that <a href="https://api.slack.com/docs/attachments">Message Attachments</a> have been disabled.<br>
				Available template tags:<br>
				<code>{review_rating}</code> - The review rating number (1-5)<br>
				<code>{review_title}</code> - The review title<br>
				<code>{review_content}</code> - The review content<br>
				<code>{review_author}</code> - The review author<br>
				<code>{review_email}</code> - The email of the review author<br>
				<code>{review_ip}</code> - The IP address of the review author<br>
				<code>{review_link}</code> - The link to edit/view a review',
		]);
	}

	/**
	 * @return void
	 */
	protected function setReviews()
	{
		$formId = 'settings/reviews';

		$this->html->createForm( $formId, [
			'action' => admin_url( 'options.php' ),
			'nonce'  => $this->app->id . '-settings',
			'submit' => __( 'Save Settings', 'site-reviews' ),
		]);

		$this->addSetting( $formId, [
			'type'  => 'select',
			'name'  => 'date.format',
			'label' => __( 'Date Format', 'site-reviews' ),
			'options' => [
				'default' => __( 'Use the default date format', 'site-reviews' ),
				'relative' => __( 'Use a relative date format', 'site-reviews' ),
				'custom' => __( 'Use a custom date format', 'site-reviews' ),
			],
			'desc'  => sprintf( __( 'The default date format is the one set in your %s.', 'site-reviews' ),
				sprintf( '<a href="%s">%s<a>', get_admin_url( null, 'options-general.php' ), __( 'WordPress settings', 'site-reviews' ))
			),
		]);

		$this->addSetting( $formId, [
			'type'    => 'text',
			'name'    => 'date.custom',
			'label'   => __( 'Custom Date Format', 'site-reviews' ),
			'default' => get_option( 'date_format' ),
			'desc'    => sprintf( __( 'Enter a custom date format (%s).', 'site-reviews' ),
				sprintf( '<a href="https://codex.wordpress.org/Formatting_Date_and_Time">%s</a>', __( 'documentation on date and time formatting', 'site-reviews' ))
			),
			'depends' => [
				'date.format' => 'custom',
			],
		]);

		$this->addSetting( $formId, [
			'type'  => 'yesno_inline',
			'name'  => 'avatars.enabled',
			'label' => __( 'Enable Avatars', 'site-reviews' ),
			'desc'  => sprintf( __( 'Display reviewer avatars. These are generated from the email address of the reviewer using %s.', 'site-reviews' ),
				sprintf( '<a href="https://gravatar.com">%s</a>', __( 'Gravatar', 'site-reviews' ))
			),
		]);

		$this->addSetting( $formId, [
			'type'  => 'yesno_inline',
			'name'  => 'excerpt.enabled',
			'label' => __( 'Enable Excerpts', 'site-reviews' ),
			'desc'  => __( 'Display an excerpt instead of the full review.', 'site-reviews' ),
		]);

		$this->addSetting( $formId, [
			'type'    => 'number',
			'name'    => 'excerpt.length',
			'label'   => __( 'Excerpt Length', 'site-reviews' ),
			'default' => '55',
			'desc'    => __( 'Set the excerpt word length.', 'site-reviews' ),
			'depends' => [
				'excerpt.enabled' => 'yes',
			],
		]);

		$this->html->addfield( $formId, [
			'type'  => 'heading',
			'value' => __( 'Rich Snippets (schema.org)', 'site-reviews' ),
			'desc'  => __( 'Review snippets appear in Google Search results and include the star rating and other summary info from your reviews.', 'site-reviews' ),
		]);

		$this->addSetting( $formId, [
			'type'  => 'select',
			'name'  => 'schema.type.default',
			'label' => __( 'Default Schema Type', 'site-reviews' ),
			'default' => 'LocalBusiness',
			'options' => [
				'LocalBusiness' => __( 'Local Business', 'site-reviews' ),
				'Product' => __( 'Product', 'site-reviews' ),
				'custom' => __( 'Custom', 'site-reviews' ),
			],
			'desc' => sprintf( __( 'This is the default schema type for the item being reviewed. You can override this option on a per-post/page basis by adding a %s metadata value using %s.', 'site-reviews' ),
				'<code>schema_type</code>',
				sprintf( '<a href="https://codex.wordpress.org/Using_Custom_Fields#Usage">%s</a>', __( 'Custom Fields', 'site-reviews' ))
			),
		]);

		$this->addSetting( $formId, [
			'type'  => 'text',
			'name'  => 'schema.type.custom',
			'label' => __( 'Custom Schema Type', 'site-reviews' ),
			'depends' => [
				'schema.type.default' => 'custom',
			],
			'desc' => sprintf(
				__( 'Google supports review ratings for the following schema content types: Local businesses, Movies, Books, Music, and Products. %s', 'site-reviews' ),
				sprintf( '<a href="https://schema.org/docs/schemas.html">%s</a>', __( 'View more information on schema types here.', 'site-reviews' ))
			),
		]);

		$this->addSetting( $formId, [
			'type'  => 'select',
			'name'  => 'schema.name.default',
			'label' => __( 'Default Name', 'site-reviews' ),
			'default' => 'post',
			'options' => [
				'post' => __( 'Use the assigned or current page title', 'site-reviews' ),
				'custom' => __( 'Enter a custom title', 'site-reviews' ),
			],
			'desc' => sprintf( __( 'This is the default name of the item being reviewed. You can override this option on a per-post/page basis by adding a %s metadata value using %s.', 'site-reviews' ),
				'<code>schema_name</code>',
				sprintf( '<a href="https://codex.wordpress.org/Using_Custom_Fields#Usage">%s</a>', __( 'Custom Fields', 'site-reviews' ))
			),
		]);

		$this->addSetting( $formId, [
			'type'  => 'text',
			'name'  => 'schema.name.custom',
			'label' => __( 'Custom Name', 'site-reviews' ),
			'depends' => [
				'schema.name.default' => 'custom',
			],
		]);

		$this->addSetting( $formId, [
			'type'  => 'select',
			'name'  => 'schema.description.default',
			'label' => __( 'Default Description', 'site-reviews' ),
			'default' => 'post',
			'options' => [
				'post' => __( 'Use the assigned or current page excerpt', 'site-reviews' ),
				'custom' => __( 'Enter a custom description', 'site-reviews' ),
			],
			'desc' => sprintf( __( 'This is the default description for the item being reviewed. You can override this option on a per-post/page basis by adding a %s metadata value using %s.', 'site-reviews' ),
				'<code>schema_description</code>',
				sprintf( '<a href="https://codex.wordpress.org/Using_Custom_Fields#Usage">%s</a>', __( 'Custom Fields', 'site-reviews' ))
			),
		]);

		$this->addSetting( $formId, [
			'type'  => 'text',
			'name'  => 'schema.description.custom',
			'label' => __( 'Custom Description', 'site-reviews' ),
			'depends' => [
				'schema.description.default' => 'custom',
			],
		]);

		$this->addSetting( $formId, [
			'type'  => 'select',
			'name'  => 'schema.url.default',
			'label' => __( 'Default URL', 'site-reviews' ),
			'default' => 'post',
			'options' => [
				'post' => __( 'Use the assigned or current page URL', 'site-reviews' ),
				'custom' => __( 'Enter a custom URL', 'site-reviews' ),
			],
			'desc' => sprintf( __( 'This is the default URL for the item being reviewed. You can override this option on a per-post/page basis by adding a %s metadata value using %s.', 'site-reviews' ),
				'<code>schema_url</code>',
				sprintf( '<a href="https://codex.wordpress.org/Using_Custom_Fields#Usage">%s</a>', __( 'Custom Fields', 'site-reviews' ))
			),
		]);

		$this->addSetting( $formId, [
			'type'  => 'text',
			'name'  => 'schema.url.custom',
			'label' => __( 'Custom URL', 'site-reviews' ),
			'depends' => [
				'schema.url.default' => 'custom',
			],
		]);

		$this->addSetting( $formId, [
			'type'  => 'select',
			'name'  => 'schema.image.default',
			'label' => __( 'Default Image', 'site-reviews' ),
			'default' => 'post',
			'options' => [
				'post' => __( 'Use the featured image of the assigned or current page', 'site-reviews' ),
				'custom' => __( 'Enter a custom image URL', 'site-reviews' ),
			],
			'desc' => sprintf( __( 'This is the default image for the item being reviewed. You can override this option on a per-post/page basis by adding a %s metadata value using %s.', 'site-reviews' ),
				'<code>schema_image</code>',
				sprintf( '<a href="https://codex.wordpress.org/Using_Custom_Fields#Usage">%s</a>', __( 'Custom Fields', 'site-reviews' ))
			),
		]);

		$this->addSetting( $formId, [
			'type'  => 'text',
			'name'  => 'schema.image.custom',
			'label' => __( 'Custom Image URL', 'site-reviews' ),
			'depends' => [
				'schema.image.default' => 'custom',
			],
		]);
	}

	/**
	 * @return void
	 */
	protected function setReviewsForm()
	{
		$formId = 'settings/reviews-form';

		$this->html->createForm( $formId, [
			'action' => admin_url( 'options.php' ),
			'nonce'  => $this->app->id . '-settings',
			'submit' => __( 'Save Settings', 'site-reviews' ),
		]);

		$this->addSetting( $formId, [
			'type'    => 'checkbox',
			'name'    => 'required',
			'label'   => __( 'Required Fields', 'site-reviews' ),
			'default' => ['title','content','name','email'],
			'options' => [
				'title' => __( 'Title', 'site-reviews' ),
				'content' => __( 'Review', 'site-reviews' ),
				'name' => __( 'Name', 'site-reviews' ),
				'email' => __( 'Email', 'site-reviews' ),
			],
		]);

		$this->addSetting( $formId, [
			'type'  => 'select',
			'name'  => 'recaptcha.integration',
			'label' => __( 'Invisible reCAPTCHA', 'site-reviews' ),
			'options' => [
				'' => __( 'Do not use reCAPTCHA', 'site-reviews' ),
				'custom' => __( 'Use reCAPTCHA', 'site-reviews' ),
				'invisible-recaptcha' => _x( 'Use 3rd-party plugin: Invisible reCaptcha', 'plugin name', 'site-reviews' ),
			],
			'desc'  => sprintf( __( 'Invisible reCAPTCHA is a free anti-spam service from Google. To use it, you will need to %s for an API key pair for your site. If you are already using a reCAPTCHA plugin listed here, please select it; otherwise choose "Use reCAPTCHA".', 'site-reviews' ),
				sprintf( '<a href="http://www.google.com/recaptcha/admin" target="_blank">%s</a>', __( 'sign up', 'site-reviews' ))
			),
		]);

		$this->addSetting( $formId, [
			'type'  => 'text',
			'name'  => 'recaptcha.key',
			'label' => __( 'Site Key', 'site-reviews' ),
			'depends' => [
				'recaptcha.integration' => 'custom',
			],
		]);

		$this->addSetting( $formId, [
			'type'  => 'text',
			'name'  => 'recaptcha.secret',
			'label' => __( 'Site Secret', 'site-reviews' ),
			'depends' => [
				'recaptcha.integration' => 'custom',
			],
		]);

		$this->addSetting( $formId, [
			'type'  => 'select',
			'name'  => 'recaptcha.position',
			'label' => __( 'Badge Position', 'site-reviews' ),
			'options' => [
				'bottomleft' => 'Bottom Left',
				'bottomright' => 'Bottom Right',
				'inline' => 'Inline',
			],
			'depends' => [
				'recaptcha.integration' => 'custom',
			],
		]);
	}

	/**
	 * @return void
	 */
	protected function setStrings()
	{
		$formId = 'settings/strings';

		$this->html->createForm( $formId, [
			'action' => admin_url( 'options.php' ),
			'class'  => 'glsr-strings-form',
			'nonce'  => $this->app->id . '-settings',
			'submit' => __( 'Save Settings', 'site-reviews' ),
		]);

		// This exists for when there are no custom translations
		$this->addSetting( $formId, [
			'type' => 'hidden',
			'name' => '',
		]);

		$this->html->addCustomField( $formId, function() {
			$translations = $this->app->make( 'Translator' )->renderAll();
			$class = empty( $translations )
				? 'glsr-hidden'
				: '';
			return $this->html->renderTemplate( 'strings/translations', [
				'class' => $class,
				'translations' => $translations,
			]);
		});
	}
}
