<?php
namespace WCBN;

/**
 * Class NotificationBackend
 *
 * @since 1.0.0
 */
class NotificationBackend {

    private static $browser_actions = [];
    private static $wc_actions = []; 

    /**
     * post type slug
     * @var string
     */
    private static $post_type = 'wcbn-notify';


    /**
     * post meta field
     * @var arr
     */
    private static $field = [];

    /**
     * check if on a new page in CPT
     * @var boolen
     */
    private static $new_page = 0;


    /**
     * Initiliaze
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'register_post_type'));
        add_action('save_post_' . self::$post_type, array(__CLASS__, 'save_post'), 10, 3);
        add_action('edit_form_after_title',  array(__CLASS__, 'move_metabox_after_title'), 10, 1);

        add_filter('manage_wcbn-notify_posts_columns', array(__CLASS__, 'notify_column_head'), 10 , 1);
        add_action('manage_wcbn-notify_posts_custom_column', array(__CLASS__, 'notify_column_content'), 5, 2);

        if(!isset($_GET["post"])) {
            self::$new_page = 1;
        } else {
            self::$new_page = 0;
        }

        self::$field["trigger"] = 'wcbn-trigger-notify';
        self::$field["popup"] = 'wcbn-notify-popup';
        self::$field["delay"] = 'wcbn-trigger-delay';
        self::$field["override"] = 'wcbn-trigger-open-override';


        // Browser Based Actions
        self::$browser_actions["open"] = __('On page load', 'wcbn');
        //self::$browser_actions["close"] = __('On closing a browser tab', 'wcbn');
        self::$browser_actions["scroll"] = __('On scrolling through', 'wcbn');

        // WC Actions
        self::$wc_actions["woocommerce_add_to_cart"] = __('On Add to Cart(Non-AJAX)', 'wcbn');
        self::$wc_actions["woocommerce_checkout_init"] = __('On Proceed to Checkout', 'wcbn');
        self::$wc_actions["woocommerce_order_items_table"] = __('On Place Order', 'wcbn');
        self::$wc_actions["woocommerce_cart_is_empty"] = __('On Cart Empty Page', 'wcbn');
        self::$wc_actions["woocommerce_after_shop_loop"] = __('On Product List Page(if products exist)', 'wcbn');
        self::$wc_actions["woocommerce_after_single_product"] = __('On Single Product', 'wcbn');
    }

    /**
     * @return $wc_actions
     */
    public static function get_wc_actions() {
        return self::$wc_actions;
    }


