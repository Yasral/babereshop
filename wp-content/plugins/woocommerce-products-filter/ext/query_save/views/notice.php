<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

$class = ($match) ? "woof_notise_match" : "woof_notise_not_match";
?>
<div class="woof_notice_result <?php echo $class ?>">
    <span class="dashicons <?php echo($match) ? "dashicons-yes-alt" : "dashicons-warning" ?>"></span>
    <?php echo $notice ?>  
</div>
<?php
