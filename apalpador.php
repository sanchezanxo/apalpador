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

// Menú lateral
function agregar_menu_apalpador() {
    add_menu_page('Apalpador', 'Apalpador', 'manage_options', 'apalpador-menu', 'render_pagina_apalpador');
}
add_action('admin_menu', 'agregar_menu_apalpador');


// Páxina de opcións do plugin
function render_pagina_apalpador() {
    ?>
    <div class="wrap">
        <h2>Configuracións xerais</h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('apalpador-settings'); // agrego campos de configuración
            do_settings_sections('apalpador-settings'); // Mostro seccións
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Seleccionar imaxe</th>
                    <td>
                        <?php
                        $imagen_seleccionada = get_option('apalpador_image');

                        $imagenes_dir = plugin_dir_path(__FILE__) . 'public/images/';
                        $imagenes = scandir($imagenes_dir);

                        foreach ($imagenes as $imagen) {
                            if ($imagen !== '.' && $imagen !== '..') {
                                $imagen_url = plugins_url('public/images/' . $imagen, __FILE__);
                                $selected = ($imagen === $imagen_seleccionada) ? 'checked' : '';
                                echo '<label>';
                                echo '<input type="radio" name="apalpador_image" value="' . esc_attr($imagen) . '" ' . $selected . '>';
                                echo '<img src="' . esc_url($imagen_url) . '" alt="' . esc_attr($imagen) . '" width="50" height="50" />';
                                echo esc_html($imagen);
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
            </table>
            <?php
            submit_button(); // gardar trocos
            ?>
        </form>
    </div>
    <?php
}

// Nesta función rexistramos as opcións
function registrar_opciones_apalpador() {
    // rexistro da sección
    add_settings_section('apalpador-settings', 'Configuración de Apalpador', '', 'apalpador-settings');

    // rexistro de opcións
    register_setting('apalpador-settings', 'apalpador_image');
    register_setting('apalpador-settings', 'apalpador_location');
    register_setting('apalpador-settings', 'apalpador_width');
    register_setting('apalpador-settings', 'apalpador_height');
}
add_action('admin_init', 'registrar_opciones_apalpador');


function mostrar_imagen_apalpador_en_contenido($content) {
    // opcións gardadas
    $imagen_seleccionada = get_option('apalpador_image');
    $ubicacion_imagen = get_option('apalpador_location');
    $ancho_imagen = get_option('apalpador_width');

    // Hai imaxe?
    if (!empty($imagen_seleccionada)) {
        $imagen_style = '';
        if ($ubicacion_imagen === 'bottom-right') {
            $imagen_style = 'style="bottom: 0; right: 0;"';
        } elseif ($ubicacion_imagen === 'bottom-left') {
            $imagen_style = 'style="bottom: 0; left: 0;"';
        }
		// xeramos o html da imaxe
    // Genera el HTML para mostrar la imagen
    $imagen_html = '<div class="apalpador-container apalpador-' . esc_attr($ubicacion_imagen) . '" ' . $imagen_style . '>';
    $imagen_html .= '<img class="apalpador-image" src="' . esc_url(plugins_url('public/images/' . $imagen_seleccionada, __FILE__)) . '" alt="Apalpador" />';
    $imagen_html .= '</div>';

    // Calcula el tamaño deseado en función del ancho de la pantalla


    // Verifica si el usuario está en un dispositivo móvil (ancho de pantalla menor)
    if (wp_is_mobile()) {
        $tamaño_deseado = $ancho_imagen / 2; // Reduce el tamaño á terceira parte
    } else	{
    $tamaño_deseado = $ancho_imagen; // Tamaño predeterminado		
	}

    // Agrega el tamaño calculado como atributo al elemento de imagen
    $imagen_html = str_replace('<img', '<img width="' . esc_attr($tamaño_deseado) . '"', $imagen_html);

		
		
		
        // agrego imaxe ao contido
        $content = $imagen_html . $content;
    }

    return $content;
}

add_filter('the_content', 'mostrar_imagen_apalpador_en_contenido');


function registrar_recursos_apalpador() {
    // rexistramos o CSS
    wp_register_style('apalpador-styles', plugins_url('assets/css/apalpador-styles.css', __FILE__));
    
}
add_action('wp_enqueue_scripts', 'registrar_recursos_apalpador');

function encolar_recursos_apalpador() {
    // encolamos o CSS
    wp_enqueue_style('apalpador-styles');
    
}
add_action('wp_enqueue_scripts', 'encolar_recursos_apalpador');