    /**
     * Register post type
     */
    public static function register_post_type() {
        $labels = array(
            'name'               => _x('Browser Notify Triggers', 'post type general name', 'wcbn'),
            'singular_name'      => _x('Browser Notify Trigger', 'post type singular name', 'wcbn'),
            'menu_name'          => _x('Browser Notify Triggers', 'admin menu', 'wcbn'),
            'name_admin_bar'     => _x('Browser Notify Triggers', 'add new on admin bar', 'wcbn'),
            'add_new_item'       => __('Add New Browser Notify', 'wcbn'),
            'new_item'           => __('New Browser Notify', 'wcbn'),
            'edit_item'          => __('Edit Browser Notify', 'wcbn'),
            'view_item'          => __('View Browser Notify', 'wcbn'),
            'all_items'          => __('Browser Notify Triggers', 'wcbn'),
            'search_items'       => __('Search Browser Notify', 'wcbn'),
            'parent_item_colon'  => __('Parent Browser Notify:', 'wcbn'),
            'not_found'          => __('No Browser Notify found.', 'wcbn'),
            'not_found_in_trash' => __('No Browser Notify found in Trash.', 'wcbn')
        );

        register_post_type(self::$post_type, array(
            'labels' => $labels,
            'description' => __('This is where you can add Browser Notify.', 'wcbn'),
            'public' => false,
            'show_ui' => true,
            'supports' => array('title'),
            'register_meta_box_cb' => array(__CLASS__, "add_meta_box"),
            'show_in_menu' => 'woocommerce',
            'show_in_nav_menus'   => true
        ));

        $labels = array(
            'name'               => _x('Browser Notify Popup', 'post type general name', 'wcbn'),
            'singular_name'      => _x('Browser Notify Popup', 'post type singular name', 'wcbn'),
            'menu_name'          => _x('Browser Notify Popup', 'admin menu', 'wcbn'),
            'name_admin_bar'     => _x('Browser Notify Popup', 'add new on admin bar', 'wcbn'),
            'add_new_itemotify'  => __('Add New Browser Notify Popup', 'wcbn'),
            'new_item'           => __('New Browser Notify Popup', 'wcbn'),
            'edit_item'          => __('Edit Browser Notify Popup', 'wcbn'),
            'view_item'          => __('View Browser Notify Popup', 'wcbn'),
            'all_items'          => __('Browser Notify Popups', 'wcbn'),
            'search_items'       => __('Search Browser Notify Popup', 'wcbn'),
            'parent_item_colon'  => __('Parent Browser Notify Popup:', 'wcbn'),
            'not_found'          => __('No Browser Notify Popup found.', 'wcbn'),
            'not_found_in_trash' => __('No Browser Notify Popup found in Trash.', 'wcbn')
        );

        register_post_type(self::$post_type . '-popup', array(
            'labels' => $labels,
            'description' => __('This is where you can add Browser Notify Popup.', 'wcbn'),
            'public' => false,
            'show_ui' => true,
            'supports' => array('title', 'editor'),
            'show_in_menu' => 'woocommerce',
            'show_in_nav_menus'   => true
        ));

    }

 
    public static function move_metabox_after_title ($post) {
        global $post, $wp_meta_boxes;
        do_meta_boxes( null, 'notify-trigger-box', $post );
        do_meta_boxes( null, 'notify-popup-box', $post );
    }
    /**
     * Get all created triggers
     * @return triggers_list
     */
    public static function get_triggers()
    {
        $args = array(
            'post_type'   => self::$post_type,
            'post_status' => 'publish',
            'numberposts' => -1
        );
        $notifications = get_posts($args);

        if(!$notifications) {
            return false;
        }

        $triggers = [];
        foreach($notifications as $post) {
            $triggers[$post->ID] = get_post_meta($post->ID, self::$field["trigger"], true);
        }

        return $triggers;
    }

    /**
     * Set Dropdown option attributes
     * @return dropdopwn_option_attributes
     */
    public static function option_attr($key, $action, $id, $opts)
    {
        $opt = [];
        extract($opts);

        if(isset($created_triggers) && $created_triggers) {
            $trigger_created = array_search($key, $created_triggers);
            if(($trigger_created !== false && $trigger_created != $id)) {
                $opt['attr'] = \__('disabled ', 'wcbn');
                $opt['action'] = $action . \__('(assigned)', 'wcbn');
            } else {
                $opt['attr'] = "";
                $opt['action'] = $action;
            }
        } else {
            $opt['attr'] = "";
            $opt['action'] = $action;
        }

        if($sel_trigger == $key) {
            $opt['attr'] .= \__('selected ', 'wcbn');
        }
        return $opt;
    }

    /**
     * Get markup for action trigger dropdown
     * @return dropdopwn_list
    */
    public static function notify_trigger_markup($post)
    {
        $id = $post->ID;
        $opts['created_triggers'] = self::get_triggers();
        $opts['sel_trigger'] = get_post_meta($id, self::$field["trigger"], 1);
        $field_delay = get_post_meta($id, self::$field["delay"], 1);
        $field_override_open = get_post_meta($id, self::$field["override"], 1);
        $is_wc_action = false;
        ?>
        <span id="trigger-notify-wrap">
            <label for="wcbn-trigger-notify">Select Trigger:</label>
            <select id="wcbn-trigger-notify" onchange="wcbn_trigger_change(this);" name='wcbn-trigger-notify' >
                <?php if(self::$new_page): ?>
                    <option selected disabled value=''> <?php \_e("Select Trigger" , "wcbn") ?> </option>
                <?php endif; ?>    
                
                <optgroup label="Browser Actions">
                    <?php foreach (self::$browser_actions as $key => $action): ?>
                        <?php $opt = self::option_attr($key, $action, $id, $opts);?>
                        <option <?php echo $opt["attr"] ?> value="<?php echo $key; ?>">
                            <?php echo $opt["action"] ?>
                        </option>
                    <?php endforeach; ?>
                </optgroup>
                <optgroup label="WC Actions">
                    <?php foreach (self::$wc_actions as $key => $action): ?>
                        <?php $opt = self::option_attr($key, $action, $id, $opts);?>
                        <option class="wc-action-options" <?php echo $opt["attr"] ?> value="<?php echo $key; ?>">
                            <?php echo $opt["action"] ?>
                            <?php if($opts['sel_trigger'] == $key) 
                                $is_wc_action = true;
                            ?>
                        </option>
                    <?php endforeach; ?>
                </optgroup>
            </select>
            <br />
            <label for="wcbn-trigger-delay" >Delay(in secs):</label>
            <input type="text" name="wcbn-trigger-delay"  id="wcbn-trigger-delay"  
                value="<?php echo $field_delay?>"/>
        
            <div id="wcbn-override-open" <?php echo (!$is_wc_action || $opts['sel_trigger'] == 'open' ?  "class='wcbn-hide'" : '')?>>
                <span >
                    <label for="wcbn-override-open">Override 'On page load' trigger:</label>
                    <input type="checkbox" name="wcbn-override-open"
                        value="1" <?php echo ($field_override_open ? "checked" : "")?>/>
                </span>
            </div>
            <?php wp_nonce_field( 'trigger_notify_submit', 'trigger_notify_form' ); ?>
        </span>
        <?php
    }

