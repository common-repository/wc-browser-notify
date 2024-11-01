<?php
namespace WCBN;

/**
 * Class NotificationFrontend
 *
 * @since 1.0.0
 */
class NotificationFrontend {

    /**
     * post type slug
     * @var string
     */
    private static $post_type = 'wcbn-notify';

    /**
     * post meta field
     * @var arr
     */
    private static $meta_field = [];

    /**
     * notify trigger obj
     * @var arr
    */
    private static $notify_triggers_obj = [];


    /**
     * Initiliaze
     */
    public static function init() {
        self::$meta_field["trigger"] = 'wcbn-trigger-notify';
        self::$meta_field["popup"] = 'wcbn-notify-popup';
        self::$meta_field["delay"] = 'wcbn-trigger-delay';
        self::$meta_field["override"] = 'wcbn-trigger-open-override';

        $override = self::set_notify_triggers();
        // Make Open Trigger First If not to be Overriden
        if(!$override) {
            if( is_array( self::$notify_triggers_obj ) ){

                natcasesort(self::$notify_triggers_obj);
            }
        }

        add_action( 'wp_enqueue_scripts', array(__CLASS__ ,'front_enqueue_scripts' ));
        add_action( 'wp_footer', array(__CLASS__ ,'add_script_on_footer' ));
        
        add_action( 'woocommerce_add_to_cart', array(__CLASS__ ,'woocommerce_add_to_cart' ));
        add_action( 'woocommerce_checkout_init', array(__CLASS__ ,'woocommerce_checkout_init' ));
        add_action( 'woocommerce_cart_is_empty', array(__CLASS__ ,'woocommerce_cart_is_empty' ));
        add_action( 'woocommerce_after_shop_loop', array(__CLASS__ ,'woocommerce_after_shop_loop' ));
        add_action( 'woocommerce_after_single_product', array(__CLASS__ ,'woocommerce_after_single_product' )); // On Single Product
        add_action( 'woocommerce_order_items_table', 
            array(__CLASS__ ,'woocommerce_order_items_table' )); // After Order Placed

    }

    public static function set_notify_triggers() {
        //get your custom posts ids as an array
        $posts = get_posts(array(
            'post_type'   => self::$post_type,
            'post_status' => 'publish',
            'fields' => 'ids'
            )
        );
        $override_open = 0;
        foreach($posts as $p) {
            $action = get_post_meta($p, self::$meta_field["trigger"], true);
            $popup_post_id = get_post_meta($p, self::$meta_field["popup"], true);
            $trigger_delay = get_post_meta($p, self::$meta_field["delay"], true);
            $trigger_open_override = get_post_meta($p, self::$meta_field["override"], true);

            if($trigger_open_override) {
                $override_open = 1;
            }

            self::$notify_triggers_obj[$action] = [];
            self::$notify_triggers_obj[$action]["delay"] = $trigger_delay;
            self::$notify_triggers_obj[$action]["override"] = $trigger_open_override;
            self::$notify_triggers_obj[$action]['popup_title'] = get_post_field("post_title", $popup_post_id);
            self::$notify_triggers_obj[$action]['popup_text'] = get_post_field("post_content", $popup_post_id);
        }

        return $override_open;
    }

    public static function front_enqueue_scripts() {
        $deps = array(
            'jquery',
            'jquery-ui-core',
            'editor'
        );

        // Register the script
        wp_register_script('wcbn-js', ASSETS_URL . 'js/wcbn.js', $deps, Main::get_version(), true);


        wp_enqueue_style('wcbn-css-remodal', ASSETS_URL . 'css/remodal.css', array(), false);
        wp_enqueue_style('wcbn-css-remodal-default', ASSETS_URL . 'css/remodal-default-theme.css', array(), false);

        //echo ASSETS_URL . 'js/remodal.min.js'; die;
        wp_register_script('remodal-js', ASSETS_URL . 'js/remodal.min.js', array(), false, 1);
        wp_enqueue_script('remodal-js');
    }

    /**
     * After All Data is being set in the notify obj
     * @return void
    */
    public static function add_script_on_footer() {
        // Add Var to Js
        wp_localize_script( 'wcbn-js', 'notify_obj', self::$notify_triggers_obj );
        
        // Enqueued script with localized data.
        wp_enqueue_script('wcbn-js');
    }


    /**
     * Associates Trigger Var on the given WC Hook
     * @param functio to trigger
     * @return void
     */
    public static function trigger_action_on_hook($func) {
        if(isset(self::$notify_triggers_obj[$func])) {
            self::$notify_triggers_obj[$func]["trigger"] = 1;
        }
    }

    /**
     * Hooked into woocommerce_add_to_cart
     * @param void
     * @return void
    */
    public static function woocommerce_add_to_cart() {
        self::trigger_action_on_hook(__FUNCTION__);
    }

    /**
     * Hooked into woocommerce_checkout_init
     * @param void
     * @return void
    */
    public static function woocommerce_checkout_init() {
        self::trigger_action_on_hook(__FUNCTION__);
    }

    /**
     * Hooked into woocommerce_order_items_table
     * @param void
     * @return void
    */
    public static function woocommerce_order_items_table() {
       self::trigger_action_on_hook(__FUNCTION__);
    }


        
    /**
     * Hooked into woocommerce_cart_is_empty
     * @param void
     * @return void
    */
    public static function woocommerce_cart_is_empty() {
        self::trigger_action_on_hook(__FUNCTION__);
    }

    /**
     * Hooked into woocommerce_after_shop_loop
     * @param void
     * @return void
    */
    public static function woocommerce_after_shop_loop() {
        self::trigger_action_on_hook(__FUNCTION__);
    }

    /**
     * Hooked into woocommerce_after_single_product
     * @param void
     * @return void
    */
    public static function woocommerce_after_single_product() {
        self::trigger_action_on_hook(__FUNCTION__);
    }
}
