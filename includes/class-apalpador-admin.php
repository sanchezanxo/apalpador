<?php
/**
 * Admin class for Apalpador plugin.
 *
 * Handles admin menu, settings page, and options registration.
 *
 * @package Apalpador
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Apalpador_Admin
 *
 * Manages admin functionality.
 */
class Apalpador_Admin {

	/**
	 * Option name in database.
	 *
	 * @var string
	 */
	private $option_name = 'apalpador_options';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
	}

	/**
	 * Add admin menu and submenus.
	 *
	 * @return void
	 */
	public function add_admin_menu() {
		// Main menu.
		add_menu_page(
			__( 'Apalpador Settings', 'apalpador' ),
			__( 'Apalpador', 'apalpador' ),
			'manage_options',
			'apalpador',
			array( $this, 'render_settings_page' ),
			'dashicons-star-filled',
			100
		);

		// Settings submenu (same as main).
		add_submenu_page(
			'apalpador',
			__( 'Apalpador Settings', 'apalpador' ),
			__( 'Settings', 'apalpador' ),
			'manage_options',
			'apalpador',
			array( $this, 'render_settings_page' )
		);

		// Help submenu.
		add_submenu_page(
			'apalpador',
			__( 'Apalpador Help', 'apalpador' ),
			__( 'Help', 'apalpador' ),
			'manage_options',
			'apalpador-help',
			array( $this, 'render_help_page' )
		);
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @param string $hook Current admin page hook.
	 * @return void
	 */
	public function enqueue_admin_assets( $hook ) {
		// Only load on our plugin pages.
		if ( 'toplevel_page_apalpador' !== $hook && 'apalpador_page_apalpador-help' !== $hook ) {
			return;
		}

		// Enqueue media library for image selector.
		if ( 'toplevel_page_apalpador' === $hook ) {
			wp_enqueue_media();
		}

		wp_enqueue_style(
			'apalpador-admin',
			APALPADOR_PLUGIN_URL . 'assets/css/admin.css',
			array(),
			APALPADOR_VERSION
		);

		wp_enqueue_script(
			'apalpador-admin',
			APALPADOR_PLUGIN_URL . 'assets/js/admin.js',
			array( 'jquery' ),
			APALPADOR_VERSION,
			true
		);

		wp_localize_script(
			'apalpador-admin',
			'apalpadorAdmin',
			array(
				'mediaTitle'  => __( 'Select Apalpador Image', 'apalpador' ),
				'mediaButton' => __( 'Use this image', 'apalpador' ),
			)
		);
	}

	/**
	 * Register plugin settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			'apalpador_settings',
			$this->option_name,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_options' ),
				'default'           => Apalpador_Settings::get_defaults(),
			)
		);

		// Section: General.
		add_settings_section(
			'apalpador_general',
			__( 'General', 'apalpador' ),
			array( $this, 'render_section_general' ),
			'apalpador'
		);

		add_settings_field(
			'enabled',
			__( 'Enable Plugin', 'apalpador' ),
			array( $this, 'render_field_enabled' ),
			'apalpador',
			'apalpador_general'
		);

		add_settings_field(
			'character_enabled',
			__( 'Show Character', 'apalpador' ),
			array( $this, 'render_field_character_enabled' ),
			'apalpador',
			'apalpador_general'
		);

		add_settings_field(
			'date_range',
			__( 'Date Range', 'apalpador' ),
			array( $this, 'render_field_date_range' ),
			'apalpador',
			'apalpador_general'
		);

		// Section: Apalpador.
		add_settings_section(
			'apalpador_appearance',
			__( 'Apalpador', 'apalpador' ),
			array( $this, 'render_section_appearance' ),
			'apalpador'
		);

		add_settings_field(
			'image',
			__( 'Image', 'apalpador' ),
			array( $this, 'render_field_image' ),
			'apalpador',
			'apalpador_appearance'
		);

		add_settings_field(
			'position',
			__( 'Position', 'apalpador' ),
			array( $this, 'render_field_position' ),
			'apalpador',
			'apalpador_appearance'
		);

		add_settings_field(
			'size',
			__( 'Size', 'apalpador' ),
			array( $this, 'render_field_size' ),
			'apalpador',
			'apalpador_appearance'
		);

		add_settings_field(
			'padding',
			__( 'Padding', 'apalpador' ),
			array( $this, 'render_field_padding' ),
			'apalpador',
			'apalpador_appearance'
		);

		// Section: Animations.
		add_settings_section(
			'apalpador_animations',
			__( 'Animations', 'apalpador' ),
			array( $this, 'render_section_animations' ),
			'apalpador'
		);

		add_settings_field(
			'anim_entry',
			__( 'Entry Animation', 'apalpador' ),
			array( $this, 'render_field_anim_entry' ),
			'apalpador',
			'apalpador_animations'
		);

		add_settings_field(
			'anim_idle',
			__( 'Idle Animation', 'apalpador' ),
			array( $this, 'render_field_anim_idle' ),
			'apalpador',
			'apalpador_animations'
		);

		add_settings_field(
			'anim_click',
			__( 'Click Animation', 'apalpador' ),
			array( $this, 'render_field_anim_click' ),
			'apalpador',
			'apalpador_animations'
		);

		add_settings_field(
			'bubble',
			__( 'Speech Bubble', 'apalpador' ),
			array( $this, 'render_field_bubble' ),
			'apalpador',
			'apalpador_animations'
		);

		// Section: Effects.
		add_settings_section(
			'apalpador_effects',
			__( 'Effects', 'apalpador' ),
			array( $this, 'render_section_effects' ),
			'apalpador'
		);

		add_settings_field(
			'snow',
			__( 'Snow Effect', 'apalpador' ),
			array( $this, 'render_field_snow' ),
			'apalpador',
			'apalpador_effects'
		);

		add_settings_field(
			'star',
			__( 'Shooting Star', 'apalpador' ),
			array( $this, 'render_field_star' ),
			'apalpador',
			'apalpador_effects'
		);
	}

	/**
	 * Sanitize options before saving.
	 *
	 * @param array $input Raw input data.
	 * @return array Sanitized data.
	 */
	public function sanitize_options( $input ) {
		$sanitized = array();
		$defaults  = Apalpador_Settings::get_defaults();

		// General.
		$sanitized['enabled']           = ! empty( $input['enabled'] );
		$sanitized['character_enabled'] = ! empty( $input['character_enabled'] );
		$sanitized['date_start']        = $this->sanitize_date( $input['date_start'] ?? $defaults['date_start'] );
		$sanitized['date_end']          = $this->sanitize_date( $input['date_end'] ?? $defaults['date_end'] );

		// Apalpador.
		$presets = Apalpador_Settings::get_preset_images();
		$sanitized['image_preset'] = isset( $input['image_preset'] ) && ( 'custom' === $input['image_preset'] || array_key_exists( $input['image_preset'], $presets ) )
			? sanitize_text_field( $input['image_preset'] )
			: $defaults['image_preset'];
		$sanitized['image_id']    = absint( $input['image_id'] ?? 0 );
		$sanitized['position']    = in_array( $input['position'] ?? '', array( 'bottom-left', 'bottom-right' ), true )
			? $input['position']
			: $defaults['position'];
		$sanitized['size']        = in_array( $input['size'] ?? '', array( 'small', 'medium', 'large', 'custom' ), true )
			? $input['size']
			: $defaults['size'];
		$sanitized['size_custom'] = absint( $input['size_custom'] ?? $defaults['size_custom'] );
		$sanitized['size_custom'] = max( 50, min( 500, $sanitized['size_custom'] ) ); // Clamp 50-500.
		$sanitized['size_mobile']        = in_array( $input['size_mobile'] ?? '', array( 'small', 'medium', 'large', 'custom' ), true )
			? $input['size_mobile']
			: $defaults['size_mobile'];
		$sanitized['size_custom_mobile'] = absint( $input['size_custom_mobile'] ?? $defaults['size_custom_mobile'] );
		$sanitized['size_custom_mobile'] = max( 50, min( 500, $sanitized['size_custom_mobile'] ) ); // Clamp 50-500.
		$sanitized['padding_h']   = absint( $input['padding_h'] ?? $defaults['padding_h'] );
		$sanitized['padding_h']   = min( 200, $sanitized['padding_h'] ); // Max 200.
		$sanitized['padding_v']   = absint( $input['padding_v'] ?? $defaults['padding_v'] );
		$sanitized['padding_v']   = min( 200, $sanitized['padding_v'] ); // Max 200.

		// Animations.
		$sanitized['anim_entry'] = in_array( $input['anim_entry'] ?? '', array( 'none', 'slide', 'fade', 'bounce', 'rotate' ), true )
			? $input['anim_entry']
			: $defaults['anim_entry'];
		$sanitized['anim_idle']  = ! empty( $input['anim_idle'] );
		$sanitized['anim_click'] = in_array( $input['anim_click'] ?? '', array( 'none', 'shake', 'bounce', 'spin', 'pulse' ), true )
			? $input['anim_click']
			: $defaults['anim_click'];

		// Speech bubble.
		$sanitized['bubble_enabled'] = ! empty( $input['bubble_enabled'] );
		$sanitized['bubble_text']    = sanitize_text_field( $input['bubble_text'] ?? $defaults['bubble_text'] );
		$sanitized['bubble_text']    = mb_substr( $sanitized['bubble_text'], 0, 100 ); // Max 100 chars.
		$sanitized['bubble_trigger'] = in_array( $input['bubble_trigger'] ?? '', array( 'once', 'click', 'hover' ), true )
			? $input['bubble_trigger']
			: $defaults['bubble_trigger'];
		$sanitized['bubble_size']    = in_array( $input['bubble_size'] ?? '', array( 'small', 'medium', 'large' ), true )
			? $input['bubble_size']
			: $defaults['bubble_size'];

		// Effects.
		$sanitized['snow_enabled']   = ! empty( $input['snow_enabled'] );
		$sanitized['snow_density']   = in_array( $input['snow_density'] ?? '', array( 'low', 'medium', 'high' ), true )
			? $input['snow_density']
			: $defaults['snow_density'];
		$sanitized['star_enabled']   = ! empty( $input['star_enabled'] );
		$sanitized['star_frequency'] = absint( $input['star_frequency'] ?? $defaults['star_frequency'] );
		$sanitized['star_frequency'] = max( 5, min( 60, $sanitized['star_frequency'] ) ); // Clamp 5-60.
		$sanitized['star_color']     = sanitize_hex_color( $input['star_color'] ?? $defaults['star_color'] ) ?: $defaults['star_color'];

		return $sanitized;
	}

	/**
	 * Sanitize date in MM-DD format.
	 *
	 * @param string $date Date string.
	 * @return string Sanitized date.
	 */
	private function sanitize_date( $date ) {
		if ( preg_match( '/^(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/', $date ) ) {
			return $date;
		}
		return '12-01';
	}

	/**
	 * Render settings page.
	 *
	 * @return void
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		include APALPADOR_PLUGIN_DIR . 'admin/views/settings-page.php';
	}

	/**
	 * Render help page.
	 *
	 * @return void
	 */
	public function render_help_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		include APALPADOR_PLUGIN_DIR . 'admin/views/help-page.php';
	}

	/**
	 * Render General section description.
	 *
	 * @return void
	 */
	public function render_section_general() {
		echo '<p>' . esc_html__( 'Configure when the Apalpador should appear on your site.', 'apalpador' ) . '</p>';
	}

	/**
	 * Render Appearance section description.
	 *
	 * @return void
	 */
	public function render_section_appearance() {
		echo '<p>' . esc_html__( 'Customize how the Apalpador looks and where it appears.', 'apalpador' ) . '</p>';
	}

	/**
	 * Render Animations section description.
	 *
	 * @return void
	 */
	public function render_section_animations() {
		echo '<p>' . esc_html__( 'Configure the Apalpador animations.', 'apalpador' ) . '</p>';
	}

	/**
	 * Render Effects section description.
	 *
	 * @return void
	 */
	public function render_section_effects() {
		echo '<p>' . esc_html__( 'Add festive visual effects to your site.', 'apalpador' ) . '</p>';
	}

	/**
	 * Render enabled field.
	 *
	 * @return void
	 */
	public function render_field_enabled() {
		$options = Apalpador_Settings::get_options();
		$enabled = $options['enabled'] ?? true;
		?>
		<label>
			<input type="checkbox" name="<?php echo esc_attr( $this->option_name ); ?>[enabled]" value="1" <?php checked( $enabled ); ?>>
			<?php esc_html_e( 'Enable the plugin on the frontend', 'apalpador' ); ?>
		</label>
		<p class="description"><?php esc_html_e( 'Master switch to enable/disable all plugin features.', 'apalpador' ); ?></p>
		<?php
	}

	/**
	 * Render character enabled field.
	 *
	 * @return void
	 */
	public function render_field_character_enabled() {
		$options           = Apalpador_Settings::get_options();
		$character_enabled = $options['character_enabled'] ?? true;
		?>
		<label>
			<input type="checkbox" name="<?php echo esc_attr( $this->option_name ); ?>[character_enabled]" value="1" <?php checked( $character_enabled ); ?>>
			<?php esc_html_e( 'Show the Apalpador character', 'apalpador' ); ?>
		</label>
		<p class="description"><?php esc_html_e( 'Disable to show only snow and shooting star effects without the character.', 'apalpador' ); ?></p>
		<?php
	}

	/**
	 * Render date range fields.
	 *
	 * @return void
	 */
	public function render_field_date_range() {
		$options    = Apalpador_Settings::get_options();
		$date_start = $options['date_start'] ?? '12-01';
		$date_end   = $options['date_end'] ?? '01-06';
		?>
		<label>
			<?php esc_html_e( 'From:', 'apalpador' ); ?>
			<input type="text" name="<?php echo esc_attr( $this->option_name ); ?>[date_start]" value="<?php echo esc_attr( $date_start ); ?>" class="apalpador-date-input" placeholder="MM-DD" pattern="(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])">
		</label>
		<label>
			<?php esc_html_e( 'To:', 'apalpador' ); ?>
			<input type="text" name="<?php echo esc_attr( $this->option_name ); ?>[date_end]" value="<?php echo esc_attr( $date_end ); ?>" class="apalpador-date-input" placeholder="MM-DD" pattern="(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])">
		</label>
		<p class="description"><?php esc_html_e( 'Format: MM-DD (e.g., 12-01 for December 1st)', 'apalpador' ); ?></p>
		<?php
	}

	/**
	 * Render image selector field with gallery.
	 *
	 * @return void
	 */
	public function render_field_image() {
		$options      = Apalpador_Settings::get_options();
		$image_preset = $options['image_preset'] ?? 'default';
		$image_id     = $options['image_id'] ?? 0;
		$presets      = Apalpador_Settings::get_preset_images();

		// Get custom image URL if set.
		$custom_image_url = '';
		if ( $image_id ) {
			$custom_image_url = wp_get_attachment_image_url( $image_id, 'medium' );
		}

		// Determine current preview URL.
		$current_preview_url = '';
		if ( 'custom' === $image_preset && $custom_image_url ) {
			$current_preview_url = $custom_image_url;
		} elseif ( isset( $presets[ $image_preset ] ) ) {
			$current_preview_url = $presets[ $image_preset ];
		} elseif ( isset( $presets['default'] ) ) {
			$current_preview_url = $presets['default'];
		}
		?>
		<div class="apalpador-image-selector">
			<!-- Preset Gallery -->
			<div class="apalpador-preset-gallery">
				<p class="description" style="margin-bottom: 10px;"><?php esc_html_e( 'Select a preset image:', 'apalpador' ); ?></p>
				<div class="apalpador-preset-grid">
					<?php foreach ( $presets as $slug => $url ) : ?>
						<label class="apalpador-preset-item <?php echo $image_preset === $slug ? 'selected' : ''; ?>">
							<input
								type="radio"
								name="<?php echo esc_attr( $this->option_name ); ?>[image_preset]"
								value="<?php echo esc_attr( $slug ); ?>"
								<?php checked( $image_preset, $slug ); ?>
								class="apalpador-preset-radio"
							>
							<img src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( $slug ); ?>">
							<span class="apalpador-preset-name"><?php echo esc_html( ucfirst( str_replace( '-', ' ', $slug ) ) ); ?></span>
						</label>
					<?php endforeach; ?>

					<!-- Custom option -->
					<label class="apalpador-preset-item apalpador-preset-custom <?php echo 'custom' === $image_preset ? 'selected' : ''; ?>">
						<input
							type="radio"
							name="<?php echo esc_attr( $this->option_name ); ?>[image_preset]"
							value="custom"
							<?php checked( $image_preset, 'custom' ); ?>
							class="apalpador-preset-radio"
						>
						<span class="apalpador-custom-placeholder <?php echo $custom_image_url ? 'has-image' : ''; ?>">
							<?php if ( $custom_image_url ) : ?>
								<img src="<?php echo esc_url( $custom_image_url ); ?>" alt="">
							<?php else : ?>
								<span class="dashicons dashicons-plus-alt2"></span>
							<?php endif; ?>
						</span>
						<span class="apalpador-preset-name"><?php esc_html_e( 'Custom', 'apalpador' ); ?></span>
					</label>
				</div>
			</div>

			<!-- Custom Image Upload (shown when custom is selected) -->
			<div class="apalpador-custom-upload" <?php echo 'custom' === $image_preset ? '' : 'style="display:none;"'; ?>>
				<input type="hidden" name="<?php echo esc_attr( $this->option_name ); ?>[image_id]" value="<?php echo esc_attr( $image_id ); ?>" class="apalpador-image-id">
				<button type="button" class="button apalpador-select-image"><?php esc_html_e( 'Select from Media Library', 'apalpador' ); ?></button>
				<button type="button" class="button apalpador-remove-image" <?php echo $image_id ? '' : 'style="display:none;"'; ?>><?php esc_html_e( 'Remove', 'apalpador' ); ?></button>
			</div>

			<!-- Live Preview -->
			<div class="apalpador-live-preview">
				<p class="description"><?php esc_html_e( 'Preview:', 'apalpador' ); ?></p>
				<div class="apalpador-preview-box">
					<img src="<?php echo esc_url( $current_preview_url ); ?>" alt="<?php esc_attr_e( 'Preview', 'apalpador' ); ?>" class="apalpador-preview-img">
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render position field.
	 *
	 * @return void
	 */
	public function render_field_position() {
		$options  = Apalpador_Settings::get_options();
		$position = $options['position'] ?? 'bottom-left';
		?>
		<select name="<?php echo esc_attr( $this->option_name ); ?>[position]">
			<option value="bottom-left" <?php selected( $position, 'bottom-left' ); ?>><?php esc_html_e( 'Bottom Left', 'apalpador' ); ?></option>
			<option value="bottom-right" <?php selected( $position, 'bottom-right' ); ?>><?php esc_html_e( 'Bottom Right', 'apalpador' ); ?></option>
		</select>
		<?php
	}

	/**
	 * Render size field.
	 *
	 * @return void
	 */
	public function render_field_size() {
		$options            = Apalpador_Settings::get_options();
		$size               = $options['size'] ?? 'medium';
		$size_custom        = $options['size_custom'] ?? 150;
		$size_mobile        = $options['size_mobile'] ?? 'small';
		$size_custom_mobile = $options['size_custom_mobile'] ?? 100;
		?>
		<p><strong><?php esc_html_e( 'Desktop:', 'apalpador' ); ?></strong></p>
		<select name="<?php echo esc_attr( $this->option_name ); ?>[size]" class="apalpador-size-select">
			<option value="small" <?php selected( $size, 'small' ); ?>><?php esc_html_e( 'Small (100px)', 'apalpador' ); ?></option>
			<option value="medium" <?php selected( $size, 'medium' ); ?>><?php esc_html_e( 'Medium (150px)', 'apalpador' ); ?></option>
			<option value="large" <?php selected( $size, 'large' ); ?>><?php esc_html_e( 'Large (200px)', 'apalpador' ); ?></option>
			<option value="custom" <?php selected( $size, 'custom' ); ?>><?php esc_html_e( 'Custom', 'apalpador' ); ?></option>
		</select>
		<span class="apalpador-custom-size" <?php echo 'custom' === $size ? '' : 'style="display:none;"'; ?>>
			<input type="number" name="<?php echo esc_attr( $this->option_name ); ?>[size_custom]" value="<?php echo esc_attr( $size_custom ); ?>" min="50" max="500" step="10"> px
		</span>
		<br><br>
		<p><strong><?php esc_html_e( 'Mobile:', 'apalpador' ); ?></strong></p>
		<select name="<?php echo esc_attr( $this->option_name ); ?>[size_mobile]" class="apalpador-size-mobile-select">
			<option value="small" <?php selected( $size_mobile, 'small' ); ?>><?php esc_html_e( 'Small (100px)', 'apalpador' ); ?></option>
			<option value="medium" <?php selected( $size_mobile, 'medium' ); ?>><?php esc_html_e( 'Medium (150px)', 'apalpador' ); ?></option>
			<option value="large" <?php selected( $size_mobile, 'large' ); ?>><?php esc_html_e( 'Large (200px)', 'apalpador' ); ?></option>
			<option value="custom" <?php selected( $size_mobile, 'custom' ); ?>><?php esc_html_e( 'Custom', 'apalpador' ); ?></option>
		</select>
		<span class="apalpador-custom-size-mobile" <?php echo 'custom' === $size_mobile ? '' : 'style="display:none;"'; ?>>
			<input type="number" name="<?php echo esc_attr( $this->option_name ); ?>[size_custom_mobile]" value="<?php echo esc_attr( $size_custom_mobile ); ?>" min="50" max="500" step="10"> px
		</span>
		<?php
	}

	/**
	 * Render padding fields.
	 *
	 * @return void
	 */
	public function render_field_padding() {
		$options   = Apalpador_Settings::get_options();
		$padding_h = $options['padding_h'] ?? 20;
		$padding_v = $options['padding_v'] ?? 20;
		?>
		<label>
			<?php esc_html_e( 'Horizontal:', 'apalpador' ); ?>
			<input type="number" name="<?php echo esc_attr( $this->option_name ); ?>[padding_h]" value="<?php echo esc_attr( $padding_h ); ?>" min="0" max="200" step="5"> px
		</label>
		<br>
		<label>
			<?php esc_html_e( 'Vertical:', 'apalpador' ); ?>
			<input type="number" name="<?php echo esc_attr( $this->option_name ); ?>[padding_v]" value="<?php echo esc_attr( $padding_v ); ?>" min="0" max="200" step="5"> px
		</label>
		<?php
	}

	/**
	 * Render entry animation field.
	 *
	 * @return void
	 */
	public function render_field_anim_entry() {
		$options    = Apalpador_Settings::get_options();
		$anim_entry = $options['anim_entry'] ?? 'slide';
		?>
		<select name="<?php echo esc_attr( $this->option_name ); ?>[anim_entry]">
			<option value="none" <?php selected( $anim_entry, 'none' ); ?>><?php esc_html_e( 'None', 'apalpador' ); ?></option>
			<option value="slide" <?php selected( $anim_entry, 'slide' ); ?>><?php esc_html_e( 'Slide', 'apalpador' ); ?></option>
			<option value="fade" <?php selected( $anim_entry, 'fade' ); ?>><?php esc_html_e( 'Fade', 'apalpador' ); ?></option>
			<option value="bounce" <?php selected( $anim_entry, 'bounce' ); ?>><?php esc_html_e( 'Bounce', 'apalpador' ); ?></option>
			<option value="rotate" <?php selected( $anim_entry, 'rotate' ); ?>><?php esc_html_e( 'Rotate', 'apalpador' ); ?></option>
		</select>
		<?php
	}

	/**
	 * Render idle animation field.
	 *
	 * @return void
	 */
	public function render_field_anim_idle() {
		$options   = Apalpador_Settings::get_options();
		$anim_idle = $options['anim_idle'] ?? true;
		?>
		<label>
			<input type="checkbox" name="<?php echo esc_attr( $this->option_name ); ?>[anim_idle]" value="1" <?php checked( $anim_idle ); ?>>
			<?php esc_html_e( 'Enable subtle breathing animation', 'apalpador' ); ?>
		</label>
		<?php
	}

	/**
	 * Render click animation field.
	 *
	 * @return void
	 */
	public function render_field_anim_click() {
		$options    = Apalpador_Settings::get_options();
		$anim_click = $options['anim_click'] ?? 'shake';
		?>
		<select name="<?php echo esc_attr( $this->option_name ); ?>[anim_click]">
			<option value="none" <?php selected( $anim_click, 'none' ); ?>><?php esc_html_e( 'None', 'apalpador' ); ?></option>
			<option value="shake" <?php selected( $anim_click, 'shake' ); ?>><?php esc_html_e( 'Shake', 'apalpador' ); ?></option>
			<option value="bounce" <?php selected( $anim_click, 'bounce' ); ?>><?php esc_html_e( 'Bounce', 'apalpador' ); ?></option>
			<option value="spin" <?php selected( $anim_click, 'spin' ); ?>><?php esc_html_e( 'Spin', 'apalpador' ); ?></option>
			<option value="pulse" <?php selected( $anim_click, 'pulse' ); ?>><?php esc_html_e( 'Pulse', 'apalpador' ); ?></option>
		</select>
		<?php
	}

	/**
	 * Render speech bubble fields.
	 *
	 * @return void
	 */
	public function render_field_bubble() {
		$options        = Apalpador_Settings::get_options();
		$bubble_enabled = $options['bubble_enabled'] ?? false;
		$bubble_text    = $options['bubble_text'] ?? 'Bo Nadal!';
		$bubble_trigger = $options['bubble_trigger'] ?? 'once';
		$bubble_size    = $options['bubble_size'] ?? 'medium';
		?>
		<label>
			<input type="checkbox" name="<?php echo esc_attr( $this->option_name ); ?>[bubble_enabled]" value="1" <?php checked( $bubble_enabled ); ?> class="apalpador-bubble-toggle">
			<?php esc_html_e( 'Enable speech bubble', 'apalpador' ); ?>
		</label>
		<div class="apalpador-bubble-options" <?php echo $bubble_enabled ? '' : 'style="display:none;"'; ?>>
			<label>
				<?php esc_html_e( 'Text:', 'apalpador' ); ?>
				<input type="text" name="<?php echo esc_attr( $this->option_name ); ?>[bubble_text]" value="<?php echo esc_attr( $bubble_text ); ?>" class="apalpador-bubble-text" maxlength="100">
			</label>
			<p class="description"><?php esc_html_e( 'Maximum 100 characters.', 'apalpador' ); ?></p>
			<br>
			<label>
				<?php esc_html_e( 'Font size:', 'apalpador' ); ?>
				<select name="<?php echo esc_attr( $this->option_name ); ?>[bubble_size]">
					<option value="small" <?php selected( $bubble_size, 'small' ); ?>><?php esc_html_e( 'Small', 'apalpador' ); ?></option>
					<option value="medium" <?php selected( $bubble_size, 'medium' ); ?>><?php esc_html_e( 'Medium', 'apalpador' ); ?></option>
					<option value="large" <?php selected( $bubble_size, 'large' ); ?>><?php esc_html_e( 'Large', 'apalpador' ); ?></option>
				</select>
			</label>
			<br><br>
			<label>
				<?php esc_html_e( 'Trigger:', 'apalpador' ); ?>
				<select name="<?php echo esc_attr( $this->option_name ); ?>[bubble_trigger]">
					<option value="once" <?php selected( $bubble_trigger, 'once' ); ?>><?php esc_html_e( 'Show once on page load', 'apalpador' ); ?></option>
					<option value="click" <?php selected( $bubble_trigger, 'click' ); ?>><?php esc_html_e( 'Show on click', 'apalpador' ); ?></option>
					<option value="hover" <?php selected( $bubble_trigger, 'hover' ); ?>><?php esc_html_e( 'Show on hover', 'apalpador' ); ?></option>
				</select>
			</label>
		</div>
		<?php
	}

	/**
	 * Render snow effect fields.
	 *
	 * @return void
	 */
	public function render_field_snow() {
		$options      = Apalpador_Settings::get_options();
		$snow_enabled = $options['snow_enabled'] ?? true;
		$snow_density = $options['snow_density'] ?? 'medium';
		?>
		<label>
			<input type="checkbox" name="<?php echo esc_attr( $this->option_name ); ?>[snow_enabled]" value="1" <?php checked( $snow_enabled ); ?> class="apalpador-snow-toggle">
			<?php esc_html_e( 'Enable snow effect', 'apalpador' ); ?>
		</label>
		<div class="apalpador-snow-density" <?php echo $snow_enabled ? '' : 'style="display:none;"'; ?>>
			<label><?php esc_html_e( 'Density:', 'apalpador' ); ?></label>
			<select name="<?php echo esc_attr( $this->option_name ); ?>[snow_density]">
				<option value="low" <?php selected( $snow_density, 'low' ); ?>><?php esc_html_e( 'Low', 'apalpador' ); ?></option>
				<option value="medium" <?php selected( $snow_density, 'medium' ); ?>><?php esc_html_e( 'Medium', 'apalpador' ); ?></option>
				<option value="high" <?php selected( $snow_density, 'high' ); ?>><?php esc_html_e( 'High', 'apalpador' ); ?></option>
			</select>
		</div>
		<?php
	}

	/**
	 * Render shooting star fields.
	 *
	 * @return void
	 */
	public function render_field_star() {
		$options        = Apalpador_Settings::get_options();
		$star_enabled   = $options['star_enabled'] ?? true;
		$star_frequency = $options['star_frequency'] ?? 10;
		$star_color     = $options['star_color'] ?? '#ffffff';
		?>
		<label>
			<input type="checkbox" name="<?php echo esc_attr( $this->option_name ); ?>[star_enabled]" value="1" <?php checked( $star_enabled ); ?> class="apalpador-star-toggle">
			<?php esc_html_e( 'Enable shooting star effect', 'apalpador' ); ?>
		</label>
		<div class="apalpador-star-options" <?php echo $star_enabled ? '' : 'style="display:none;"'; ?>>
			<label>
				<?php esc_html_e( 'Frequency: every', 'apalpador' ); ?>
				<input type="number" name="<?php echo esc_attr( $this->option_name ); ?>[star_frequency]" value="<?php echo esc_attr( $star_frequency ); ?>" min="5" max="60" step="1">
				<?php esc_html_e( 'seconds', 'apalpador' ); ?>
			</label>
			<br><br>
			<label>
				<?php esc_html_e( 'Color:', 'apalpador' ); ?>
				<input type="color" name="<?php echo esc_attr( $this->option_name ); ?>[star_color]" value="<?php echo esc_attr( $star_color ); ?>">
			</label>
		</div>
		<?php
	}
}
