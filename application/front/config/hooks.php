<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/



/* End of file hooks.php */
/* Location: ./application/config/hooks.php */



$hook['pre_system'][] = array(
    'class'    => 'settings_options',
    'function' => 'under_construction',
    'filename' => 'settings_options.php',
    'filepath' => 'hooks'
); 


$hook['pre_system'][] = array(
    'class'    => 'settings_options',
    'function' => 'close_db',
    'filename' => 'settings_options.php',
    'filepath' => 'hooks'
); 

$hook['post_controller_constructor'][] = array(
    'class'    => 'settings_options',
    'function' => 'language',
    'filename' => 'settings_options.php',
    'filepath' => 'hooks'
); 

$hook['post_controller_constructor'][] = array(
    'class'    => 'settings_options',
    'function' => 'set_settings_options',
    'filename' => 'settings_options.php',
    'filepath' => 'hooks'
); 

/*
$hook['pre_system'][] = array(
    'class'    => 'settings_options',
    'function' => 'set_routes',
    'filename' => 'settings_options.php',
    'filepath' => 'hooks'
);
*/
