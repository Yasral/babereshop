<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_EXT_BY_SKU extends WOOF_EXT
{

    public $type = 'by_html_type';
    public $html_type = 'by_sku'; //your custom key here
    public $index = 'woof_sku';
    public $html_type_dynamic_recount_behavior = null;

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
    }

    public function wp_head()
    {
    }

    //shortcode
    public function woof_sku_filter($args = array())
    {
        
    }

    //settings page hook
    public function woof_print_html_type_options()
    {
        
    }

    public function assemble_query_params(&$meta_query, &$query = NULL)
    {

        return $meta_query;
    }

}

WOOF_EXT::$includes['html_type_objects']['by_sku'] = new WOOF_EXT_BY_SKU();
