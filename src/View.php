<?php

/**
 * Evaluate a PHP script (for display as an HTML template)
 *
 * A View may accept a decorating parent script,
 * which then can include a child template by invoking:
 *
 *    <?php include($template); ?>
 *
 * from anywhere within the parent script.
 */
class View
{
  private $_decorator;

  public function __construct($decorator)
  {
    $this->_decorator = $decorator;
  }

  public function evaluate($name, $vars = array())
  {
    extract($vars);

    $template = TPL_DIR . $name;

    ob_start();

    if($this->_decorator === null)
    {
      include($template);
    }

    else
    {
      include($this->_decorator);
    }

    $response = ob_get_clean();

    unset($_SESSION['flash']);

    return $response;
  }
}
