<?php
/**
 * Get Post List
 * return array
 */
function ever_compare_get_post_list( $post_type = 'page' ){
    $options = array();
    $options['0'] = __('Select','ever-compare');
    $perpage = -1;
    $all_post = array( 'posts_per_page' => $perpage, 'post_type'=> $post_type );
    $post_terms = get_posts( $all_post );
    if ( ! empty( $post_terms ) && ! is_wp_error( $post_terms ) ){
        foreach ( $post_terms as $term ) {
            $options[ $term->ID ] = $term->post_title;
        }
        return $options;
    }
}

/**
 * [ever_compare_locate_template]
 * @param  [string] $tmp_name Template name
 * @return [Template path]
 */
function ever_compare_locate_template( $tmp_name ) {

    $woo_tmp_base = function_exists('WC') ? WC()->template_path() : '';

    $woo_tmp_path     = $woo_tmp_base . $tmp_name; //active theme directory/woocommerce/
    $theme_tmp_path   = '/' . $tmp_name; //active theme root directory
    $plugin_tmp_path  = EVERCOMPARE_DIR . 'includes/templates/' . $tmp_name;

    $located = locate_template( [ $woo_tmp_path, $theme_tmp_path ] );

    if ( ! $located && file_exists( $plugin_tmp_path ) ) {
        return apply_filters( 'evercompare_locate_template', $plugin_tmp_path, $tmp_name );
    }

    return apply_filters( 'evercompare_locate_template', $located, $tmp_name );
}

/**
 * [ever_compare_get_template]
 * @param  [string]  $tmp_name Template name
 * @param  [array]  $args template argument array
 * @param  boolean $echo
 * @return [void]
 */
function ever_compare_get_template( $tmp_name = '', $args = null, $echo = true ) {
    $located = ever_compare_locate_template( $tmp_name );

    if ( $args && is_array( $args ) ) {
        extract( $args );
    }

    if ( $echo !== true ) { ob_start(); }

    // include file located.
    include( $located );

    if ( $echo !== true ) { return ob_get_clean(); }

}


/**
 * Get default fields List
 * return array
 */
function ever_compare_get_default_fields(){
    $fields = array(
        'title'         => esc_html__( 'Title', 'ever-compare' ),
        'ratting'       => esc_html__( 'Ratting', 'ever-compare' ),
        'price'         => esc_html__( 'Price', 'ever-compare' ),
        'add_to_cart'   => esc_html__( 'Add To Cart', 'ever-compare' ),
        'description'   => esc_html__( 'Description', 'ever-compare' ),
        'availability'  => esc_html__( 'Availability', 'ever-compare' ),
        'sku'           => esc_html__( 'Sku', 'ever-compare' ),
        'weight'        => esc_html__( 'Weight', 'ever-compare' ),
        'dimensions'    => esc_html__( 'Dimensions', 'ever-compare' ),
    );
    return apply_filters( 'ever_compare_default_fields', $fields );
}

/**
 * Get Fields List
 * return array
 */
function ever_compare_get_available_attributes() {
    $attribute_list = array();

    if( function_exists( 'wc_get_attribute_taxonomies' ) ) {
        $attribute_list = wc_get_attribute_taxonomies();
    }

    $fields = ever_compare_get_default_fields();

    if ( count( $attribute_list ) > 0 ) {
        foreach ( $attribute_list as $attribute ) {
            $fields[ 'pa_' . $attribute->attribute_name ] = $attribute->attribute_label;
        }
    }

    return $fields;
}

/**
 * [ever_compare_table_active_heading]
 * @return [array]
 */
function ever_compare_table_active_heading(){
    $active_heading = !empty( woolentor_get_option( 'show_fields', 'ever_compare_table_settings_tabs' ) ) ? woolentor_get_option( 'show_fields', 'ever_compare_table_settings_tabs' ) : array();
    return $active_heading;
}

/**
 * [ever_compare_table_heading]
 * @return [array]
 */
function ever_compare_table_heading(){
    $new_list = array();
    $field_list = count( ever_compare_table_active_heading() ) > 0 ? ever_compare_table_active_heading() : ever_compare_get_default_fields();
    foreach ( $field_list as $key => $value ) {
        $new_list[$key] = \EverCompare\Frontend\Manage_Compare::instance()->field_name( $key );
    }
    return $new_list;
}

/**
 * [ever_compare_dimensions]
 * @param  [string] $key
 * @param  [string] $tab
 * @return [String | Bool]
 */
function ever_compare_dimensions( $key, $tab, $css_attr ){
    $dimensions = !empty( woolentor_get_option( $key, $tab ) ) ? woolentor_get_option( $key, $tab ) : array();
    if( !empty( $dimensions['top'] ) || !empty( $dimensions['right'] ) || !empty( $dimensions['bottom'] ) || !empty( $dimensions['left'] ) ){

        $unit   = ( empty( $dimensions['unit'] ) ? 'px' : $dimensions['unit'] );
        $top    = ( !empty( $dimensions['top'] ) ? $dimensions['top'] : 0 );
        $right  = ( !empty( $dimensions['right'] ) ? $dimensions['right'] : 0 );
        $bottom = ( !empty( $dimensions['bottom'] ) ? $dimensions['bottom'] : 0 );
        $left   = ( !empty( $dimensions['left'] ) ? $dimensions['left'] : 0 );

        $css_attr .= ":{$top}{$unit} {$right}{$unit} {$bottom}{$unit} {$left}{$unit}";
        return $css_attr.';';

    }else{
        return false;
    }
}

/**
 * [ever_compare_generate_css]
 * @return [String | Bool]
 */
function ever_compare_generate_css( $key, $tab, $css_attr, $unit = '' ){
    $field_value = !empty( woolentor_get_option( $key, $tab ) ) ? woolentor_get_option( $key, $tab ) : '';

    if( !empty( $field_value ) ){
        $css_attr .= ":{$field_value}{$unit}";
        return $css_attr.';';
    }else{
        return false;
    }

}