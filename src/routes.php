
<?php
return array(
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
