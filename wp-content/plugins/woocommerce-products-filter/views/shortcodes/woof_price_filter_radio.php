<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php
if (!isset($additional_taxes)) {
    $additional_taxes = '';
}
$price2_filter_data = WOOF_HELPER::get_price2_filter_data($additional_taxes);

$show_count = get_option('woof_show_count', 0);
$show_count_dynamic = get_option('woof_show_count_dynamic', 0);
$hide_dynamic_empty_pos = 0;
$hide_count_text = 0;
//***
if (isset($_REQUEST['hide_terms_count_txt_short']) AND $_REQUEST['hide_terms_count_txt_short'] != -1) {
    if ((int) $_REQUEST['hide_terms_count_txt_short'] == 1) {
        $hide_count_text = 0;
    } else {
        $hide_count_text = 0;
    }
}
//***
?>


<div data-css-class="woof_price_filter_radio_container" class="woof_checkbox_authors_container ">
    <div class="woof_container_overlay_item"></div>
    <div class="woof_container_inner">
        <ul class='woof_authors '>
            <?php
            if (!isset($price2_filter_data['ranges']['options']) OR!is_array($price2_filter_data['ranges']['options'])) {
                esc_html_e('Not possible. Enter options ranges in the plugin settings -> tab Structure -> Search by price -> additional options', 'woocommerce-products-filter');
            } else {
                foreach ($price2_filter_data['ranges']['options'] as $k => $value): $value = trim($value);
                    ?>
                    <?php
                    $c = 0;
                    $cs = '';
                    if ($show_count) {
                        $c = (int) $price2_filter_data['ranges']['count'][$k];
                        $cs = '(' . $c . ')';
                    }
                    if ($hide_count_text) {
                        $cs = '';
                    }

                    if ($show_count_dynamic AND $c == 0) {
                        if ($hide_dynamic_empty_pos) {
                            continue;
                        }
                    }
                    //***

                    $unique_id = uniqid('wr_');
                    ?>
                    <li class="woof_list">
                        <input type="radio" <?php if ($c == 0 AND $show_count): ?>disabled=""<?php endif; ?> class="woof_price_filter_radio"  <?php echo checked($price2_filter_data['selected'], $k); ?>  name="woof_price_radio" id="woof_price_radio_<?php echo $unique_id ?>" value="<?php echo $k ?>"  />
                        <span>&nbsp;&nbsp;</span><label for="woof_price_radio_<?php echo $unique_id ?>"><?php echo($value) ?> <?php echo $cs ?>  </label>
                        <a href="" data-tax="price" style="display: none;" class="woof_radio_price_reset <?php if ($price2_filter_data['selected'] == $k): ?> woof_radio_term_reset_visible <?php endif; ?> woof_radio_term_reset_<?php echo $k ?>"><img src="<?php echo $this->settings['delete_image'] ?>" height="12" width="12" alt="<?php esc_html_e("Delete", 'woocommerce-products-filter') ?>" /></a>
                    </li>
                    <?php
                endforeach;
            }
            ?>
        </ul>
    </div>
</div>


<?php

