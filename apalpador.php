<?php

/*
Plugin Name: O Apalpador
Plugin URI: https://plugins.anxosanchez.com
Description: Coloca ao Apalpador na túa páxina web e recupera a tradición do nadal galego
Date: 14/12/2019
Version: 0.0.1
Author: Anxo Sánchez
Author URI: https://www.anxosanchez.com
License: GPL3
*/


/* evitamos que se poda executar directamente desde o navegador */
defined('ABSPATH') or die("Bye bye");

/* definimos constantes  */
define('PATH',plugin_dir_path(__FILE__));
define('URL',plugin_dir_url(__FILE__));

/* incluimos os arquivos que necesitamos  */
include(PATH.'includes/funcions.php');
include(PATH.'includes/opcions.php');


 ?>