    /**
     * Add Custom Column Header
     * @hook manage_{post_type}_posts_columns
    */
    public static function notify_column_head($defaults) {
        // Show date after custom column
        unset($defaults['date']);
        $defaults['associated_popup'] = \__('Associated Popup', 'wcbn');
        $defaults['date'] = \__('Date');
        return $defaults;
    }
     
    /**
     * Add Custom Column Content
     * @return manage_{post_type}_posts_custom_column
    */
    public static function notify_column_content($column_name, $post_ID) {
        if ($column_name == "associated_popup") {
            $popup_id = get_post_meta($post_ID, self::$field["popup"], 1);
            $popup_title = get_the_title($popup_id);
            if($popup_title) {
                echo $popup_title;
            }
        }
    }

    /**
     * Get markup for Popup selector
     * @return dropdopwn_list
    */
    public static function notify_popup_markup($post)
    {
        $field = get_post_meta($post->ID, self::$field["popup"], true);
        $args = array(
            'post_type' => self::$post_type . '-popup'
        );
        $loop = new \WP_Query($args);

        echo '<span id="notify-popup-wrap">
        <label for="wcbn-notify-popup">Associate Popup: </label>
        <select name="wcbn-notify-popup" id="wcbn-notify-popup">';
            if(self::$new_page) {
                echo '<option selected value="" disabled>'. \__("Select Popup" , "wcbn") .'</option>';
            }

            while($loop->have_posts()): $loop->the_post();
                $selected = 0;
                $id = get_the_ID();
                $title = get_the_title();
                if($field == $id) {
                    $selected = 1;
                }
                echo "<option value='".$id."'".($selected ? \__('selected', 'wcbn') : '')." >" . $title . "</option>";
            endwhile;
        echo '</select>
        </span>
        ';
        wp_nonce_field( 'notify_popup_submit', 'notify_popup_form' );
        wp_reset_query();  
    }


    /**
     * Add Action Trigger metabox
     * @return string
    */
    public static function add_meta_box()
    {
        add_meta_box(self::$field["trigger"], "Notify Trigger",  array(__CLASS__,"notify_trigger_markup"), "wcbn-notify", "notify-trigger-box", "high", null);

        add_meta_box(self::$field["popup"], "Notify Popup",  array(__CLASS__,"notify_popup_markup"), "wcbn-notify", "notify-popup-box", "high", null);
    }

    /**
     * Get post type for Global tabs
     * @return string
     */
    public static function get_posttype() {
        return self::$post_type;
    }

    /**
     * Hooked into save_post_{$post_type}
     * @param $post_id
     * @return void
     */
    public static function save_post($post_id, $post, $update) {
        if ( ! isset( $_POST['trigger_notify_form'] ) || ! wp_verify_nonce( $_POST['trigger_notify_form'], 'trigger_notify_submit' )) {
            return $post_id;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Don't save revisions and autosaves
        if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
            return;
        }

        if (!$update) return;

        //Update Meta Field
        update_post_meta($post_id, self::$field["trigger"], $_POST["wcbn-trigger-notify"]);
        update_post_meta($post_id, self::$field["delay"], $_POST["wcbn-trigger-delay"]);
        update_post_meta($post_id, self::$field["override"], $_POST["wcbn-override-open"]);
        update_post_meta($post_id, self::$field["popup"], $_POST["wcbn-notify-popup"]);

    }
}
