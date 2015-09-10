<?php

if(isset($_SERVER['DATABASE_URL'])){
    define('DBDRIVER','pgsql');
}else{
    define('DBDRIVER','sqlite');
}

define('PWD', realpath(dirname(__file__)));

define('BASE_URL', 'https://phlaskr-php.herokuapp.com');

define('SRC_DIR', PWD . '/src');
define('TMP_DIR',SRC_DIR);
define('PUB_DIR', PWD . '/public');
define('TPL_DIR', PWD . '/templates');

define('USERNAME', 'admin');
define('PASSWORD', 'default');

$include_paths = array(get_include_path(), SRC_DIR);

if(!session_id()){
    session_start();
    $_SESSION['logged_in'] = false;
    $_SESSION['flash'] = NULL;
}

set_include_path(implode(PATH_SEPARATOR, $include_paths));

function classloader($classname)
{
  require_once $classname . '.php';
}

spl_autoload_register('classloader');
