<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WOOF_SEO {

    protected $no_index_search = false;
    protected $rules = array();
    protected $curr_url = '';
    protected $current_rule = array();
    protected $current_replace_vars = array();

    public function __construct($rules, $url) {
        if (is_admin() && (!defined('DOING_AJAX') || !DOING_AJAX )) {
            return false;
        }
        global $WOOF;
        $this->rules = $rules;
        $this->curr_url = $url;

        if (isset($WOOF->woof_settings['woof_url_request']['page_index'])) {
            $this->no_index_search = $WOOF->woof_settings['woof_url_request']['page_index'];
        }

        $this->special_filters = array(
            'instock' => array('stock' => 'instock'),
            'onsale' => array('onsales' => 'salesonly'),
            'featured' => array('product_visibility' => 'featured'),
            'backorder_not_in' => array('backorder' => 'onbackorder'),
        );

        add_action('wp_head', array($this, 'meta_head'), 1);
        add_filter('woocommerce_page_title', array($this, 'set_h1'));
       // add_filter( 'wp_title', array( $this, 'set_title'), 999999,3 );
       // add_filter('document_title_parts', array($this, 'set_title'), 9999, 2);		
		add_filter( 'pre_get_document_title', array($this, 'pre_get_document_title'), 999 );
        add_filter('the_title', array($this, 'set_page_title'), 10000, 2);
        add_filter('wp_robots', array($this, 'wp_robots'));

        //remove_action( 'wp_head', 'rel_canonical' );
        remove_action('wp_head', 'index_rel_link');
        remove_action('wp_head', 'start_post_rel_link');
        remove_action('wp_head', 'gutenberg_render_title_tag', 1);
        //for ajax
        add_filter('woof_draw_products_get_args', array($this, 'ajax_page_title'), 100, 2);
    }

    public function check_search_rules() {

        $rules = $this->rules;
        $current_url = $this->curr_url;
        if ($this->current_rule) {
            return $this->current_rule;
        }
        foreach ($rules as $key => $rule_data) {
            if (isset($rule_data['url'])) {
                $needle = array('{any}', '/');
                $replase = array('.*', '\/');
                $url = str_replace($needle, $replase, $rule_data['url']);
                preg_match('/' . $url . '/', $current_url, $matches);
                if ($matches) {
                    $this->current_rule = $rule_data;
                    break;
                }
            }
        }

        return $this->current_rule;
    }

    public function is_search_going() {
        global $WOOF;
        $is_search = false;
        if ($WOOF->is_isset_in_request_data($WOOF->get_swoof_search_slug())) {
            $is_search = true;
        }

        return $is_search;
    }

    public function do_index() {
        $do_index = $this->no_index_search;

        if ($this->check_search_rules()) {
            $do_index = true;
        }

        return $do_index;
    }

    public function ajax_page_title($get, $link) {
        $this->curr_url = $link;

        $rule = $this->check_search_rules();
		if($rule){
			if (!isset($rule['title'])) {
				$rule['title'] = '';
			}
			if (!isset($rule['h1'])) {
				$rule['h1'] = '';
			}
			//$rule['title'] = apply_filters('woof_seo_meta_title', $this->replace_vars($rule['title'], $this->get_current_replace_vars()));
			$title = apply_filters('woof_seo_h1', $this->replace_vars($rule['h1'], $this->get_current_replace_vars()));
			$get["woof_redraw_elements"]['.entry-title'] = sprintf('<h1 class="entry-title">%s</h1>', $title);
			$get["woof_redraw_elements"]['.woocommerce-products-header__title'] = sprintf('<h1 class="woocommerce-products-header__title page-title">%s</h1>', $title);
		}
        return $get;
    }

    public function meta_head() {
        //show  meta desc  title  
        $rule = $this->check_search_rules();

        if (!$rule) {
            return false;
        }
        //title
        if (!isset($rule['title']) || !$rule['title']) {
            $rule['title'] = wp_get_document_title();
        }

        if (!isset($rule['description'])) {
            $rule['description'] = '';
        }

       // $rule['title'] = apply_filters('woof_seo_meta_title', $this->replace_vars($rule['title'], $this->get_current_replace_vars()));

        $rule['description'] = apply_filters('woof_seo_meta_description', $this->replace_vars($rule['description'], $this->get_current_replace_vars()));

        if ($rule['description']) {
            echo sprintf('<meta name="description" content="%s" />', esc_attr($rule['description'])) . "\r\n";
        }
    }

    public function pre_get_document_title($title) {

        $rule = $this->check_search_rules();
        if ($rule) {

            $title = apply_filters('woof_seo_meta_title', $this->replace_vars($rule['title'], $this->get_current_replace_vars()));

        }

        return $title;
    }
	public function set_title($title, $sep = '-', $seplocation ='') {

        $rule = $this->check_search_rules();
        if ($rule) {
            if (isset($rule['title']) AND $rule['title']) {
                $title['title'] = apply_filters('woof_seo_meta_title', $this->replace_vars($rule['title'], $this->get_current_replace_vars()));
            }
        }

        return $title;
    }

    public function set_page_title($title, $id) {
        $rule = $this->check_search_rules();
        if ($rule && is_page($id)) {
            if (isset($rule['h1']) AND $rule['h1']) {
                $title = apply_filters('woof_seo_h1', $this->replace_vars($rule['h1'], $this->get_current_replace_vars()));
            }
        }

        return $title;
    }

    public function set_h1($title) {
        $rule = $this->check_search_rules();
        if ($rule) {
            if (isset($rule['h1']) AND $rule['h1']) {
                $title = apply_filters('woof_seo_h1', $this->replace_vars($rule['h1'], $this->get_current_replace_vars()));
            }
        }

        return $title;
    }

    public function replace_vars($string, $replace_vars) {
        foreach ($replace_vars as $key => $var) {
            $string = str_replace('{' . $key . '}', $var, $string);
        }
        $string = preg_replace('/\{[a-zA-Z0-9_\W]+?\}/m', '', $string);

        return $string;
    }

    public function get_current_replace_vars() {
        if (!count($this->current_replace_vars)) {
            $replace_vars = array();
            global $WOOF;
            $request = $WOOF->get_request_data();
            $taxonomies = $WOOF->get_taxonomies();
            $settings = $WOOF->settings;

            foreach ($request as $key => $val) {
                if ('product_visibility' == $key) {
                    $key = 'featured';
                }

                if (isset($taxonomies[$key]) AND 'product_visibility' != $key) {

                    $replace_vars[$key . '_title'] = WOOF_HELPER::wpml_translate($taxonomies[$key]);
                    $slugs = explode(',', $val);
                    $terms = get_terms(array(
                        'taxonomy' => $taxonomies[$key]->name,
                        'slug' => $slugs,
                        'fields' => 'names'
                    ));
                    $replace_vars[$key] = implode(', ', $terms);
                } elseif (class_exists('WOOF_META_FILTER') AND WOOF_META_FILTER::get_meta_filter_name($key)) {
                    $replace_vars[$key . '_title'] = WOOF_META_FILTER::get_meta_filter_name($key);
                    $replace_vars[$key] = WOOF_META_FILTER::get_meta_filter_option_name($key, $val);
                } elseif ($key == 'min_price') {
                    $f_key = 'by_price';
                    $replace_vars[$f_key . '_title'] = esc_html__("Price", 'woocommerce-products-filter');
                    $from = wc_price((float) $val);
                    $to = '';
                    if (isset($request['max_price'])) {
                        $to = wc_price((float) $request['max_price']);
                    }
                    $replace_vars[$f_key] = esc_html__("from", 'woocommerce-products-filter') . ' ' . $from;
                    if ($to) {
                        $replace_vars[$f_key] .= ' ' . esc_html__("to", 'woocommerce-products-filter') . ' ' . $to;
                    }
                } else {

                    if (!empty(WOOF_EXT::$includes['html_type_objects'])) {

                        foreach (WOOF_EXT::$includes['html_type_objects'] as $obj) {
                            if ($obj->index == $key) {

                                $f_key = $obj->html_type;
                                $title = '';
                                $value = '';
                                if (isset(WOOF_EXT::$includes['js_lang_custom'][$key])) {
                                    $title = apply_filters('woof_ext_custom_title_' . $f_key, WOOF_EXT::$includes['js_lang_custom'][$key]);
                                }

                                if ('by_author' == $f_key) {
                                    $user = get_user_by('id', (int) $val);
                                    if (is_object($user)) {
                                        $value = $user->display_name;
                                    }
                                } elseif ('by_rating' == $f_key) {
                                    $vals = array(
                                        0 => esc_html__('Filter by rating', 'woocommerce-products-filter'),
                                        4 => esc_html__('average rating between 4 to 5', 'woocommerce-products-filter'),
                                        3 => esc_html__('average rating between 3 to 4-', 'woocommerce-products-filter'),
                                        2 => esc_html__('average rating between 2 to 3-', 'woocommerce-products-filter'),
                                        1 => esc_html__('average rating between 1 to 2-', 'woocommerce-products-filter')
                                    );
                                    if (isset($vals[$val])) {
                                        $value = $vals[$val];
                                    }
                                } elseif ('by_sku' == $f_key OR 'by_text' == $f_key) {
                                    $value = '"' . $val . '"';
                                } else {
                                    $value = $title;
                                }

                                $replace_vars[$f_key] = $value;
                                $replace_vars[$f_key . '_title'] = $title;
                            }
                        }
                    }
                }
            }

            $this->current_replace_vars = $replace_vars;
        }

        return apply_filters('woof_seo_request_literals', $this->current_replace_vars);
    }

    public function wp_robots($robots) {
        if ($this->is_search_going()) {
            if ($this->do_index()) {
                $robots['index'] = true;
                $robots['follow'] = true;
                $robots['noindex'] = false;
                $robots['nofollow'] = false;
            } else {
                $robots['noindex'] = true;
                $robots['nofollow'] = true;
                $robots['index'] = false;
                $robots['follow'] = false;
            }
        }

        return $robots;
    }

}
