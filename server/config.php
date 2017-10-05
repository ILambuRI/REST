<?php
// set_include_path(get_include_path() .PATH_SEPARATOR. 'shop/cars' .PATH_SEPARATOR. 'rest');

function __autoload($class){
    require_once('rest/' .$class. '.php');
}

/* MySql Home */
define('M_HOST','localhost');
define('M_USER','root');
define('M_PASS','');
define('M_DB','soap');

// /* MySql Class */
// define('M_HOST','localhost');
// define('M_USER','user10');
// define('M_PASS','tuser10');
// define('M_DB','user10');

/* ERRORs */
