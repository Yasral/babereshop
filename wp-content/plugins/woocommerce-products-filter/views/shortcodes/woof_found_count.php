<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

//***

global $wp_query;
$show = false;
if ($this->is_isset_in_request_data($this->get_swoof_search_slug())) {
    $show = true;
}
if (isset($this->settings['woof_turbo_mode']['enable']) AND $this->settings['woof_turbo_mode']['enable']) {
    $show = true;
}
?>
<?php if ($show): ?>
    <span class="woof_found_count"><?php echo esc_html(isset($_REQUEST['woof_wp_query_found_posts']) ? $_REQUEST['woof_wp_query_found_posts'] : $wp_query->found_posts) ?></span>
<?php else: ?>
	<span class="woof_found_count"></span>
<?php endif; ?>
