<?php namespace ffinst;

use ffinst\tabs\FFISourcesTab;
use ffinst\tabs\FFIStreamsTab;
use ffinst\flow\LAAdminBase;
use ffinst\db\FFIDBMigrationManager;
use la\core\tabs\LAGeneralTab;
use la\core\tabs\LAAuthTab;

class FFIAdmin extends LAAdminBase{
    private $plugins = array(
        'flow-flow' => array(
            'flow-flow/flow-flow.php',
            'flow-flow',
        ),
        'insta-flow' => array(
            'insta-flow/insta-flow.php',
            'insta-flow-admin',
        )
    );

	public function __construct($context) {
		parent::__construct($context);

        // Add the options page and menu item.
        add_action( 'admin_menu', array( $this, 'addPluginAdminNewsPage' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_news_scripts' ) );
	}
	
	protected function initPluginAdminPage(){
		$mm = new FFIDBMigrationManager($this->context);
		$mm->migrate();
		unset($mm);
	}

    /**
     * States:
     * 0 - not installer
     * 1 - installed
     * 2 - activated
     */
	protected function getPluginsState(){
	    $result = array();

	    foreach ($this->plugins as $k => $v){
	        $state = 0;

            if(file_exists(WP_PLUGIN_DIR . '/' . $v[0])){
                $state = 1;
            }

            if(is_plugin_active($v[0])){
                $state = 2;
            }

            $result[$k] = array(
                'state' => $state,
                'plugin_page_slug' => $v[1]
            );
        }

        return $result;
    }
	
	protected function enqueueAdminStylesAlways($plugin_directory){
		wp_enqueue_style($this->getPluginSlug() .'-admin-icon-styles', $plugin_directory . 'css/admin-icon.css', array(), $this->context['version'] );
	}
	
	protected function enqueueAdminStylesOnlyAtPluginPage($plugin_directory){
        wp_enqueue_style($this->getPluginSlug() .'-news-styles', $plugin_directory . 'css/news.css', array(), $this->context['version']);
        wp_enqueue_script( $this->getPluginSlug() . '-news', $plugin_directory . 'js/news.js', array( 'jquery' , 'underscore' ), $this->context['version']);

        wp_localize_script($this->getPluginSlug() . '-news', 'FFIADMIN', array(
			'assets_url' => $this->context['plugin_url'] . $this->context['slug'],
            'plugins' => $this->getPluginsState(),
            'requirements' => array(
                'php_status' => version_compare(phpversion(), '5.3', '>='),
                'php' => preg_replace("(-.+)", '', phpversion()),
                'wp_status' => (float) get_bloginfo('version') > 4,
                'wp' => get_bloginfo('version'),
                'memory_status' => preg_replace('/[^0-9]/', '', ini_get('memory_limit')) >= 32,
                'memory' => ini_get('memory_limit'),
                'upload_status' => preg_replace('/[^0-9]/', '', ini_get('upload_max_filesize')) >= 64,
                'upload' => ini_get('upload_max_filesize')
            )
        ));
	}
	
	protected function enqueueAdminScriptsAlways($plugin_directory){
		wp_enqueue_script( $this->getPluginSlug() . '-global-admin-script', $plugin_directory . 'js/global_admin.js', array( 'jquery' , 'backbone', 'underscore' ), $this->context['version']);
	}
	
	protected function enqueueAdminScriptsOnlyAtPluginPage($plugin_directory){
		parent::enqueueAdminScriptsOnlyAtPluginPage($plugin_directory);
	}

	protected function addPluginAdminMenu($displayAdminPageFunction) {
		$plugin_directory = $this->context['plugin_url'] . $this->getPluginSlug() . '/';
		
		$wp_version = (float)get_bloginfo('version');
		if ($wp_version > 3.8) { // From 3.8 WP supports SVG icons
			$icon = $plugin_directory . 'assets/social-streams-icon.svg';
		} else {
			$icon = 'dashicons-networking';
		}
		
		return add_menu_page(
            'Social Apps',
            'Social Apps',
            'manage_options',
            $this->getPluginSlug(),
            $displayAdminPageFunction,
            $icon
        );
	}

    public function enqueue_news_scripts()
    {
        $plugin_directory = plugins_url() . '/' . $this->getPluginSlug() . '/';
        $screen = get_current_screen();
        if ( 'social-apps_page_insta-flow-admin' == $screen->id ) {
            wp_enqueue_style($this->getPluginSlug() .'-admin-styles', $plugin_directory . 'css/admin.css', array(), $this->context['version']);
            wp_enqueue_style($this->getPluginSlug() .'-colorpickersliders', $plugin_directory . 'css/jquery-colorpickersliders.css', array(), $this->context['version']);

            // Load web font
            wp_register_style('ff-fonts', '//fonts.googleapis.com/css?family=Montserrat:400,700|PT+Serif|Lato:300,400' );
            wp_enqueue_style( 'ff-fonts' );

            // Enqueue scripts
            wp_enqueue_script( $this->getPluginSlug() . '-streams-script', $plugin_directory . 'js/streams.js', array( 'jquery' ), $this->context['version']);
            wp_enqueue_script( $this->getPluginSlug() . '-admin-script', $plugin_directory . 'js/admin.js', array( 'jquery', 'backbone', 'underscore' ), $this->context['version']);
            wp_localize_script($this->getPluginSlug() . '-admin-script', 'WP_FF_admin', array());
            wp_localize_script($this->getPluginSlug() . '-admin-script', 'isWordpress', '1');
            wp_localize_script($this->getPluginSlug() . '-admin-script', '_ajaxurl', (string)$this->context['ajax_url']);
            wp_enqueue_script( $this->getPluginSlug() . '-tinycolor', $plugin_directory . 'js/tinycolor.js', array( 'jquery' ), $this->context['version']);
            wp_enqueue_script( $this->getPluginSlug() . '-colorpickersliders', $plugin_directory . 'js/jquery.colorpickersliders.js', array( 'jquery' ), $this->context['version']);
        }
	}

	public function addPluginAdminNewsPage () {
        add_submenu_page(
            $this->getPluginSlug(),
            'Grace Instagram',
            'Grace Instagram',
            'manage_options',
            $this->getPluginSlug() . '-admin',
            array($this, 'display_plugin_admin_subpage')
        );
    }
	
	protected function displayPluginAdminPage($context) {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.', $this->getPluginSlug()));
		}
		
		$context = $this->context;
		$activated = $this->db->registrationCheck();
		
		$this->db->dataInit();
		
		$context['activated'] = $activated;
		
		include_once($context['root']  . 'views/news.php');
	}
	
	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public final function display_plugin_admin_subpage() {
		$activated = $this->db->registrationCheck();
		
		$context = $this->context;
		$context['admin_page_title'] = esc_html( get_admin_page_title() );
		$context['options'] = FFI::get_instance($context)->get_options();
		$context['auth_options'] = FFI::get_instance($context)->get_auth_options();
		$this->db->dataInit();
		$context['streams'] = $this->db->streamsWithStatus();
		$context['sources'] = $this->db->sources();
		$context['activated'] = $activated;
		
		$tab_prefix = 'ffi';
		$context['form-action'] = '';
		$context['tabs'][] = new FFIStreamsTab();
		$context['tabs'][] = new FFISourcesTab();
		$context['tabs'][] = new LAGeneralTab($tab_prefix);
		$context['tabs'][] = new LAAuthTab($tab_prefix);

		$context['buttons-after-tabs'] = '<li id="request-tab"><span>Save changes</span> <i class="flaticon-paperplane"></i></li>';
		
		include_once($context['root']  . 'views/admin.php');
	}
	
	/**
	 * Gets content from a URL via curl 
	 */
	private function get_url($url) {
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5);
		$content = curl_exec($ch);
		curl_close($ch);
		return $content;
	}

    public function processDeactivateRequest()
    {
        $url = $this->context['deactivate_url'];
        $data = array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'body' => $_POST
        );
        $response = wp_remote_post($url, $data);

        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            echo "Something went wrong: $error_message";
        } else {
            echo $response['body'];
        }
	}

    public function processDeactivateTicketRequest()
    {
        $to = 'support.43493.3a9e04eb9f5d9232@helpscout.net';
        $email = $_POST['email'];
        $subject = 'Grace Technical Issue (Deactivation)';
        $message = $_POST['message'];
        $version = $_POST['version'];
        $content = "Plugin verison: $version;\nUser Email: $email\n\n$message";
        $headers = array();
        $headers[] = 'From: ' . $email . ' <' . str_replace(array("\r", "\n", "\n", "\t", ",", ";"), '', $email. ">\r\n");
        $headers[] = 'Content-type: text/html';
        $headers[] = 'Reply-To: ' . $email;
        $response = wp_mail($to, $subject, $content, $headers);

        if ( !$response ) {
            echo "Something went wrong";
        } else {
            echo $response;
        }
    }
}