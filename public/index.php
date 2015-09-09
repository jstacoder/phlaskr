<?php
/*
 * The point-of-entry for our application
 *
 * Our web server should be configured to forward
 * all requests to this script
 *
 * See INSTALL for more information
 */

// Start or resume session handling
//
if(!session_id()){
    session_start();
}
require realpath('..') . '/config.php';

// Include our configuration variables

// Declare our application's valid URL routes

$routes = require dirname(dirname(__FILE__)) . '/'.'src'.'/'.'routes.php';
$x = array(
    '/' => array(
        'GET' => 'show_entries'
    ),

    '/add' => array(
        'POST' => 'add_entry'
    ),

    '/login' => array(
        'GET' => 'login', 
        'POST' => 'login'
    ),

    '/logout' => array(
        'GET' => 'logout'
    ),

    '/delete' => array(
        'GET' => 'delete_entry'
    )
);

// Instantiate our front controller and handle the request
$controller = Controller::getInstance($routes);
$path = parse_url($_SERVER['REQUEST_URI'])['path'];
$controller->dispatch($_SERVER['REQUEST_METHOD'], $path);
