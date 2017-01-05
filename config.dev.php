<?php
 
ini_set('html_errors', false);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define ('DEVELOPMENT_ENVIRONMENT', true);

// development constants
define ('APP_BASE_PATH', '/mt');
define ('APP_DBHOST', 'localhost');
define ('APP_DBNAME', 'mt');
define ('APP_DBUSER', 'root');
define ('APP_DBPASS', null);	

$defaultController = 'generatorController';
$defaultAction = 'workoutAction';
?>