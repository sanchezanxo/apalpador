<?php
/**
 * Frontend class for Apalpador plugin.
 *
 * Handles frontend display and asset loading.
 *
 * @package Apalpador
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Apalpador_Frontend
 *
 * Manages frontend functionality.
 */
class Apalpador_Frontend {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp', array( $this, 'init' ) );
	}

	/**
	 * Initialize frontend if conditions are met.
	 *
	 * @return void
	 */
	public function init() {
		if ( ! $this->should_display() ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );

		// Only render character if character_enabled is true.
		$options = Apalpador_Settings::get_options();
		if ( ! empty( $options['character_enabled'] ) ) {
			add_action( 'wp_footer', array( $this, 'render_apalpador' ) );
		}
	}

	/**
	 * Check if the Apalpador should be displayed.
	 *
	 * @return bool True if should display.
	 */
	private function should_display() {
		// Check if plugin is enabled.
		if ( ! Apalpador_Settings::is_enabled() ) {
			return false;
		}

		// Check date range.
		if ( ! Apalpador_Settings::is_within_date_range() ) {
			return false;
		}

		// Don't show on login page.
		if ( $this->is_login_page() ) {
			return false;
		}

		// Don't show on WooCommerce cart/checkout.
		if ( $this->is_woocommerce_excluded_page() ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if current page is the login page.
	 *
	 * @return bool True if login page.
	 */
	private function is_login_page() {
		return in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ), true );
	}

	/**
	 * Check if current page is WooCommerce cart or checkout.
	 *
	 * @return bool True if excluded WooCommerce page.
	 */
	private function is_woocommerce_excluded_page() {
		// Only check if WooCommerce is active.
		if ( ! class_exists( 'WooCommerce' ) ) {
			return false;
		}

		// Check cart and checkout pages.
		if ( function_exists( 'is_cart' ) && is_cart() ) {
			return true;
		}

		if ( function_exists( 'is_checkout' ) && is_checkout() ) {
			return true;
		}

		return false;
	}

	/**
	 * Enqueue frontend assets.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		wp_enqueue_style(
			'apalpador-frontend',
			APALPADOR_PLUGIN_URL . 'assets/css/frontend.css',
			array(),
			APALPADOR_VERSION
		);

		wp_enqueue_script(
			'apalpador-frontend',
			APALPADOR_PLUGIN_URL . 'assets/js/frontend.js',
			array(),
			APALPADOR_VERSION,
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);

		// Pass options to JavaScript.
		wp_localize_script(
			'apalpador-frontend',
			'apalpadorOptions',
			$this->get_frontend_options()
		);
	}

	/**
	 * Get options for frontend JavaScript.
	 *
	 * @return array Frontend options.
	 */
	private function get_frontend_options() {
		$options = Apalpador_Settings::get_options();

		return array(
			'animEntry'      => $options['anim_entry'] ?? 'slide',
			'animIdle'       => ! empty( $options['anim_idle'] ),
			'animClick'      => $options['anim_click'] ?? 'shake',
			'bubbleEnabled'  => ! empty( $options['bubble_enabled'] ),
			'bubbleText'     => $options['bubble_text'] ?? 'Bo Nadal!',
			'bubbleTrigger'  => $options['bubble_trigger'] ?? 'once',
			'bubbleSize'     => $options['bubble_size'] ?? 'medium',
			'snowEnabled'    => ! empty( $options['snow_enabled'] ),
			'snowDensity'    => $options['snow_density'] ?? 'medium',
			'starEnabled'    => ! empty( $options['star_enabled'] ),
			'starFrequency'  => absint( $options['star_frequency'] ?? 10 ),
		);
	}

	/**
	 * Render the Apalpador HTML.
	 *
	 * @return void
	 */
	public function render_apalpador() {
		$options   = Apalpador_Settings::get_options();
		$image_url = $this->get_image_url();

		if ( empty( $image_url ) ) {
			return;
		}

		$position    = $options['position'] ?? 'bottom-left';
		$size        = $this->get_size_value();
		$padding_h   = absint( $options['padding_h'] ?? 20 );
		$padding_v   = absint( $options['padding_v'] ?? 20 );
		$anim_entry  = $options['anim_entry'] ?? 'slide';
		$anim_idle   = ! empty( $options['anim_idle'] );
		$anim_click  = $options['anim_click'] ?? 'shake';

		// Build CSS classes.
		$classes = array( 'apalpador-character' );
		$classes[] = 'apalpador-position-' . $position;

		if ( 'none' !== $anim_entry ) {
			$classes[] = 'apalpador-entry-' . $anim_entry;
		}

		if ( $anim_idle ) {
			$classes[] = 'apalpador-idle';
		}

		// Build inline styles.
		$styles = array();
		$styles[] = sprintf( 'width: %dpx', $size );
		$styles[] = sprintf( 'height: %dpx', $size );

		if ( 'bottom-left' === $position ) {
			$styles[] = sprintf( 'left: %dpx', $padding_h );
		} else {
			$styles[] = sprintf( 'right: %dpx', $padding_h );
		}

		$styles[] = sprintf( 'bottom: %dpx', $padding_v );

		// Bubble options.
		$bubble_enabled = ! empty( $options['bubble_enabled'] );
		$bubble_text    = $options['bubble_text'] ?? 'Bo Nadal!';
		$bubble_size    = $options['bubble_size'] ?? 'medium';

		?>
		<div
			id="apalpador"
			class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
			style="<?php echo esc_attr( implode( '; ', $styles ) ); ?>"
			data-click-animation="<?php echo esc_attr( $anim_click ); ?>"
			role="img"
			aria-label="<?php esc_attr_e( 'Apalpador - Traditional Galician Christmas character', 'apalpador' ); ?>"
		>
			<?php if ( $bubble_enabled && ! empty( $bubble_text ) ) : ?>
				<div class="apalpador-bubble apalpador-bubble-<?php echo esc_attr( $bubble_size ); ?>" aria-live="polite">
					<?php echo esc_html( $bubble_text ); ?>
				</div>
			<?php endif; ?>
			<img
				src="<?php echo esc_url( $image_url ); ?>"
				alt="<?php esc_attr_e( 'Apalpador', 'apalpador' ); ?>"
				width="<?php echo esc_attr( $size ); ?>"
				height="<?php echo esc_attr( $size ); ?>"
			>
		</div>
		<?php
	}

	/**
	 * Get the Apalpador image URL.
	 *
	 * @return string Image URL.
	 */
	private function get_image_url() {
		$options      = Apalpador_Settings::get_options();
		$image_preset = $options['image_preset'] ?? 'default';
		$image_id     = absint( $options['image_id'] ?? 0 );

		// Use custom image if preset is 'custom' and image_id is set.
		if ( 'custom' === $image_preset && $image_id ) {
			$url = wp_get_attachment_image_url( $image_id, 'medium' );
			if ( $url ) {
				return $url;
			}
		}

		// Use preset image.
		$presets = Apalpador_Settings::get_preset_images();
		if ( isset( $presets[ $image_preset ] ) ) {
			return $presets[ $image_preset ];
		}

		// Fall back to default image.
		return APALPADOR_PLUGIN_URL . 'assets/images/apalpadores/apalpador-default.webp';
	}

	/**
	 * Get the size value in pixels.
	 *
	 * @return int Size in pixels.
	 */
	private function get_size_value() {
		$options = Apalpador_Settings::get_options();
		$size    = $options['size'] ?? 'medium';

		switch ( $size ) {
			case 'small':
				return 100;
			case 'large':
				return 200;
			case 'custom':
				return absint( $options['size_custom'] ?? 150 );
			case 'medium':
			default:
				return 150;
		}
	}
}
