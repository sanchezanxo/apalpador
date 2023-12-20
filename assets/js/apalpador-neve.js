jQuery(document).ready(function($) {
    // Verifica si la opción de efecto de nieve está habilitada

    // Función para activar el efecto de nieve
    function activateSnow() {
            $(document).snowfall({ round: true, flakeCount: 100, flakeColor: '#fff', flakePosition: 'absolute',collection: '.snowfall' });
            // Activa el efecto de nieve utilizando snowfall.js
    }

    // Llama a la función para activar el efecto de nieve
    activateSnow();
});
