<?php if ( ! defined( 'WPINC' ) ) die;
/**
 * @package   Insta_Flow
 * @author    Looks Awesome <hello@looks-awesome.com>
 
 * @link      http://looks-awesome.com
 * @copyright 2014-2017 Looks Awesome
 *
 * @wordpress-plugin
 * Plugin Name:       Grace Lite: Instagram Feed Gallery
 * Plugin URI:        https://social-streams.com
 * Description:       Create graceful galleries of Instagram posts on your WordPress site. Masonry and carousel layouts. Lite version
 * Version:           1.0.8
 * Author:            Looks Awesome
 * Author URI:        looks-awesome.com
 * Text Domain:       insta-flow
 * Domain Path:       /languages
 */
require_once( plugin_dir_path( __FILE__ ) . 'INSTClassLoader.php' );
INSTClassLoader::get(plugin_dir_path( __FILE__ ), plugins_url() . '/' . 'insta-flow' . '/')->register();

$facade = la\core\LAActivatorFacade::get();
$facade->registry_activator(new ffinst\FFIActivator(__FILE__));