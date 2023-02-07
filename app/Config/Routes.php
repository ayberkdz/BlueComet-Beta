<?php
/**
 * Router class called as an object.
 */
$router = new BlueComet\Router\Router();

/**
 * Setting the main page view as default.
 */
$router->setDefaultController('Home');
$router->setDefaultMethod('index');

/**
 * Expand your project by adding your views below.
 */
$router->get('/home', 'Home::index');

/**
 * Loads all of your views.
 */
$router->load();
?>