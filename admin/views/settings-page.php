<?php
/**
 * Settings page view.
 *
 * @package Apalpador
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap apalpador-settings">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<form method="post" action="options.php">
		<?php
		settings_fields( 'apalpador_settings' );
		do_settings_sections( 'apalpador' );
		submit_button();
		?>
	</form>
</div>
