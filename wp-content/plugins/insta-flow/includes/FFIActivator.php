<?php namespace ffinst;

use la\core\LAActivatorBase;
use ffinst\db\FFIDBMigrationManager;
use ffinst\db\FFIDBManager;

class FFIActivator extends LAActivatorBase{
    public function __construct($file)
    {
        parent::__construct($file);
        add_action( 'admin_footer', array( $this, 'add_deactivation_feedback_dialog_box' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'add_deactivation_scripts' ) );
    }

	protected function checkPlugin(){
		$mm = new FFIDBMigrationManager($this->context);
		$mm->migrate();
		unset($mm);
	}

    public function getContext()
    {
		return $this->context;
	}
	
	protected function initContext($file){
		/** @var wpdb $wpdb */
		$wpdb = $GLOBALS['wpdb'];
		
		$context = array(
				'root'              => plugin_dir_path($file),
				'slug'              => 'insta-flow',
				'slug_down'         => 'insta_flow',
				'plugin_url'        => plugin_dir_url(dirname($file).'/'),
				'admin_url'         => admin_url('admin-ajax.php'),
				'table_name_prefix' => $wpdb->base_prefix . 'ffi_',
				'version'			=> '1.0.8',
				'faq_url' 			=> 'http://docs.social-streams.com/',
				'json_url' 			=> 'http://flow.looks-awesome.com/service/news/news.json',
				'json_file' 		=> 'news.json',
				'count_posts_4init'	=> 20,
            	'deactivate_url' 	=> 'https://social-streams.com/services/rest/grace/deactivate.php'
		);
		$context['db_manager'] = new FFIDBManager($context);
		return $context;
	}
	
	protected function checkEnvironment(){
		if(version_compare(PHP_VERSION, '5.3.0') == -1){
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( '<b>Grace</b> plugin requires PHP version 5.3.0 or higher. Pls update your PHP version or ask hosting support to do this for you, you are using old and unsecure one' );
		}
		
		if(!function_exists('curl_version')){
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( '<b>Grace</b> plugin requires curl extension for php. Please install/enable this extension or ask your hosting to help you with this.' );
		}
		
		if(!function_exists('mysqli_connect')){
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( '<b>Grace</b> plugin requires mysqli extension for MySQL. Please install/enable this extension on your server or ask your hosting to help you with this. <a href="http://php.net/manual/en/mysqli.installation.php">Installation guide</a>' );
		}
	}
	
	protected function singleSiteActivate(){
		$this->checkPlugin();
	}
	
	protected function singleSiteDeactivate(){
		wp_clear_scheduled_hook( 'ffi_load_cache' );
		wp_clear_scheduled_hook( 'ffi_load_cache_4disabled' );
	}
	
	protected function beforePluginLoad(){
		$this->setContextValue('ajax_url', admin_url('admin-ajax.php'));
		
		parent::beforePluginLoad();
	}
	
	protected function registrationCronActions(){
		$this->addCronInterval('minute', array('interval' => MINUTE_IN_SECONDS, 'display' => 'Once Minute'));
		$this->addCronInterval('sex_hours', array('interval' => MINUTE_IN_SECONDS * 60 * 6, 'display' => 'Sex hours'));
		add_filter('cron_schedules', array($this, 'getCronIntervals'));
		
		$time = time();
		$ffi = FFI::get_instance($this->context);
		
		add_action('ffi_load_cache', array($ffi, 'refreshCache'));
		if(false == wp_next_scheduled( 'ffi_load_cache' )){
			wp_schedule_event( $time, 'minute', 'ffi_load_cache' );
		}
		
		add_action('ffi_load_cache_4disabled', array($ffi, 'refreshCache4Disabled'));
		if(false == wp_next_scheduled( 'ffi_load_cache_4disabled' )){
			wp_schedule_event( $time, 'sex_hours', 'ffi_load_cache_4disabled' );
		}
	}
	
