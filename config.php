<?php

define('PWD', realpath(dirname(__file__)));

define('BASE_URL', 'http://phlaskr');

define('SRC_DIR', PWD . '/src');
define('TMP_DIR', PWD . '/tmp');
define('PUB_DIR', PWD . '/public');
define('TPL_DIR', PWD . '/templates');

define('USERNAME', 'admin');
define('PASSWORD', 'default');

$include_paths = array(get_include_path(), SRC_DIR);

set_include_path(implode(PATH_SEPARATOR, $include_paths));

function classloader($classname)
{
  require_once $classname . '.php';
}

spl_autoload_register(classloader);
