<?php

function autoloadClass ($class) 
{
    $path = __DIR__ . "/rest/" . $class . ".php";
    $path = str_replace("\\", '/', $path);

    if (file_exists($path)) 
    {
        require_once $path;

        if (class_exists($class)) 
        {
            return true;
        }
    }

    return false;
}

spl_autoload_register('autoloadClass');

/* MySql Home */
define('M_HOST','localhost');
define('M_USER','root');
define('M_PASS','');
define('M_DB','rest');
define('ERROR_CODE_INFORMATION', 'http://rest/server/ErrorCodeInformation.html');

/* MySql Class */
// define('M_HOST','localhost');
// define('M_USER','user10');
// define('M_PASS','tuser10');
// define('M_DB','user10');
// define('ERROR_CODE_INFORMATION', 'http://192.168.0.15/~user10/MYPHP/REST/server/ErrorCodeInformation.html');

/* SERVICE */
define('HASH_LIFETIME', '7200');

/* ERRORs */
define('DELIMITER', ' | ');
define('ERROR_HEADER_CODE', 'Error\'s Code: ');
define('ERROR_HTML_TEXT',
       '%STATUS_CODE% %ERROR_DESCRIPTION%' .DELIMITER. '%CODE_NUMBER%<br>
       <a href="' .ERROR_CODE_INFORMATION. '">
           View Error Code Information here.
       </a>'
);