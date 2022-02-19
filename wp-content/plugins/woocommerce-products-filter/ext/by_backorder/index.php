<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

//20-06-2018
final class WOOF_EXT_BY_ONBACKORDER extends WOOF_EXT {

    public $type = 'by_html_type';
    public $html_type = 'by_backorder'; //your custom key here
    public $index = 'backorder';
    public $html_type_dynamic_recount_behavior = 'none';

    public function __construct()
    {
        parent::__construct();
        $this->init();
    }

    public function get_ext_path()
    {
        return plugin_dir_path(__FILE__);
    }

    public function get_ext_link()
    {
        return plugin_dir_url(__FILE__);
    }

    public function woof_add_items_keys($keys)
    {
        $keys[] = $this->html_type;
        return $keys;
    }

    public function init()
    {
        add_filter('woof_add_items_keys', array($this, 'woof_add_items_keys'));
        add_action('woof_print_html_type_options_' . $this->html_type, array($this, 'woof_print_html_type_options'), 10, 1);
        add_action('woof_print_html_type_' . $this->html_type, array($this, 'print_html_type'), 10, 1);
        add_action('wp_head', array($this, 'wp_head'), 999);

        self::$includes['js']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'js/' . $this->html_type . '.js';
        self::$includes['css']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'css/' . $this->html_type . '.css';
        self::$includes['js_init_functions'][$this->html_type] = 'woof_init_onbackorder';
    }

    public function wp_head()
    {
        global $WOOF;
        ?>      
        <script type="text/javascript">
            if (typeof woof_lang_custom == 'undefined') {
                var woof_lang_custom = {};/*!!important*/
            }
            woof_lang_custom.<?php echo $this->index ?> = "<?php _e('Exclude On backorder', 'woocommerce-products-filter') ?>";
        </script>
        <?php
    }

    //settings page hook
    public function woof_print_html_type_options()
    {
        global $WOOF;
        echo $WOOF->render_html($this->get_ext_path() . 'views' . DIRECTORY_SEPARATOR . 'options.php', array(
            'key' => $this->html_type,
            "woof_settings" => get_option('woof_settings', array())
                )
        );
    }


}

WOOF_EXT::$includes['html_type_objects']['by_backorder'] = new WOOF_EXT_BY_ONBACKORDER();
