<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_EXT_BY_TEXT extends WOOF_EXT {

    public $type = 'by_html_type';
    public $html_type = 'by_text'; //your custom key here
    public $index = 'woof_text';
    public $html_type_dynamic_recount_behavior = 'none';

    public function __construct() {
        parent::__construct();
        $this->init();
    }

    public function get_ext_path() {
        return plugin_dir_path(__FILE__);
    }

    public function get_ext_override_path() {
        return get_stylesheet_directory() . DIRECTORY_SEPARATOR . "woof" . DIRECTORY_SEPARATOR . "ext" . DIRECTORY_SEPARATOR . $this->html_type . DIRECTORY_SEPARATOR;
    }

    public function get_ext_link() {
        return plugin_dir_url(__FILE__);
    }

    public function woof_add_items_keys($keys) {
        $keys[] = $this->html_type;
        return $keys;
    }

    public function init() {
        add_filter('woof_add_items_keys', array($this, 'woof_add_items_keys'));
        add_filter('woof_get_request_data', array($this, 'woof_get_request_data'));

        add_filter('woof_dynamic_count_attr', array($this, 'cache_compatibility'), 99, 2);

        add_action('woof_print_html_type_options_' . $this->html_type, array($this, 'woof_print_html_type_options'), 10, 1);
        add_action('woof_print_html_type_' . $this->html_type, array($this, 'print_html_type'), 10, 1);
        add_action('wp_enqueue_scripts', array($this, 'wp_head'), 9);

        add_action('wp_ajax_woof_text_autocomplete', array($this, 'woof_text_autocomplete'));
        add_action('wp_ajax_nopriv_woof_text_autocomplete', array($this, 'woof_text_autocomplete'));

        self::$includes['js']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'js/' . $this->html_type . '.js';
        self::$includes['css']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'css/' . $this->html_type . '.css';
        self::$includes['js_init_functions'][$this->html_type] = 'woof_init_text'; //we have no init function in this case
        //***
        add_shortcode('woof_text_filter', array($this, 'woof_text_filter'));
    }

    public function woof_get_request_data($request) {
        if (isset($request['s'])) {
            $request['woof_text'] = $request['s'];
        }

        return $request;
    }

    public function cache_compatibility($args, $type) {
        global $WOOF;
        $request = $WOOF->get_request_data();
        if (isset($request['woof_text']) AND $request['woof_text']) {
            $args['woof_text'] = $request['woof_text'];
        }

        return $args;
    }

    public function wp_head() {
        global $WOOF;
        self::$includes['js_code_custom']['woof_' . $this->html_type . '_html_items'] = $this->get_js();
        self::$includes['css_code_custom'][$this->index] = $this->get_style();
        $request = $WOOF->get_request_data();
        $search_text = "";
        if (isset($request['woof_text']) AND $request['woof_text']) {
            $search_text = ":" . $request['woof_text'];
        }
        self::$includes['js_lang_custom'][$this->index] = esc_html__('By text', 'woocommerce-products-filter') . $search_text;
        //***
        if (isset($WOOF->settings['by_text']['autocomplete']) AND $WOOF->settings['by_text']['autocomplete']) {
            wp_enqueue_script('easy-autocomplete', WOOF_LINK . 'js/easy-autocomplete/jquery.easy-autocomplete.min.js', array('jquery'), WOOF_VERSION);
            wp_enqueue_style('easy-autocomplete', WOOF_LINK . 'js/easy-autocomplete/easy-autocomplete.min.css', array(), WOOF_VERSION);
            wp_enqueue_style('easy-autocomplete-theme', WOOF_LINK . 'js/easy-autocomplete/easy-autocomplete.themes.min.css', array(), WOOF_VERSION);
        }
    }

    public function get_js() {
        global $WOOF;
        ob_start();
        ?>
        var woof_text_autocomplete = 0;
        var woof_text_autocomplete_items = 10;
        <?php if (isset($WOOF->settings['by_text']['autocomplete'])): ?>
            woof_text_autocomplete =<?php echo (int) $WOOF->settings['by_text']['autocomplete']; ?>;
            woof_text_autocomplete_items =<?php echo apply_filters('woof_text_autocomplete_items', 10) ?>;
        <?php endif; ?>

        var woof_post_links_in_autocomplete = 0;
        <?php if (isset($WOOF->settings['by_text']['post_links_in_autocomplete'])): ?>
            woof_post_links_in_autocomplete =<?php echo (int) $WOOF->settings['by_text']['post_links_in_autocomplete']; ?>;
        <?php endif; ?>

        var how_to_open_links = 0;
        <?php if (isset($WOOF->settings['by_text']['how_to_open_links'])): ?>
            how_to_open_links =<?php echo (int) $WOOF->settings['by_text']['how_to_open_links']; ?>;
            <?php
        endif;
        return ob_get_clean();
    }

    public function get_style() {
        global $WOOF;
        ob_start();
        if (isset($WOOF->settings['by_text']['image'])) {
            if (!empty($WOOF->settings['by_text']['image'])) {
                ?>
                .woof_text_search_container .woof_text_search_go{
                background: url(<?php echo $WOOF->settings['by_text']['image'] ?>) !important;
                }
                <?php
            }
        }
        return ob_get_clean();
    }

    //shortcode
    public function woof_text_filter($args = array()) {
        global $WOOF;
        if (!is_array($args)) {
            $args = array();
        }
        $args['loader_img'] = $this->get_ext_link() . 'img/loader.gif';

        if (file_exists($this->get_ext_override_path() . 'views' . DIRECTORY_SEPARATOR . 'shortcodes' . DIRECTORY_SEPARATOR . 'woof_text_filter.php')) {
            return $WOOF->render_html($this->get_ext_override_path() . 'views' . DIRECTORY_SEPARATOR . 'shortcodes' . DIRECTORY_SEPARATOR . 'woof_text_filter.php', $args);
        }

        return $WOOF->render_html($this->get_ext_path() . 'views' . DIRECTORY_SEPARATOR . 'shortcodes' . DIRECTORY_SEPARATOR . 'woof_text_filter.php', $args);
    }

    //settings page hook
    public function woof_print_html_type_options() {
        global $WOOF;
        echo $WOOF->render_html($this->get_ext_path() . 'views' . DIRECTORY_SEPARATOR . 'options.php', array(
            'key' => $this->html_type,
            "woof_settings" => get_option('woof_settings', array())
                )
        );
    }

    public function assemble_query_params(&$meta_query, $wp_query = NULL) {
        add_filter('posts_where', array($this, 'woof_post_text_filter'), 9999, 2); //for searching by text
        return $meta_query;
    }

    public function woof_post_text_filter($where = '', $query = null) {
        global $wp_query;
        global $WOOF;
        $request = $WOOF->get_request_data();

        //***

        if (!defined('DOING_AJAX')) {
            if (!isset($query->query_vars['post_type']) && !isset($query->query_vars['wc_query'])) {
                return $where;
            }
            if ($query->query_vars['post_type'] != "product") {
                if (is_array($query->query_vars['post_type'])) {
                    if (!in_array("product", $query->query_vars['post_type'])) {
                        return $where;
                    }
                } elseif (!isset($query->query_vars['wc_query']) OR $query->query_vars['wc_query'] != 'product_query') {

                    return $where;
                }
            }
        }

        //***      
        if (!isset($request['woof_text']) OR!$request['woof_text']) {
            return $where;
        }

        if (defined('DOING_AJAX')) {
            $conditions = (isset($wp_query->query_vars['post_type']) AND $wp_query->query_vars['post_type'] == 'product') OR isset($_REQUEST['woof_products_doing']);
        } else {
            $conditions = isset($_REQUEST['woof_products_doing']);
        }
        //***
        {
            if ($WOOF->is_isset_in_request_data('woof_text')) {

                $woof_text = wp_specialchars_decode(trim(urldecode($request['woof_text'])));
                $woof_text = trim(WOOF_HELPER::strtolower($woof_text));
                $woof_text = preg_replace('/\s+/', ' ', $woof_text);
                if (!$woof_text) {
                    return $where;
                }
                if (!apply_filters('woof_text_search_like_option', false)) {
                    $woof_text = preg_quote($woof_text, '&');
                    $woof_text = str_replace(' ', '?(.*)', $woof_text);
                    $woof_text = stripslashes($woof_text);
                    $woof_text = str_replace("&#039;", "'", $woof_text);

                    $woof_text = str_replace("\&quot;", "\"", $woof_text);
                    $woof_text = str_replace("\(", "\\\(", $woof_text);
                    $woof_text = str_replace("\)", "\\\)", $woof_text);
                    //http://dev.mysql.com/doc/refman/5.7/en/regexp.html
                    $search_by_full_word = false;

                    if (isset($WOOF->settings['by_text']['search_by_full_word'])) {
                        $search_by_full_word = (int) $WOOF->settings['by_text']['search_by_full_word'];
                    }

                    if ($search_by_full_word) {
                        $woof_text = '[[:<:]]' . $woof_text . '[[:>:]]';
                    }

                    //***

                    $behavior = 'title';
                    if (isset($WOOF->settings['by_text']['behavior'])) {
                        $behavior = $WOOF->settings['by_text']['behavior'];
                    }

                    if (isset($_REQUEST['auto_search_by_behavior']) AND!empty($_REQUEST['auto_search_by_behavior'])) {
                        $behavior = $_REQUEST['auto_search_by_behavior'];
                    }

                    $text_where = "";
                    //***
                    switch ($behavior) {
                        case 'content':
                            $text_where .= " LOWER(post_content) REGEXP '{$woof_text}'";
                            break;

                        case 'title_or_content':
                            $text_where .= " ( LOWER(post_title) REGEXP '{$woof_text}' OR LOWER(post_content) REGEXP '{$woof_text}')";
                            break;

                        case 'title_and_content':
                            $text_where .= " ( LOWER(post_title) REGEXP '{$woof_text}' AND LOWER(post_content) REGEXP '{$woof_text}')";
                            break;

                        case 'excerpt':
                            $text_where .= " LOWER(post_excerpt) REGEXP '{$woof_text}'";
                            break;

                        case 'content_or_excerpt':
                            $text_where .= " ( LOWER(post_excerpt) REGEXP '{$woof_text}' OR LOWER(post_content) REGEXP '{$woof_text}')";
                            break;

                        case 'title_or_content_or_excerpt':
                            $text_where .= "  (( LOWER(post_title) REGEXP '{$woof_text}') OR ( LOWER(post_excerpt) REGEXP '{$woof_text}') OR ( LOWER(post_content) REGEXP '{$woof_text}'))";
                            break;

                        default:
                            //only by title
                            $text_where .= "  LOWER(post_title) REGEXP '{$woof_text}'";
                            break;
                    }
                } else {
                    $woof_text = str_replace("\&#039;", "\'", $woof_text);
                    $woof_text = str_replace("\&quot;", "\"", $woof_text);
                    $woof_text = str_replace("\(", "\\\(", $woof_text);
                    $woof_text = str_replace("\)", "\\\)", $woof_text);

                    $search_by_full_word = false;

                    if (isset($WOOF->settings['by_text']['search_by_full_word'])) {
                        $search_by_full_word = (int) $WOOF->settings['by_text']['search_by_full_word'];
                    }
                    $woof_text_array = explode(" ", $woof_text);
                    $text_where = "";
                    $text_array = array();
                    foreach ($woof_text_array as $text) {

                        if (!$search_by_full_word) {
                            $text = '%' . $text . '%';
                        }

                        $behavior = 'title';
                        if (isset($WOOF->settings['by_text']['behavior'])) {
                            $behavior = $WOOF->settings['by_text']['behavior'];
                        }

                        if (isset($_REQUEST['auto_search_by_behavior']) AND!empty($_REQUEST['auto_search_by_behavior'])) {
                            $behavior = $_REQUEST['auto_search_by_behavior'];
                        }


                        //***
                        switch ($behavior) {
                            case 'content':
                                $text_array[] = "( LOWER(post_content) LIKE '{$text}')";
                                break;

                            case 'title_or_content':
                                $text_array[] = " ( LOWER(post_title) LIKE '{$text}' OR LOWER(post_content) LIKE '{$text}')";
                                break;

                            case 'title_and_content':
                                $text_array[] = " ( LOWER(post_title) LIKE '{$text}' AND LOWER(post_content) LIKE '{$text}')";
                                break;

                            case 'excerpt':
                                $text_array[] = " LOWER(post_excerpt) LIKE '{$text}'";
                                break;

                            case 'content_or_excerpt':
                                $text_array[] = " ( LOWER(post_excerpt) LIKE '{$text}' OR LOWER(post_content) LIKE '{$text}')";
                                break;

                            case 'title_or_content_or_excerpt':
                                $text_array[] .= "  (( LOWER(post_title) LIKE '{$text}') OR ( LOWER(post_excerpt) LIKE '{$text}') OR ( LOWER(post_content) LIKE '{$woof_text}'))";
                                break;

                            default:
                                //only by title
                                $text_array[] = " ( LOWER(post_title) LIKE '{$text}' ) ";
                                break;
                        }
                    }
                    $text_where = implode(" AND ", $text_array);
                }
                global $wpdb;

                //_variation_description
                $var_desc_where = "";
                if (isset($WOOF->settings['by_text']['search_desc_variant']) AND $WOOF->settings['by_text']['search_desc_variant']) {
                    if (!in_array($behavior, array('excerpt', 'content', 'title'))) {
                        $condtion_string = "";
                        if (!empty($woof_text)) {

                            if (!apply_filters('woof_text_search_like_option', false)) {
                                $condtion_string .= " LOWER(postmeta.meta_value) REGEXP '{$woof_text}'";
                            } else {
                                $condtion_string .= " postmeta.meta_value LIKE '%$woof_text%'";
                            }
                        }

                        //***

                        $product_variations = $wpdb->get_results("
                        SELECT posts.ID
                        FROM $wpdb->posts AS posts
                        LEFT JOIN $wpdb->postmeta AS postmeta ON ( posts.ID = postmeta.post_id )
                        WHERE posts.post_type IN ('product_variation')
                        AND postmeta.meta_key = '_variation_description'
                        AND ($condtion_string)", ARRAY_N);
                        //+++
                        $product_variations_ids = array();
                        if (!empty($product_variations)) {
                            foreach ($product_variations as $v) {
                                $product_variations_ids[] = $v[0];
                            }

                            //+++
                            $product_variations_ids_string = implode(',', $product_variations_ids);

                            $products = $wpdb->get_results("
                            SELECT posts.post_parent
                            FROM $wpdb->posts AS posts
                            WHERE posts.ID IN ($product_variations_ids_string) AND posts.post_parent > 0", ARRAY_N);
                            //+++
                            $product_ids = array();
                            if (!empty($products)) {
                                foreach ($products as $v) {
                                    $product_ids[] = $v[0];
                                }
                            }

                            $product_ids = implode(',', array_merge($product_ids, $product_variations_ids));
                            $var_desc_where .= " $wpdb->posts.ID IN($product_ids)";
                        }
                        if ($var_desc_where AND!empty($var_desc_where)) {
                            $condition = " OR ";
                            if ($behavior = 'title_and_content') {
                                $condition = " OR ";
                            }
                            $var_desc_where = $condition . $var_desc_where;
                        }
                    }
                }


                //by SKU  *******************
                $sku_where = "";
                if ($WOOF->settings['by_text']['sku_compatibility']) {
                    //$woof_text = trim(urldecode($request['woof_sku']));
                    $woof_sku_request = explode(',', $request['woof_text']);
                    $woof_sku_request = array_map('urldecode', $woof_sku_request);
                    $woof_sku_request = array_map('trim', $woof_sku_request);
                    //***
                    if (!isset($WOOF->settings['by_sku']['logic']) OR empty($WOOF->settings['by_sku']['logic'])) {
                        $WOOF->settings['by_sku']['logic'] = 'LIKE';
                    }

                    $condtion_string = "";
                    if (!empty($woof_sku_request)) {
                        foreach ($woof_sku_request as $k => $sku) {
                            if ($k > 0) {
                                $condtion_string .= " OR ";
                            }
                            if ($WOOF->settings['by_sku']['logic'] == '=') {
                                $condtion_string .= "postmeta.meta_value {$WOOF->settings['by_sku']['logic']} '$sku'";
                            } else {
                                $condtion_string .= "postmeta.meta_value {$WOOF->settings['by_sku']['logic']} '%$sku%'";
                            }
                        }
                    }

                    //***

                    $product_variations = $wpdb->get_results("
                    SELECT posts.ID
                    FROM $wpdb->posts AS posts
                    LEFT JOIN $wpdb->postmeta AS postmeta ON ( posts.ID = postmeta.post_id )
                    WHERE posts.post_type IN ('product_variation','product')
                    AND postmeta.meta_key = '_sku'
                    AND ($condtion_string)", ARRAY_N);
                    //+++
                    $product_variations_ids = array();
                    if (!empty($product_variations)) {
                        foreach ($product_variations as $v) {
                            $product_variations_ids[] = $v[0];
                        }

                        //+++
                        $product_variations_ids_string = implode(',', $product_variations_ids);

                        $products = $wpdb->get_results("
                        SELECT posts.post_parent
                        FROM $wpdb->posts AS posts
                        WHERE posts.ID IN ($product_variations_ids_string) AND posts.post_parent > 0", ARRAY_N);
                        //+++
                        $product_ids = array();
                        if (!empty($products)) {
                            foreach ($products as $v) {
                                $product_ids[] = $v[0];
                            }
                        }
                        $product_ids = implode(',', array_merge($product_ids, $product_variations_ids));
                        $sku_where .= " $wpdb->posts.ID IN($product_ids)";
                        $where_sku = " AND $wpdb->posts.ID IN($product_ids)";
                    }
                    if ($sku_where AND!empty($sku_where)) {
                        $sku_where = " OR " . $sku_where;
                    }
                }
                //by SKU end  *******************


                $where .= " AND ( " . apply_filters('woof_text_search_query', $text_where . $var_desc_where . $sku_where, $woof_text) . " )   ";
            }
        }
        //***

        return $where;
    }

    //ajax
    public function woof_text_autocomplete() {
        $results = array();
        $args = array(
            'nopaging' => true,
            //'fields' => 'ids',
            'post_type' => 'product',
            'post_status' => array('publish'),
            'orderby' => 'title',
            'order' => 'ASC',
            'max_num_pages' => intval($_REQUEST['auto_res_count']) > 0 ? intval($_REQUEST['auto_res_count']) : apply_filters('woof_text_autocomplete_items', 10)
        );

        if (class_exists('SitePress')) {
            $args['lang'] = ICL_LANGUAGE_CODE;
        }

        //***

        $_GET['woof_text'] = $_REQUEST['phrase'];
        if (!empty($_REQUEST['auto_search_by'])) {
            $_REQUEST['auto_search_by_behavior'] = $_REQUEST['auto_search_by'];
        }
        add_filter('posts_where', array($this, 'woof_post_text_filter'), 10);
        $query = new WP_Query($args);
        //+++
        //http://easyautocomplete.com/guide
        if ($query->have_posts()) {
            include_once WOOF_PATH . 'lib' . DIRECTORY_SEPARATOR . 'aq_resizer.php';
            foreach ($query->posts as $p) {
                $data = array(
                    "name" => $p->post_title,
                    "type" => "product"
                );
                if (has_post_thumbnail($p->ID)) {
                    $img_src = wp_get_attachment_image_src(get_post_thumbnail_id($p->ID), 'single-post-thumbnail');
                    $data['icon'] = $img_src[0]; //woof_aq_resize($img_src[0], 100, 100, true);
                } else {
                    $data['icon'] = WOOF_LINK . 'img/not-found.jpg';
                }
                $data['link'] = get_post_permalink($p->ID);
                $results[] = $data;
            }
        } else {
            $results[] = array(
                "name" => esc_html__("Products not found!", 'woocommerce-products-filter'),
                "type" => "",
                "link" => "#",
                "icon" => WOOF_LINK . 'img/not-found.jpg'
            );
        }

        die(json_encode($results));
    }

}

WOOF_EXT::$includes['html_type_objects']['by_text'] = new WOOF_EXT_BY_TEXT();
