<?php
/**
 * Help page view.
 *
 * @package Apalpador
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap apalpador-help">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<div class="apalpador-help-content">
		<h2><?php esc_html_e( 'About the Apalpador', 'apalpador' ); ?></h2>
		<p><?php esc_html_e( 'The Apalpador is a traditional Galician Christmas figure. He is a charcoal maker who comes down from the mountains on the night of December 31st to check if children have eaten well during the year by touching their bellies. If they have eaten well, he leaves them chestnuts as a gift.', 'apalpador' ); ?></p>

		<h2><?php esc_html_e( 'How to Use', 'apalpador' ); ?></h2>
		<ol>
			<li><?php esc_html_e( 'Go to Settings to configure the plugin.', 'apalpador' ); ?></li>
			<li><?php esc_html_e( 'Enable the plugin and set your preferred date range.', 'apalpador' ); ?></li>
			<li><?php esc_html_e( 'Choose an image for the Apalpador or use the default.', 'apalpador' ); ?></li>
			<li><?php esc_html_e( 'Customize position, size, and animations as desired.', 'apalpador' ); ?></li>
			<li><?php esc_html_e( 'Enable snow and shooting star effects for extra festivity.', 'apalpador' ); ?></li>
		</ol>

		<h2><?php esc_html_e( 'Settings Overview', 'apalpador' ); ?></h2>

		<h3><?php esc_html_e( 'General', 'apalpador' ); ?></h3>
		<ul>
			<li><strong><?php esc_html_e( 'Enable Plugin:', 'apalpador' ); ?></strong> <?php esc_html_e( 'Turn the Apalpador display on or off.', 'apalpador' ); ?></li>
			<li><strong><?php esc_html_e( 'Date Range:', 'apalpador' ); ?></strong> <?php esc_html_e( 'Set when the Apalpador should appear. Default is December 1st to January 6th.', 'apalpador' ); ?></li>
		</ul>

		<h3><?php esc_html_e( 'Apalpador', 'apalpador' ); ?></h3>
		<ul>
			<li><strong><?php esc_html_e( 'Image:', 'apalpador' ); ?></strong> <?php esc_html_e( 'Select a custom image from your media library.', 'apalpador' ); ?></li>
			<li><strong><?php esc_html_e( 'Position:', 'apalpador' ); ?></strong> <?php esc_html_e( 'Choose bottom-left or bottom-right corner.', 'apalpador' ); ?></li>
			<li><strong><?php esc_html_e( 'Size:', 'apalpador' ); ?></strong> <?php esc_html_e( 'Small (100px), Medium (150px), Large (200px), or Custom.', 'apalpador' ); ?></li>
			<li><strong><?php esc_html_e( 'Padding:', 'apalpador' ); ?></strong> <?php esc_html_e( 'Distance from the edges of the screen.', 'apalpador' ); ?></li>
		</ul>

		<h3><?php esc_html_e( 'Animations', 'apalpador' ); ?></h3>
		<ul>
			<li><strong><?php esc_html_e( 'Entry Animation:', 'apalpador' ); ?></strong> <?php esc_html_e( 'How the Apalpador appears when the page loads.', 'apalpador' ); ?></li>
			<li><strong><?php esc_html_e( 'Idle Animation:', 'apalpador' ); ?></strong> <?php esc_html_e( 'Subtle breathing movement while idle.', 'apalpador' ); ?></li>
			<li><strong><?php esc_html_e( 'Click Animation:', 'apalpador' ); ?></strong> <?php esc_html_e( 'Animation when visitors click on the Apalpador.', 'apalpador' ); ?></li>
		</ul>

		<h3><?php esc_html_e( 'Effects', 'apalpador' ); ?></h3>
		<ul>
			<li><strong><?php esc_html_e( 'Snow Effect:', 'apalpador' ); ?></strong> <?php esc_html_e( 'Falling snowflakes across the screen.', 'apalpador' ); ?></li>
			<li><strong><?php esc_html_e( 'Shooting Star:', 'apalpador' ); ?></strong> <?php esc_html_e( 'Occasional shooting stars crossing the sky.', 'apalpador' ); ?></li>
		</ul>

		<h2><?php esc_html_e( 'Accessibility', 'apalpador' ); ?></h2>
		<p><?php esc_html_e( 'All animations respect the user\'s "prefers-reduced-motion" setting. If a visitor has enabled reduced motion in their system settings, animations will be disabled automatically.', 'apalpador' ); ?></p>

		<h2><?php esc_html_e( 'Support', 'apalpador' ); ?></h2>
		<p>
			<?php
			printf(
				/* translators: %s: GitHub repository URL */
				esc_html__( 'For support, feature requests, or bug reports, please visit the %s.', 'apalpador' ),
				'<a href="https://github.com/sanchezanxo/apalpador" target="_blank" rel="noopener noreferrer">' . esc_html__( 'GitHub repository', 'apalpador' ) . '</a>'
			);
			?>
		</p>
	</div>
</div>
