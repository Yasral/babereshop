<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

global $WOOF;
if (isset($WOOF->settings['by_rating']) AND $WOOF->settings['by_rating']['show']) {
    $as_star=0;
    if (isset($WOOF->settings['by_rating']['use_star'])){
        $as_star=$WOOF->settings['by_rating']['use_star'];
    }
    ?>
    <div data-css-class="woof_by_rating_container" class="woof_by_rating_container woof_container">
        <div class="woof_container_overlay_item"></div>
        <?php
                $request = $WOOF->get_request_data();
                $selected = $WOOF->is_isset_in_request_data('min_rating') ? $request['min_rating'] : 0;        
        ?>
        <div class="woof_container_inner <?php echo ($as_star)?"woof_star_selected":"";  ?>">
            <select class="woof_by_rating_dropdown woof_select" name="min_rating">
                <?php
                $vals = array(
                    0 => esc_html__('Filter by rating', 'woocommerce-products-filter'),
                    4 => esc_html__('average rating between 4 to 5', 'woocommerce-products-filter'),
                    3 => esc_html__('average rating between 3 to 4-', 'woocommerce-products-filter'),
                    2 => esc_html__('average rating between 2 to 3-', 'woocommerce-products-filter'),
                    1 => esc_html__('average rating between 1 to 2-', 'woocommerce-products-filter')
                );
                if($as_star){
                    $vals = array(
                        0 => esc_html__('Filter by rating', 'woocommerce-products-filter'),
                        4 => esc_html__('SSSSS', 'woocommerce-products-filter'),
                        3 => esc_html__('SSSS', 'woocommerce-products-filter'),
                        2 => esc_html__('SSS', 'woocommerce-products-filter'),
                        1 => esc_html__('SS', 'woocommerce-products-filter')
                    );
                }               

                ?>
                <?php foreach ($vals as $key => $value): ?>
                    <option <?php echo selected($selected, $key); ?> <?php echo ($key!==0 AND $as_star)?"class='woof_star_font'":""; ?> value="<?php echo $key ?>"><?php echo $value ?></option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" value="<?php echo esc_html__('Min rating: ', 'woocommerce-products-filter'), $selected ?>" data-anchor="woof_n_<?php echo "min_rating" ?>_<?php echo $selected ?>" />
        </div>
    </div>
    <?php
}


