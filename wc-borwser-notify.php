<?php
/**
 * Plugin Name: WC Browser Notify
 * Plugin URI: https://wooninjas.com
 * Description: Enables you to create browser alerts on browser actions OR WC actions(events)
 * Version: 1.0.1
 * Author: WooNinjas
 * Author URI: https://wooninjas.com
 * Text Domain: WCBN
 * License: GPLv2 or later
 */


namespace WCBN;
if (!defined("ABSPATH")) exit;

// Directory
define('WCBN\DIR', plugin_dir_path(__FILE__));
define('WCBN\DIR_FILE', DIR . basename(__FILE__));
define('WCBN\INCLUDES_DIR', trailingslashit(DIR . 'includes'));
define('WCBN\TEMPLATES', trailingslashit(DIR . 'templates'));

// URLS
define('WCBN\URL', trailingslashit(plugins_url('', __FILE__)));
define('WCBN\ASSETS_URL', trailingslashit(URL . 'assets'));

// Load WC dependency class
if (!class_exists('WC_Dependencies')) {
    require_once DIR . 'woo-includes/class-wc-dependencies.php';
}

// // Check if WooCommerce active
// if (!\WC_Dependencies::woocommerce_active_check()) {
//     return;
// }

//Loading files
require_once INCLUDES_DIR . 'class-notification-backend.php';
require_once INCLUDES_DIR . 'class-notification-frontend.php';
require_once INCLUDES_DIR . 'functions.php';

/**
 * Class Main for plugin initiation
 *
 * @since 1.0
 */
final class Main
{
    private static $version = '1.0.1';

    // Main instance
    protected static $_instance = null;

    protected function __construct() {
        register_activation_hook(__FILE__, array($this, 'activation'));
        register_deactivation_hook(__FILE__, array($this, 'deactivation'));
        add_action('plugins_loaded', array($this, 'upgrade'));
        
        if(!is_admin()) {
            NotificationFrontend::init();
        } else {
            NotificationBackend::init();
        }
        
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

    }

    /**
     * @return $version
     */
    public static function get_version() {
        return self::$version;
    }

    /**
     * @return $this
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Activation function hook
     *
     * @return void
     */
    public static function activation() {
        if (!current_user_can('activate_plugins'))
            return;

        update_option('wcbn_version', self::get_version());
    }

    /**
     * Deactivation function hook
     * No used in this plugin
     *
     * @return void
     */
    public static function deactivation() {}

    public static function upgrade() {
        if (get_option('wcbn_version') != self::get_version()) {
            wcbn_upgrade();
        }
    }

    /**
     * Enqueue scripts on admin
     */
    public static function admin_enqueue_scripts() {
        global $post_type;
        $screens = array('wcbn-notify');

        wp_enqueue_style('wcbn-css', ASSETS_URL . 'css/wcbn.css', array(), self::get_version());

        wp_register_script('wcbn-admin-js', ASSETS_URL.'js/wcbn-admin.js', array("jquery"), self::$version, 0);
        wp_enqueue_script('wcbn-admin-js');
    }
}


function WCBN() {

    include_once ABSPATH . 'wp-admin/includes/plugin.php';
    
    if( !is_plugin_active( 'woocommerce/woocommerce.php' ) )
    {
        if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
            include_once( ABSPATH . '/wp-admin/includes/plugin.php' );
        }
        error_log( 'if: ' . var_export( '$if', true ) );
        deactivate_plugins ( plugin_basename ( __FILE__ ), true );
        
        add_action( "admin_notices", function(){
            unset($_GET['activate']);  //unset this to hide default Plugin activated. notice
            
                $class = 'notice notice-error is-dismissible';
                $message = sprintf( __( 'wc-browser-notify requires <a href="https://woocommerce.com/">woocommerce</a> plugin to be activated.' ), 'wc-browser-notify' );
                printf ( "<div id='message' class='%s'> <p>%s</p></div>", $class, $message );
        } );
        return;
    }else{
        // error_log( 'Request: ' . var_export( 'sad', true ) );
        /**
         * Main instance
         *
         * @return Main
         */
        return Main::instance();
    }
}

// Bootstrap
WCBN();
