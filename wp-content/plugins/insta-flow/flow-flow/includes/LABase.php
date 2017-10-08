<?php namespace ffinst\flow;
use ffinst\flow\cache\FFCache;
use ffinst\flow\cache\FFCacheAdapter;
use ffinst\flow\db\FFDB;
use ffinst\flow\db\FFDBManager;
use ffinst\flow\settings\FFGeneralSettings;
use ffinst\flow\social\FFFeedUtils;
use ffinst\flow\social\FFInstagram;
use ffinst\flow\settings\FFSettingsUtils;
use ffinst\flow\settings\FFStreamSettings;
use ffinst\flow\cache\FFImageSizeCacheManager;

if ( ! defined('FF_BY_DATE_ORDER'))   define('FF_BY_DATE_ORDER', 'compareByTime');
if ( ! defined('FF_RANDOM_ORDER'))    define('FF_RANDOM_ORDER',  'randomCompare');
if ( ! defined('FF_SMART_ORDER'))     define('FF_SMART_ORDER',   'smartCompare');

abstract class LABase {
	protected static $instance = array();
	
	/**
	 * @param $context
	 *
	 * @return FlowFlow|null
	 */
	public static function get_instance($context = null) {
		$slug = is_null($context) ? 'flow-flow' : $context['slug'];
		if (!array_key_exists($slug, self::$instance)) {
			$slug_down = is_null($context) ? 'flow_flow' : $context['slug_down'];
			$class = get_called_class();
			self::$instance[$slug] = new $class($context, $slug, $slug_down);
		}
		return self::$instance[$slug];
	}
	
	public static function get_instance_by_slug($slug) {
		return (array_key_exists($slug, self::$instance)) ? self::$instance[$slug] : null;
	}
	
// 	public static function registry($slug, $instance){
// 		if (!array_key_exists($slug, self::$instance)) {
// 			self::$instance[$slug] = $instance;
// 		}
// 	}

	
	/**
	 * @deprecated
	 * @var FFDBManager
	 */
	public $db;
	
	/**
	 * @deprecated 
	 * @var FFCache 
	 */
	protected $cache;
	/**
	 * @deprecated 
	 * @var FFStreamSettings 
	 */
	protected $settings;
	/**
	 * @deprecated 
	 * @var FFGeneralSettings 
	 */
	protected $generalSettings;
	
	/** @var array */
	protected $context;
	protected $slug;
	protected $slug_down;
	
	protected function __construct($context, $slug, $slug_down) {
		$this->db = $context['db_manager'];
		$this->context = $context;
		$this->slug = $slug;
		$this->slug_down = $slug_down;
		
		/**
		 * Default filter for result before send response.
		 * Use wp filter engine because need to customize result from addons. 
		 */
		add_filter('ff_build_public_response', array($this, 'buildResponse'), 1, 8);
	}
	
