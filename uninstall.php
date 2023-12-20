<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

delete_option('apalpador_width');
delete_option('apalpador_position');
delete_option('apalpador_image');
delete_option('apalpador_snow_effect');


 ?>