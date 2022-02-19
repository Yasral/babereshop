<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_EXT_SELECT_HIERARCHY extends WOOF_EXT
{

    public $type = 'html_type';
    public $html_type = 'select_hierarchy'; //your custom key here
    public $html_type_dynamic_recount_behavior = 1;

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

    public function woof_add_html_types($types)
    {
        $types[$this->html_type] = __('Hierarchy drop-down', 'woocommerce-products-filter');
        return $types;
    }

    public function init()
    {
        
    }



}

WOOF_EXT::$includes['taxonomy_type_objects']['select_hierarchy'] = new WOOF_EXT_SELECT_HIERARCHY();
