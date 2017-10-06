<?php
// set_include_path(get_include_path() .PATH_SEPARATOR. 'shop/cars' .PATH_SEPARATOR. 'rest');

function __autoload($class){
    require_once('rest/' .$class. '.php');
}

/* MySql Home */
// define('M_HOST','localhost');
// define('M_USER','root');
// define('M_PASS','');
// define('M_DB','soap');

/* MySql Class */
define('M_HOST','localhost');
define('M_USER','user10');
define('M_PASS','tuser10');
define('M_DB','user10');

/* SERVICE */
define('DELIMITER', ' | ');
define('ERROR_CODE_INFORMATION', 'http://rest/server/ErrorCodeInformation.html');

/* ERRORs */
define('ERROR_HEADER_CODE', 'Error\'s Code: ');
define('ERROR_HTML_TEXT', 
       '%STATUS_CODE% %ERROR_DESCRIPTION%' .DELIMITER. '%CODE_NUMBER%<br>
       <a href="' .ERROR_CODE_INFORMATION. '">
           View Error Code Information here.
       </a>'
);

