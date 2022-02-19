"use strict";

jQuery(document).ready(function () {

    jQuery(".woof_add_seo_rule").on('click', function () {

        if (jQuery('.woof_seo_rules_item').length >= 2) {
            jQuery('.woof_add_seo_rule.woof-button').remove();
            return;
        }

        var url = jQuery('.woof_seo_rule_url_add').val();
        var lang = jQuery('.woof_seo_current_lang').val();

        var data = {
            action: "woof_get_seo_rule_html",
            url: url,
            lang: lang
        };

        jQuery.post(ajaxurl, data, function (section) {
            jQuery('#woof_seo_rules_list').append(section);
            jQuery('.woof_seo_rule_url_add').val("");
            woof_init_seo_rules_scripts();
        });

    });

    jQuery(".woof_seo_current_lang").on('change', function () {
        woof_seo_rules_check_lang();
    });

    woof_init_seo_rules_scripts();
    woof_seo_rules_check_lang();
});

function woof_seo_rules_check_lang() {
    var lang = jQuery('.woof_seo_current_lang').val();
    jQuery('.woof_seo_rules_item').hide();
    jQuery('.woof_seo_rules_item_' + lang).show();
}

function woof_init_seo_rules_scripts() {
    jQuery('.woof_seo_rules_delete').off('click');
    jQuery('.woof_seo_rules_delete').on('click', function () {
        var key = jQuery(this).data('key');
        jQuery("li[data-key='" + key + "']").remove();

        return false;
    });
}
