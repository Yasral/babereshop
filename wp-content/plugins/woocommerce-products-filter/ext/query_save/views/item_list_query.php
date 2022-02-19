<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');
?>
<li class="woof_query_save_item woof_query_save_item_<?php echo $key ?>">
    <?php
    if (!isset($title)) {
	$title = esc_html__('new', 'woocommerce-products-filter');
    }
    ?>
    <a class="woof_link_to_query_save"  href="<?php echo $link ?>" target="blank" ><?php echo $title ?></a>
    <p class="woof_tooltip"><span class="woof_tooltip_data"><?php echo $get ?></span>  <span class="woof_icon_save_query"></span></p>   
    <a href="#" class="woof_remove_query_save" data-user="<?php echo $user_id ?>" data-key="<?php echo $key ?>"><img src="<?php echo $this->settings['delete_image'] ?>" height="12" width="12" alt="" /></a>
    <?php

    if(is_product() AND $show_notice){

       global $product;
       $id = $product->get_id(); 
       if($id){
        ?>
        <div class="woof_query_save_notice woof_query_save_notice_<?php echo $key ?>" data-id="<?php echo $id ?>"  ></div>
        <?php
       }
    }
    ?>
</li>