	public final function register_shortcodes()
	{
		add_shortcode($this->getShortcodePrefix(), array($this, 'renderShortCode'));
	}
	
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public final function load_plugin_textdomain() {
		$domain = $this->slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
		
		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );
	}
	
	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public final function activate_new_site( $blog_id ) {
		if ( 1 !== did_action( 'wpmu_new_blog' ) )  return;
		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();
	}
	
	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public final function enqueue_styles() {
		$this->enqueueStyles();
	}
	
	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public final function enqueue_scripts() {
		$this->enqueueScripts();
	}
	
	public final function processAjaxRequest() {
		if (isset($_REQUEST['stream-id']) && $this->prepareProcess()) {
			$this->db->dataInit(true);
			$stream = $this->db->getStream($_REQUEST['stream-id']);
			if (isset($stream)) {
				$disableCache = isset($_REQUEST['disable-cache']) ? (bool)$_REQUEST['disable-cache'] : false;
				echo $this->process(array($stream), $disableCache);
			}
		}
		die();
	}
	
	public final function moderation_apply( ){
		if (isset($_REQUEST['stream']) && $this->prepareProcess()) {
			$this->db->dataInit();
			$stream = $this->db->getStream($_REQUEST['stream']);
			if (isset($stream)) {
				$this->cache->setStream(new FFStreamSettings($stream), true);
				$this->cache->moderate();
			}
		}
	}
	
	public final function processAjaxRequestBackground() {
		if ($this->prepareProcess(true)) {
			$this->db->dataInit(true);
			
			if (isset($_REQUEST['feed_id'])){
				$sources = $this->db->sources();
				if (isset($sources[$_REQUEST['feed_id']])){
					$this->process4feeds(array($sources[$_REQUEST['feed_id']]), false, true);
				}
			}
			if (isset($_REQUEST['stream_id'])){
				$stream = $this->db->getStream($_REQUEST['stream_id']);
				if (isset($stream))
				{
					$this->process4feeds(array($stream), false, true);
				}
			}
		}
	}
	
	
	public final function processRequest(){
		if (isset($_REQUEST['stream-id']) && $this->prepareProcess()) {
			$this->db->dataInit(true);
			$stream = $this->db->getStream($_REQUEST['stream-id']);
			if (isset($stream)) {
				return $this->process(array($stream), isset($_REQUEST['disable-cache']));
			}
		}
		return '';
	}
	
	public final function refreshCache($streamId = null, $force = false, $withDisabled = false) {
		if ($this->prepareProcess(true)) {
			$enabled = $withDisabled ? FFDB::conn()->parse('`cach`.system_enabled = 0') : FFDB::conn()->parse('`cach`.enabled = 1');
			if (empty($streamId))
				$sql = FFDB::conn()->parse('SELECT `cach`.`feed_id` FROM ?n `cach` WHERE ?p AND (`cach`.last_update + `cach`.cache_lifetime * 60) < UNIX_TIMESTAMP() ORDER BY `cach`.last_update', $this->db->cache_table_name, $enabled);
				else
					$sql = FFDB::conn()->parse('SELECT `cach`.`feed_id` FROM ?n `cach` INNER JOIN ?n `ss` ON `ss`.feed_id = `cach`.feed_id WHERE ?p AND `ss`.stream_id = ?s AND (`cach`.last_update + `cach`.cache_lifetime * 60) < UNIX_TIMESTAMP() ORDER BY `cach`.last_update',
							$this->db->cache_table_name, $this->db->streams_sources_table_name, $enabled, $streamId);
					try {
						if (false !== ($feeds = FFDB::conn()->getCol($sql))){
							$useIpv4 = $this->getGeneralSettings()->useIPv4();
							$use = $this->getGeneralSettings()->useCurlFollowLocation();
							for ( $i = 0; $i < 8; $i ++ ) {
								if (isset($feeds[$i])){
									$feed_id = $feeds[$i];
// 									{
// 										$_REQUEST['feed_id'] = $feed_id;
// 										$this->processAjaxRequestBackground();
// 									}
									{
										FFFeedUtils::getFeedData($this->getLoadCacheUrl($feed_id, $force), 1, false, false, $use, $useIpv4);
									}
								}
							}
						}
					}
					catch(\Exception $e){
						error_log($e->getMessage());
						error_log($e->getTraceAsString());
					}
		}
	}
	
	public final function refreshCache4Disabled() {
		$this->refreshCache(null, false, true);
	}
	
	public function renderShortCode($attr, $text = null) {
		if (isset($attr['id'])){
			if ($this->prepareProcess()) {
				$this->db->dataInit(true);
				$stream = (object)$this->db->getStream($attr['id']);
				if (isset($stream)) {
					$stream->preview = (isset($attr['preview']) && $attr['preview']);
					$stream->gallery = $stream->preview ? 'nope' : isset($stream->gallery) ? $stream->gallery : 'nope';
					return $this->renderStream($stream, $this->getPublicContext($stream, $this->context));
				}
			} else {
				echo 'Flow-Flow message: Stream with specified ID not found or no feeds were added to stream';
			}
		}
	}
	
	/**
	 * @param $result
	 * @param $all
	 * @param $context
	 * @param $errors
	 * @param $oldHash
	 * @param $page
	 * @param $status
	 * @param FFStreamSettings $stream
	 *
	 * @return array
	 */
	public function buildResponse($result, $all, $context, $errors, $oldHash, $page, $status, $stream){
		$streamId = (int) $stream->getId();
		$countOfPages = isset($_REQUEST['countOfPages']) ? $_REQUEST['countOfPages'] : 0;
		$result = array('id' => $streamId, 'items' => $all, 'errors' => $errors,
				'hash' => $oldHash, 'page' => $page, 'countOfPages' => $countOfPages, 'status' => $status);
		return $result;
	}
	
	/**
	 * @deprecated
	 * $return FFGeneralSettings
	 */
	public function getGeneralSettings(){
		return $this->generalSettings;
	}
	
	/**
	 * @deprecated
	 */
	public function get_options() {
		$options = $this->db->getOption('options', true);
		return $options;
	}
	
	/**
	 * @deprecated
	 */
	public function get_auth_options() {
		$options = $this->db->getOption('fb_auth_options', true);
		return $options;
	}

	protected abstract function enqueueStyles();
	protected abstract function enqueueScripts();
	protected abstract function getShortcodePrefix();
	protected abstract function getPublicContext($stream, $context);
	protected abstract function getLoadCacheUrl($streamId = null, $force = false);
	protected abstract function getNameJSOptions();
	
	protected function prepareProcess($forceLoadCache = false) {
		if ($this->db->countFeeds() > 0) {
			$this->generalSettings = $this->db->getGeneralSettings();
			$this->cache = new FFCacheAdapter($this->context, $forceLoadCache);
			return true;
		}
		return false;
	}
	
	protected function renderStream($stream, $context){
		$settings = new FFStreamSettings($stream);
		if ($settings->isPossibleToShow()){
			if ( ! in_array( 'curl', get_loaded_extensions() ) ) {
				echo "<p style='background: indianred;padding: 15px;color: white;'>Flow-Flow admin info: Your server doesn't have cURL module installed. Please ask your hosting to check this.</p>";
				return;
			}
			
			if (!isset($stream->layout) || empty($stream->layout)) {
				echo "<p style='background: indianred;padding: 15px;color: white;'>Flow-Flow admin info: Please choose stream layout on options page.</p>";
				return;
			}
			
			ob_start();
			$css_version = isset($stream->last_changes) ? $stream->last_changes : '1.0';
			$url = content_url() . '/resources/' . $context['slug'] . '/css/stream-id' . $stream->id . '.css';
			echo "<link rel='stylesheet' id='ff-dynamic-css" . $stream->id . " type='text/css' href='{$url}?ver={$css_version}'/>";
			
			include($context['root']  . 'views/public.php');
			$output = ob_get_clean();
			$output = str_replace("\r\n", '', $output);
			return $output;
		}
		else
			return '';
	}
	
	protected function process($streams, $disableCache = false, $background = false) {
		foreach ($streams as $stream) {
			try {
				$moderation = false;
				foreach ( $this->db->sources() as $source ) {
					$moderation = FFSettingsUtils::YepNope2ClassicStyleSafe($source, 'mod', false);
					if ($moderation){
						break;
					}
				}
				
				$this->settings = new FFStreamSettings($stream);
				$this->cache->setStream($this->settings, $moderation);
				$this->context['image_cache'] = new FFImageSizeCacheManager($this->context);
				$instances = $this->createFeedInstances($this->settings->getAllFeeds());
				$result = $this->cache->posts($instances, $disableCache);
				unset($instances);
				unset($this->context['image_cache']);
				if ($background) return $result;
				$errors = $this->cache->errors();
				$hash = $this->cache->hash();
				return $this->prepareResult($result, $errors, $hash);
			} catch (\Exception $e) {
				error_log($e->getMessage());
				error_log($e->getTraceAsString());
			}
		}
	}
	
	private function process4feeds($feeds, $disableCache = false, $background = false) {
		try {
			//$this->settings = new FFStreamSettings($stream);
			//$this->cache->setStream($this->settings);
			$this->context['image_cache'] = new FFImageSizeCacheManager($this->context);
			$instances = $this->createFeedInstances($feeds);
			$result = $this->cache->posts($instances, $disableCache);
			unset($instances);
			unset($this->context['image_cache']);
			if ($background) return $result;
			$errors = $this->cache->errors();
			$hash = $this->cache->hash();
			return $this->prepareResult($result, $errors, $hash);
		} catch (\Exception $e) {
			error_log($e->getMessage());
			error_log($e->getTraceAsString());
		}
	}
	
	private function createFeedInstances($feeds) {
		$result = array();
		if (is_array($feeds)) {
			foreach ($feeds as $feed) {
				$feed = (object)$feed;
				$instance = new FFInstagram();
				$instance->init($this->context, $this->generalSettings, $feed);
				$result[] = $instance;
			}
		}
		return $result;
	}
	
	private function prepareResult(array $all, $errors, $hash) {
		$page = isset($_REQUEST['page']) ? (int)$_REQUEST['page'] : 0;
		$oldHash = isset($_REQUEST['hash']) ? $_REQUEST['hash'] : $hash;
		if (isset($_REQUEST['recent']) && $hash != null){
			$oldHash = $hash;
		}
		list($status, $errors) = $this->status();
		$result = apply_filters('ff_build_public_response', array(), $all, $this->context, $errors, $oldHash, $page, $status, $this->settings);
		if (($result === false) && (JSON_ERROR_UTF8 === json_last_error())){
			foreach ( $all as $item ) {
				json_encode($item);
				if (JSON_ERROR_UTF8 === json_last_error()){
					$item->text = mb_convert_encoding($item->text, "UTF-8", "auto");
				}
			}
			$result = apply_filters('ff_build_public_response', $result, $all, $this->context, $errors, $oldHash, $page, $status, $this->settings);
		}


		
		$result['server_time'] = time();
		$json = json_encode($result);
		if ($json === false){
			$errors = array();
			switch (json_last_error()) {
				case JSON_ERROR_NONE:
					echo ' - No errors';
					break;
				case JSON_ERROR_DEPTH:
					$errors[] = 'Json encoding error: Maximum stack depth exceeded';
					break;
				case JSON_ERROR_STATE_MISMATCH:
					$errors[] = 'Json encoding error: Underflow or the modes mismatch';
					break;
				case JSON_ERROR_CTRL_CHAR:
					$errors[] = 'Json encoding error: Unexpected control character found';
					break;
				case JSON_ERROR_SYNTAX:
					$errors[] = 'Json encoding error: Syntax error, malformed JSON';
					break;
				case JSON_ERROR_UTF8:
					for ( $i = 0; sizeof( $result['items'] ) > $i; $i++ ) {
						if (function_exists('mb_convert_encoding'))
							$result['items'][$i]->text = mb_convert_encoding($result['items'][$i]->text, "UTF-8", "auto");
					}
					$json = json_encode($result);
					if ($json === false){
						$errors[] = 'Json encoding error:  Malformed UTF-8 characters, possibly incorrectly encoded';
					}
					else {
						return $json;
					}
					break;
				default:
					$errors[] = 'Json encoding error';
					break;
			}
			$result = apply_filters('ff_build_public_response', array(), array(), $this->context, $errors, $oldHash, $page, 'errors', $this->settings);
			$json = json_encode($result);
		}
		return $json;
	}
	
	private function status() {
		$status_info = FFDB::getStatusInfo($this->db->cache_table_name, $this->db->streams_sources_table_name, (int)$this->settings->getId(), false);
		if ($status_info['status'] == '0'){
			return array('errors', isset($status_info['error']) ? $status_info['error'] : '');
		}
		if ($status_info['status'] == '1'){
			$feed_count = sizeof($this->settings->getAllFeeds());
			$status = ($feed_count == (int)$status_info['feeds_count']) ? 'get' : 'building';
			return array($status, array());
		}
		throw new \Exception('Was received the unknown status');
	}
}