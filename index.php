<?php
/** 
 * Defined BlueComet 's php version.
 */
define('BC_PHP_VERSION', '8.0.23');

if (version_compare(phpversion(), BC_PHP_VERSION, '<')) {
    die('BlueComet supports PHP version '.BC_PHP_VERSION.' and above. The PHP version '.phpversion().' you are using is not supported.');
}

/**
 * Expected to bring the bootstrap.
 */
require_once('system/Bootstrap.php');

/**
 * Expected to bring the routes.
 */
require_once('app/Config/Routes.php');
?>