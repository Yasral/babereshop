var woof_sku_do_submit = false;
function woof_init_sku() {
    jQuery('.woof_show_sku_search').keyup(function (e) {
        var val = jQuery(this).val();
        var uid = jQuery(this).data('uid');

        if (e.keyCode == 13 /*&& val.length > 0*/) {
            woof_sku_do_submit = true;
            woof_sku_direct_search('woof_sku', val);
            return true;
        }

        //save new word into woof_current_values
        if (woof_autosubmit) {
            woof_current_values['woof_sku'] = val;
        } else {
            woof_sku_direct_search('woof_sku', val);
        }


        //if (woof_is_mobile == 1) {
        if (val.length > 0) {
            jQuery('.woof_sku_search_go.' + uid).show(222);
        } else {
            jQuery('.woof_sku_search_go.' + uid).hide();
        }
        //}
    });
    //+++
    jQuery('body').on('click','.woof_sku_search_go', function () {
        var uid = jQuery(this).data('uid');
        woof_sku_do_submit = true;
        woof_sku_direct_search('woof_sku', jQuery('.woof_show_sku_search.' + uid).val());
    });
}

function woof_sku_direct_search(name, slug) {

    jQuery.each(woof_current_values, function (index, value) {
        if (index == name) {
            delete woof_current_values[name];
            return;
        }
    });

    if (slug != 0) {
        woof_current_values[name] = slug;
    }

    woof_ajax_page_num = 1;
    if (woof_autosubmit || woof_sku_do_submit) {
        woof_sku_do_submit = false;
        woof_submit_link(woof_get_submit_link());
    }
}


