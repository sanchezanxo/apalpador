<?php

/*
* 
*/

/* esta comprobación é moi importante. Comproba se o usuario ten permisos para acceder a esta configuración
e senon os ten, pois sáltalle unha mensaxe que di que non ten permisos suficientes */
if (! current_user_can ('manage_options')) wp_die (__ ('Non tes permisos suficientes, meu.'));
?>

	<div class="wrap">
		<h2>Plugin Apalpador</h2>
		<p>Benvido ao plugin do Apalpador. Un nadal galego en WordPress :-)</p>
        <p>Imaxe utilizada:</p>
        <?php	echo '<img src="'.URL.'apalpador/img/apalpador.gif" />';  ?>
			 
	</div>
    
<?php

?>