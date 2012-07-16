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
session_start();

// Include our configuration variables
require realpath('..') . '/config.php';

// Declare our application's valid URL routes
$routes = array(
  '/' => array('GET' => 'show_entries'),

  '/add' => array('POST' => 'add_entry'),

  '/login' => array('GET' => 'login', 'POST' => 'login'),

  '/logout' => array('GET' => 'logout')
);

// Instantiate our front controller and handle the request
$controller = Controller::getInstance($routes);
$controller->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
