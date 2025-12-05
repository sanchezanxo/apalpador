<?php
/**
 * Settings class for Apalpador plugin.
 *
 * Handles retrieval and management of plugin settings.
 *
 * @package Apalpador
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Apalpador_Settings
 *
 * Manages plugin settings and provides helper methods.
 */
class Apalpador_Settings {

	/**
	 * Cached options.
	 *
	 * @var array|null
	 */
	private static $options = null;

	/**
	 * Get all plugin options.
	 *
	 * @return array Plugin options.
	 */
	public static function get_options() {
		if ( null === self::$options ) {
			self::$options = get_option( 'apalpador_options', array() );
		}
		return self::$options;
	}

	/**
	 * Get a single option value.
	 *
	 * @param string $key     Option key.
	 * @param mixed  $default Default value if option not found.
	 * @return mixed Option value.
	 */
	public static function get_option( $key, $default = null ) {
		$options = self::get_options();
		return isset( $options[ $key ] ) ? $options[ $key ] : $default;
	}

	/**
	 * Check if plugin is enabled.
	 *
	 * @return bool True if enabled.
	 */
	public static function is_enabled() {
		return (bool) self::get_option( 'enabled', true );
	}

	/**
	 * Check if current date is within the configured date range.
	 *
	 * @return bool True if within date range.
	 */
	public static function is_within_date_range() {
		$start = self::get_option( 'date_start', '12-01' );
		$end   = self::get_option( 'date_end', '01-06' );

		$current_month_day = gmdate( 'm-d' );

		// Handle year wrap (e.g., Dec 1 to Jan 6).
		if ( $start > $end ) {
			// Either after start OR before end.
			return ( $current_month_day >= $start || $current_month_day <= $end );
		}

		// Normal range within same year.
		return ( $current_month_day >= $start && $current_month_day <= $end );
	}

	/**
	 * Get default options.
	 *
	 * @return array Default options.
	 */
	public static function get_defaults() {
		return array(
			'enabled'           => true,
			'character_enabled' => true,
			'date_start'        => '12-01',
			'date_end'          => '01-06',
			'image_preset'      => 'default',
			'image_id'          => 0,
			'position'          => 'bottom-left',
			'size'              => 'medium',
			'size_custom'       => 150,
			'size_mobile'       => 'small',
			'size_custom_mobile' => 100,
			'padding_h'         => 20,
			'padding_v'         => 20,
			'anim_entry'        => 'slide',
			'anim_idle'         => true,
			'anim_click'        => 'shake',
			'bubble_enabled'    => false,
			'bubble_text'       => 'Bo Nadal!',
			'bubble_trigger'    => 'once',
			'bubble_size'       => 'medium',
			'snow_enabled'      => true,
			'snow_density'      => 'medium',
			'star_enabled'      => true,
			'star_frequency'    => 10,
			'star_color'        => '#ffffff',
		);
	}

	/**
	 * Get available preset images from the plugin directory.
	 *
	 * @return array Array of preset images with slug => url.
	 */
	public static function get_preset_images() {
		$presets   = array();
		$images_dir = APALPADOR_PLUGIN_DIR . 'assets/images/apalpadores/';
		$images_url = APALPADOR_PLUGIN_URL . 'assets/images/apalpadores/';

		if ( ! is_dir( $images_dir ) ) {
			return $presets;
		}

		$files = glob( $images_dir . '*.{png,jpg,jpeg,svg,gif,webp}', GLOB_BRACE );

		if ( $files ) {
			foreach ( $files as $file ) {
				$filename = basename( $file );
				$slug     = pathinfo( $filename, PATHINFO_FILENAME );
				$presets[ $slug ] = $images_url . $filename;
			}
		}

		return $presets;
	}
}