	protected function registrationAjaxActions(){
		$ffi = FFI::get_instance($this->context);
		$ffadmin = new FFIAdmin($this->context);
		$dbm = $this->context['db_manager'];
		$slug_down= $this->context['slug_down'];
		
		add_action('wp_ajax_' . $slug_down . '_fetch_posts', array( $ffi, 'processAjaxRequest'));
		add_action('wp_ajax_nopriv_' . $slug_down . '_fetch_posts', array( $ffi, 'processAjaxRequest'));
		add_action('wp_ajax_' . $slug_down . '_moderation_apply_action', array( $ffi, 'moderation_apply'));
		add_action('wp_ajax_' . $slug_down . '_load_cache', array( $ffi, 'processAjaxRequestBackground'));
		add_action('wp_ajax_nopriv_' . $slug_down . '_load_cache', array( $ffi, 'processAjaxRequestBackground'));
		
		add_action('wp_ajax_' . $slug_down . '_social_auth',			array( $dbm, 'social_auth' ));
		add_action('wp_ajax_' . $slug_down . '_save_sources_settings',	array( $dbm, 'save_sources_settings' ));
		add_action('wp_ajax_' . $slug_down . '_get_stream_settings',	array( $dbm, 'get_stream_settings' ));
		add_action('wp_ajax_' . $slug_down . '_ff_save_settings',		array( $dbm, 'ff_save_settings_fn' ));
		add_action('wp_ajax_' . $slug_down . '_save_stream_settings',	array( $dbm, 'save_stream_settings' ));
		add_action('wp_ajax_' . $slug_down . '_create_stream',			array( $dbm, 'create_stream' ));
		add_action('wp_ajax_' . $slug_down . '_clone_stream',			array( $dbm, 'clone_stream' ));
		add_action('wp_ajax_' . $slug_down . '_delete_stream', 			array( $dbm, 'delete_stream' ));
		
		add_action('wp_ajax_' . $slug_down . '_get_admin_page', array( $ffadmin, 'get_plugin_admin_page' ));
        add_action('wp_ajax_' . $slug_down . '_deactivate', array( $ffadmin, 'processDeactivateRequest'));
        add_action('wp_ajax_' . $slug_down . '_deactivate_ticket', array( $ffadmin, 'processDeactivateTicketRequest'));
	}
	
	protected function renderAdminSide(){
		new FFIAdmin($this->context);
	}
	
	protected function renderPublicSide(){
		$ffi = FFI::get_instance($this->context);
		add_action( 'init', array($ffi,		'register_shortcodes'));
		add_action( 'init', array($ffi,		'load_plugin_textdomain'));
		add_action( 'wp_enqueue_scripts',	array( $ffi, 'enqueue_scripts' ));
		add_action( 'wpmu_new_blog',		array( $ffi, 'activate_new_site' ));
	}
	
	protected function afterPluginLoad(){
		$ffi = FFI::get_instance($this->context);
		add_action( 'widgets_init', array($ffi, 'initWPWidget'));
		add_action( 'vc_before_init', array($ffi, 'initVCIntegration'));
	}

    public function add_deactivation_feedback_dialog_box(){
        $deactivate_reasons = array(
            1 => array(
                'id'    => 'plugin_is_hard_to_use_technical_problems',
                'text'  => __( 'Technical problems / hard to use', $this->context['slug'] ),
            ),
            2 => array(
                'id'    => 'free_version_limited',
                'text'  => __( 'Free version is limited', $this->context['slug'] ),
            ),
            3 => array(
                'id'    => 'premium_expensive',
                'text'  => __( 'Premium is expensive', $this->context['slug'] ),
            ),
            4 => array(
                'id'    => 'upgrading_to_paid_version',
                'text'  => __( 'Upgrading to paid version', $this->context['slug'] ),
            ),
            5 => array(
                'id'    => 'temporary_deactivation',
                'text'  => __( 'Not what I need', $this->context['slug'] ),
            ),
        );

        ?>
        <?php

        $deactivate_url =
            add_query_arg(
                array(
                    'action' => 'deactivate',
                    'plugin' => plugin_basename( $this->context['slug'] ) . '%2Finsta-flow.php',
                    '_wpnonce' => wp_create_nonce( 'deactivate-plugin_' . plugin_basename( $this->context['slug'] ) . '/insta-flow.php' )
                ),
                admin_url( 'plugins.php' )
            );

        require ( 'display_deactivation_popup.php' );
    }

    public function add_deactivation_scripts()
    {
        wp_enqueue_style( 'ff-deactivate-popup', $this->context['plugin_url'] . $this->context['slug'] . '/css/deactivate_popup.css', array(), $this->context['version'] );
        wp_enqueue_script( 'ff-deactivate-popup', $this->context['plugin_url'] . $this->context['slug'] . '/js/deactivate_popup.js', array(), $this->context['version']);

        $admin_data = wp_get_current_user();
        wp_localize_script(  'ff-deactivate-popup', 'FFI_Deactivate' , array(
            "prefix" => 'foo',
            "deactivate_class" => '_deactivate_link',
            "email" => $admin_data->data->user_email,
            "plugin" => $this->context['slug'],
            "slug_down" => $this->context['slug_down'],
            "parent_plugin" => 'flow-flow',
            "premium_url" => 'https://goo.gl/eLAjuZ',
            "plugin_url" => $this->context['plugin_url'],
            "version" => $this->context['version'],
            "admin_email" => get_option('admin_email'),
            "admin_url" => $this->context['admin_url']
        ));
    }
}