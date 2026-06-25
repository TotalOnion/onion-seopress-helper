<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://heavyweightagency.co.uk/
 * @since      1.1.0
 *
 * @package    Onion_Seopress_Helper
 * @subpackage Onion_Seopress_Helper/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
	<h1>
		<?php _e( 'SEOPress Helper Settings', 'PR SEOPress Helper' ); ?>
	</h1>
	<form method="post" action="options.php">
		<?php
			settings_fields( ONION_SEOPRESS_HELPER_NAME . '_options' );
			do_settings_sections( ONION_SEOPRESS_HELPER_SLUG . '-settings-page' );
			submit_button();
		?>
	</form>
</div>
