<?php
/*
Plugin Name: Apalpador
Plugin URI: https://www.anxosanchez.com
Description: Coloca a imaxe do "Apalpador" (figura mitolóxica do nadal galego) no teu WordPress
Version: 1.0.0
Author: Anxo Sánchez
Author URI: https://anxosanchez.com
Text Domain: apalpador
License: GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/

/*
 * Esta función agrega as opcións laterais de administración.
 */
function agregar_menu_apalpador() {
    add_menu_page('Apalpador', 'Apalpador', 'manage_options', 'apalpador-menu', 'render_pagina_apalpador');
}
add_action('admin_menu', 'agregar_menu_apalpador');


/*
 * Esta función controla o formulario de opcións do plugin. Aquí realízanse as configuracións.
 */
function render_pagina_apalpador() {

    $snow_effect = get_option('apalpador_snow_effect', 0); // valor actual do efecto neve
    ?>
	
    <div class="wrap">
        <h2>Configuracións xerais</h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('apalpador-settings'); // agrego campos de configuración
            do_settings_sections('apalpador-settings'); // mostro seccións
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Seleccionar imaxe</th>
                    <td>
                        <?php
                        $imaxe_seleccionada = get_option('apalpador_image');
                        $imaxes_dir = plugin_dir_path(__FILE__) . 'public/images/';
                        $imaxes = scandir($imaxes_dir);
                        foreach ($imaxes as $imaxe) {
                            if ($imaxe !== '.' && $imaxe !== '..') {
                                $imaxe_url = plugins_url('public/images/' . $imaxe, __FILE__);
                                $selected = ($imaxe === $imaxe_seleccionada) ? 'checked' : '';
                                echo '<label>';
                                echo '<input type="radio" name="apalpador_image" value="' . esc_attr($imaxe) . '" ' . $selected . '>';
                                echo '<img src="' . esc_url($imaxe_url) . '" alt="' . esc_attr($imaxe) . '" width="50" height="50" />';
                                echo esc_html($imaxe);
                                echo '</label>';
                                echo '<br>';
                            }
                        }
                        ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Ubicación da imaxe</th>
                    <td>
                        <select name="apalpador_location">
                            <option value="bottom-right" <?php selected(get_option('apalpador_location'), 'bottom-right'); ?>>Parte inferior dereita</option>
                            <option value="bottom-left" <?php selected(get_option('apalpador_location'), 'bottom-left'); ?>>Parte inferior esquerda</option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Ancho da imaxe (en px)</th>
                    <td>
                        <input type="text" name="apalpador_width" value="<?php echo esc_attr(get_option('apalpador_width')); ?>" placeholder="Ancho" />
                        <p class="description">O alto axustarase automaticamente mantendo a proporción. En móbil, o tamaño será a metade.</p>
                    </td>
                </tr>
				<tr valign="top">
                    <th scope="row">Efecto neve</th>
                    <td>
                        <label for="apalpador_snow_effect">
                            <input type="checkbox" id="apalpador_snow_effect" name="apalpador_snow_effect" value="1" <?php checked($snow_effect, 1); ?> />
                            Activar efecto de nieve
                        </label>
                    </td>
                </tr>
                <tr>
            </table>
            <?php
            submit_button(); // gardar trocos
            ?>
        </form>
    </div>
    <?php
}

/*
 * Esta función rexistra as opcións na base de datos.
 */
function registrar_opciones_apalpador() {

    add_settings_section('apalpador-settings', 'Configuración de Apalpador', '', 'apalpador-settings');     // rexistro da sección
    register_setting('apalpador-settings', 'apalpador_image');
    register_setting('apalpador-settings', 'apalpador_location');
    register_setting('apalpador-settings', 'apalpador_width');
    register_setting('apalpador-settings', 'apalpador_snow_effect'); 
}
add_action('admin_init', 'registrar_opciones_apalpador');

/*
 * Como mostramos a imaxe do Apalpador e o efecto neve na páxina web.
 */
function mostrar_imaxe_apalpador_en_contenido($content) {
    // opcións gardadas
    $imaxe_seleccionada = get_option('apalpador_image');
    $ubicacion_imaxe = get_option('apalpador_location');
    $ancho_imaxe = get_option('apalpador_width');
    if (!empty($imaxe_seleccionada)) {    // Hai imaxe?
        $imaxe_style = '';
        if ($ubicacion_imaxe === 'bottom-right') {
            $imaxe_style = 'style="bottom: 0; right: 0;"';
        } elseif ($ubicacion_imaxe === 'bottom-left') {
            $imaxe_style = 'style="bottom: 0; left: 0;"';
        }
		// xeramos o html da imaxe
		$imaxe_html = '<div class="apalpador-container apalpador-' . esc_attr($ubicacion_imaxe) . '" ' . $imaxe_style . '>';
		$imaxe_html .= '<img class="apalpador-image" src="' . esc_url(plugins_url('public/images/' . $imaxe_seleccionada, __FILE__)) . '" alt="Apalpador" />';
		$imaxe_html .= '</div>';
		// efecto neve
		$imaxe_html .= '<div class="snowfall"></div>';

		// Se o usuario navega en MOBIL entón baixamos o tamaño á metade
		if (wp_is_mobile()) {
			$tamaño_deseado = $ancho_imaxe / 2; 
		} else	{
			$tamaño_deseado = $ancho_imaxe; // Tamaño predeterminado		
		}
		
		$imaxe_html = str_replace('<img', '<img width="' . esc_attr($tamaño_deseado) . '"', $imaxe_html); //cambiamos o tamaño
		$content = $imaxe_html . $content;	// agrego imaxe ao contido
    }

    return $content;
}
add_filter('the_content', 'mostrar_imaxe_apalpador_en_contenido');

/*
 * Rexistramos o CSS.
 */
function registrar_recursos_apalpador() {
    wp_register_style('apalpador-styles', plugins_url('assets/css/apalpador-styles.css', __FILE__));
    
}
add_action('wp_enqueue_scripts', 'registrar_recursos_apalpador');

/*
 * Encolamos o CSS.
 */
function encolar_recursos_apalpador() {

    wp_enqueue_style('apalpador-styles');
    
}
add_action('wp_enqueue_scripts', 'encolar_recursos_apalpador');


/*
 * Rexistramos e encolamos o JS do efecto neve
 */
function cargar_apalpador_neve_script() {
	
    $snow_effect = get_option('apalpador_snow_effect', 0); //obtemos se está activado o efecto neve

    if ($snow_effect == 1) {
		// jquery.snowfall.min.js
		wp_register_script('jquery-snowfall', plugin_dir_url(__FILE__) . 'assets/js/snowfall.jquery.min.js', array('jquery'), null, true);
		wp_enqueue_script('jquery-snowfall');

		// apalpador-neve.js
		wp_register_script('apalpador-neve', plugin_dir_url(__FILE__) . 'assets/js/apalpador-neve.js', array('jquery'), null, true);
		wp_enqueue_script('apalpador-neve');
	}
}
add_action('wp_enqueue_scripts', 'cargar_apalpador_neve_script');

