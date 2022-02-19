<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

switch ($type) {
    case 'tabs':
    case 'tabs_radio':
    default :
        $id = uniqid();
        ?>
        <input type="<?php echo ($type == 'tabs_radio') ? "radio" : "checkbox"; ?>" <?php echo $checked ? "checked='checked'" : ''; ?> name="woof_section_tabs" id="woof_tab_<?php echo $key . "_" . $id; ?>">
        <label class="woof_section_tab_label" for="woof_tab_<?php echo $key . "_" . $id; ?>" id="woof_<?php echo $key . "_" . $id; ?>_content"><?php echo $title ?><span>+</span></label>
        <div class="woof_section_tab" class="woof_<?php echo $key ?>_content"> 
        <?php
    }


