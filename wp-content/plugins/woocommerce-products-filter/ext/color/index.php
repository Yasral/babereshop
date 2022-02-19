<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_EXT_COLOR extends WOOF_EXT
{

    public $type = 'html_type';
    public $html_type = 'color'; //your custom key here
    public $html_type_dynamic_recount_behavior = 2;

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

    public function init()
    {
        
    }

    public function admin_head()
    {
        
    }

    public function woof_add_html_types($types)
    {
        //$types[$this->html_type] = __('Color', 'woocommerce-products-filter');
        return $types;
    }

    public function woocommerce_settings_tabs_woof()
    {
        //wp_enqueue_style('wp-color-picker');
        //wp_enqueue_script('wp-color-picker');
    }

    public function wp_head()
    {
        
    }

    public function print_additional_options($key)
    {
        
    }

    public function woof_print_design_additional_options()
    {
        
    }

}

WOOF_EXT::$includes['taxonomy_type_objects']['color'] = new WOOF_EXT_COLOR();
