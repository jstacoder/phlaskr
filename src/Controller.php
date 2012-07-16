<?php

/**
 * A singleton front-controller
 *
 * @see Controller::dispatch() for application routing and point-of-entry
 */
class Controller
{
  private $_routes;

  private $_model;

  private $_view;

  private function __construct($routes)
  {
    $this->_routes = $routes;
    $this->_model = new Model(TMP_DIR . '/phlaskr.db');
    $this->_view = new View(TPL_DIR . '/layout.phtml');
  }

  private function __clone()
  {
  }

  public function show_entries()
  {
    $entries = $this->_model->get_entries();
    echo $this->_view->evaluate('/show_entries.phtml', array('entries' => $entries));
  }

  public function add_entry()
  {
    if(!$_SESSION['logged_in'])
    {
      header('Status: 403 Forbidden');
      echo $this->_view->evaluate('/40x.phtml', array('code' => 403));
    }

    else if(isset($_POST['title']) && isset($_POST['text']))
    {
      $title = $_POST['title'];
      $text = $_POST['text'];

      if($this->_model->put_entry($title, $text))
      {
        $_SESSION['flash'] = 'New entry was successfully posted';
        header('Location: /');
      }

      else
      {
        $_SESSION['flash'] = 'Error encountered when inserting entry';
        header('Location: /');
      }
    } 

    else
    {
      $_SESSION['flash'] = 'Title and Text are required fields';
      header('Location: /');
    }
  }

  public function login()
  {
    if($_SERVER['REQUEST_METHOD'] === 'POST')
    {
      if($_REQUEST['username'] !== USERNAME)
      { 
        $error = 'Invalid username';
      }

      else if($_REQUEST['password'] !== PASSWORD)
      {
        $error = 'Invalid password';
      }
      
      else
      {
        $error = NULL;
        $_SESSION['logged_in'] = TRUE;
        $_SESSION['flash'] = 'You were logged in';
        header('Location: /');
        return;
      }
    }

    echo $this->_view->evaluate('/login.phtml', array('error' => $error));
  }

  public function logout()
  {
    if($_SESSION['logged_in'])
    {
      unset($_SESSION['logged_in']);
      $_SESSION['flash'] = 'You were logged out';
    }

    header('Location: /');
  }

  /**
   * Proxy a request to an instance method, as mapped in our <code>_routes</code> member
   */
  public function dispatch($httpMethod, $path)
  {
    if(array_key_exists($path, $this->_routes))
    {
      if(array_key_exists($httpMethod, $this->_routes[$path]))
      {
        return call_user_func(array($this, $this->_routes[$path][$httpMethod]));
      }

      else
      {
        header('Status: 415 Unsupported Method');
        echo $this->_view->evaluate('/40x.phtml', array('code' => 415));
      }
    }

    else
    {
      header('Status: 404 File not found');
      echo $this->_view->evaluate('/40x.phtml', array('code' => 404));
    }
  }

  private static $_instance;

  public static function getInstance($routes)
  {
    self::$_instance = new self($routes);
    return self::$_instance;
  }
}
