<?php
/**
 * [wishsuite_get_post_list]
 * @param  string $post_type
 * @return [array]
 */
function wishsuite_get_post_list( $post_type = 'page' ){
    $options = array();
    $options['0'] = __('Select','wishsuite');
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
 * [wishsuite_locate_template]
 * @param  [string] $tmp_name Template name
 * @return [Template path]
 */
function wishsuite_locate_template( $tmp_name ) {
    $woo_tmp_base = WC()->template_path();

    $woo_tmp_path     = $woo_tmp_base . $tmp_name; //active theme directory/woocommerce/
    $theme_tmp_path   = '/' . $tmp_name; //active theme root directory
    $plugin_tmp_path  = WISHSUITE_DIR . 'includes/templates/' . $tmp_name;

    $located = locate_template( [ $woo_tmp_path, $theme_tmp_path ] );

    if ( ! $located && file_exists( $plugin_tmp_path ) ) {
        return apply_filters( 'wishsuite_locate_template', $plugin_tmp_path, $tmp_name );
    }

    return apply_filters( 'wishsuite_locate_template', $located, $tmp_name );
}

/**
 * [wishsuite_get_template]
 * @param  [string]  $tmp_name Template name
 * @param  [array]  $args template argument array
 * @param  boolean $echo
 * @return [void]
 */
function wishsuite_get_template( $tmp_name, $args = null, $echo = true ) {
    $located = wishsuite_locate_template( $tmp_name );

    if ( $args && is_array( $args ) ) {
        extract( $args );
    }

    if ( $echo !== true ) { ob_start(); }

    // include file located.
    include( $located );

    if ( $echo !== true ) { return ob_get_clean(); }

}

/**
 * [wishsuite_get_page_url]
 * @return [URL]
 */
function wishsuite_get_page_url() {
    $page_id = woolentor_get_option( 'wishlist_page', 'wishsuite_table_settings_tabs' );
    return get_permalink( $page_id );
}

/**
 * [wishsuite_add_to_cart]
 * @param  [object] $product
 * @return [HTML]
 */
function wishsuite_add_to_cart( $product, $quentity ){
    return \WishSuite\Frontend\Manage_Wishlist::instance()->add_to_cart_html( $product, $quentity );
}

/**
 * Get default fields List
 * return array
 */
function wishsuite_get_default_fields(){
    $fields = array(
        'remove'      => esc_html__( 'Remove', 'wishsuite' ),
        'image'       => esc_html__( 'Image', 'wishsuite' ),
        'title'       => esc_html__( 'Title', 'wishsuite' ),
        'price'       => esc_html__( 'Price', 'wishsuite' ),
        'quantity'    => esc_html__( 'Quantity', 'wishsuite' ),
        'add_to_cart' => esc_html__( 'Add To Cart', 'wishsuite' ),
        'description' => esc_html__( 'Description', 'wishsuite' ),
        'availability'=> esc_html__( 'Availability', 'wishsuite' ),
        'sku'         => esc_html__( 'Sku', 'wishsuite' ),
        'weight'      => esc_html__( 'Weight', 'wishsuite' ),
        'dimensions'  => esc_html__( 'Dimensions', 'wishsuite' ),
    );
    return apply_filters( 'wishsuite_default_fields', $fields );
}

/**
 * [wishsuite_table_active_heading]
 * @return [array]
 */
function wishsuite_table_active_heading(){
    $active_heading = !empty( woolentor_get_option( 'show_fields', 'wishsuite_table_settings_tabs' ) ) ? woolentor_get_option( 'show_fields', 'wishsuite_table_settings_tabs' ) : array();
    return $active_heading;
}

/**
 * [wishsuite_table_heading]
 * @return [array]
 */
function wishsuite_table_heading(){
    $new_list = array();

    $active_default_fields = array(
        'remove'      => esc_html__( 'Remove', 'wishsuite' ),
        'image'       => esc_html__( 'Image', 'wishsuite' ),
        'title'       => esc_html__( 'Title', 'wishsuite' ),
        'price'       => esc_html__( 'Price', 'wishsuite' ),
        'quantity'    => esc_html__( 'Quantity', 'wishsuite' ),
        'add_to_cart' => esc_html__( 'Add To Cart', 'wishsuite' ),
    );

    $field_list = count( wishsuite_table_active_heading() ) > 0 ? wishsuite_table_active_heading() : $active_default_fields;
    foreach ( $field_list as $key => $value ) {
        $new_list[$key] = \WishSuite\Frontend\Manage_Wishlist::instance()->field_name( $key );
    }
    return $new_list;
}

/**
 * Get Post List
 * return array
 */
function wishsuite_get_available_attributes() {
    $attribute_list = array();

    if( function_exists( 'wc_get_attribute_taxonomies' ) ) {
        $attribute_list = wc_get_attribute_taxonomies();
    }

    $fields = wishsuite_get_default_fields();

    if ( count( $attribute_list ) > 0 ) {
        foreach ( $attribute_list as $attribute ) {
            $fields[ 'pa_' . $attribute->attribute_name ] = $attribute->attribute_label;
        }
    }

    return $fields;
}


/**
 * [wishsuite_dimensions]
 * @param  [string] $key
 * @param  [string] $tab
 * @return [String | Bool]
 */
function wishsuite_dimensions( $key, $tab, $css_attr ){
    $dimensions = !empty( woolentor_get_option( $key, $tab ) ) ? woolentor_get_option( $key, $tab ) : array();
    if( !empty( $dimensions['top'] ) || !empty( $dimensions['right'] ) || !empty( $dimensions['bottom'] ) || !empty( $dimensions['left'] ) ){
        $unit = empty( $dimensions['unit'] ) ? 'px' : $dimensions['unit'];
        $css_attr .= ":{$dimensions['top']}{$unit} {$dimensions['right']}{$unit} {$dimensions['bottom']}{$unit} {$dimensions['left']}{$unit}";
        return $css_attr.';';
    }else{
        return false;
    }
}

/**
 * [wishsuite_generate_css]
 * @return [String | Bool]
 */
function wishsuite_generate_css( $key, $tab, $css_attr ){
    $field_value = !empty( woolentor_get_option( $key, $tab ) ) ? woolentor_get_option( $key, $tab ) : '';

    if( !empty( $field_value ) ){
        $css_attr .= ":{$field_value}";
        return $css_attr.';';
    }else{
        return false;
    }

}