<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

global $WOOF;
?>

<section id="tabs-url-request">
    <div class="woof-tabs woof-tabs-style-line">

        <?php global $wp_locale; ?>

        <div class="content-wrap">

            <section>

                <div class="woof-section-title">
                    <div class="col-title">

                        <h4><?php esc_html_e('SEO URL request', 'woocommerce-products-filter') ?></h4>

                    </div>
                    <div class="col-button">
                        <a href="https://products-filter.com/extencion/seo-url-request/" target="_blank" class="button-primary"><span class="icon-info"></span></a><br>
                    </div>
                </div>

                <div class="woof__alert woof__alert-info2" style="color: red !important;">
                    <?php esc_html_e('In FREE version it is possible to operate with 2 rules only!', 'woocommerce-products-filter') ?>
                </div>

                <div class="woof-control-section">

                    <h4><?php esc_html_e('Enable/Disable', 'woocommerce-products-filter') ?></h4>

                    <div class="woof-control-container">

                        <div class="woof-control">

                            <?php
                            $enable_url = array(
                                0 => esc_html__('No', 'woocommerce-products-filter'),
                                1 => esc_html__('Yes', 'woocommerce-products-filter')
                            );

                            if (!isset($woof_settings['woof_url_request']['enable'])) {
                                $woof_settings['woof_url_request']['enable'] = 0;
                            }
                            $enable = $woof_settings['woof_url_request']['enable'];
                            ?>

                            <div class="select-wrap">
                                <select name="woof_settings[woof_url_request][enable]" class="chosen_select">
                                    <?php foreach ($enable_url as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>" <?php if ($enable == $key): ?>selected="selected"<?php endif; ?>><?php echo $value; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="woof-description">
                            <p class="description"><?php esc_html_e('This option changes the search link. The search query becomes part of the URL.', 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>
                </div><!--/ .woof-control-section-->

                <div class="woof-control-section">

                    <h4><?php esc_html_e('Disable page indexing', 'woocommerce-products-filter') ?></h4>

                    <div class="woof-control-container">

                        <div class="woof-control">

                            <?php
                            $page_index = array(
                                0 => esc_html__('No', 'woocommerce-products-filter'),
                                1 => esc_html__('Yes', 'woocommerce-products-filter')
                            );

                            if (!isset($woof_settings['woof_url_request']['page_index'])) {
                                $woof_settings['woof_url_request']['page_index'] = 1;
                            }
                            $index = $woof_settings['woof_url_request']['page_index'];
                            ?>

                            <div class="select-wrap">
                                <select name="woof_settings[woof_url_request][page_index]" class="chosen_select">
                                    <?php foreach ($page_index as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>" <?php if ($index == $key): ?>selected="selected"<?php endif; ?>><?php echo $value; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="woof-description">
                            <p class="description"><?php esc_html_e('Disables page indexing when a seo search query exists.', 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>
                </div><!--/ .woof-control-section-->
                <div class="woof-control-section">

                    <h4><?php esc_html_e('Rules', 'woocommerce-products-filter') ?>:</h4>

                    <div class="woof-control-container woof-control-container-add-seo-rule">
                        <div class="woof-control">
                            <input type='text' class='woof_seo_rule_url_add' placeholder="<?php esc_html_e('Create your products page URL here', 'woocommerce-products-filter') ?>" value="">
                            <input type="button" class="woof_add_seo_rule woof-button" style="margin: 0;" value="<?php esc_html_e('Add SEO rule', 'woocommerce-products-filter') ?>">
                        </div>						
                        <div class="woof-description">
                            <p class="description"><?php esc_html_e('You can insert key {any} into the link. An example: /color-{any}/. For fields title and description - you can insert names of the terms that should be in the search query. For example insert the name of the current color: {pa_color}. To show taxonomy title use literal key {pa_color_title}. Example: Current season clothes of {pa_color_title} {pa_color}. Rule like "{pa_color_title} {pa_color}" will generate in the text: "Color red". Such rules can be set for each language with WPML plugin automatically,  for another plugins you can use hook woof_seo_rules_langs (read this extension documentation)', 'woocommerce-products-filter') ?></p>
                        </div>
                    </div>

                    <div class="woof-control-container woof-control-container-seo">

                        <div class="woof-control-seo">
                            <div class="woof_seo_rules_list_container">
                                <?php
                                $langs = $seo_rule->get_all_langs();
                                $add_class = 'woof_hide_options';
                                if (count($langs) > 1) {
                                    $add_class = '';
                                }
                                ?>

                                <div>
                                    <select class='woof_seo_current_lang <?php echo $add_class ?>'>
                                        <?php
                                        foreach ($langs as $lang) {
                                            ?>
                                            <option values='<?php echo $lang; ?>'><?php echo $lang; ?></option>
                                        <?php }
                                        ?>
                                    </select>
                                </div>

                                <ul id='woof_seo_rules_list' >
                                    <?php
                                    $seo_rules = array();
                                    foreach ($langs as $lang) {
                                        $seo_rules = array();
                                        if (isset($woof_settings['woof_url_request']['seo_rules'][$lang]) && is_array($woof_settings['woof_url_request']['seo_rules'][$lang])) {
                                            $seo_rules = $woof_settings['woof_url_request']['seo_rules'][$lang];
                                        }

                                        $counter = 0;
                                        foreach ($seo_rules as $key => $data) {
                                            if ($counter++ >= 2) {
                                                break;
                                            }
                                            $seo_rule->woof_draw_seo_rules_item($key, $lang, $data['url'], $data['title'], $data['description'], $data['h1']);
                                        }
                                    }
                                    ?>
                                </ul>

                            </div>
                        </div>

                    </div>



                </div><!--/ .woof-control-section-->				
            </section>

        </div>

    </div>
</section>
