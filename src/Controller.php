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
    $this->_model = new Model(SRC_DIR . '/phlaskr.db');
    $this->_view = new View(TPL_DIR . '/layout.phtml');
  }

  private function _flash($data,$reset=false){
        if($reset){
            unset($_SESSION['flash']);
            $_SESSION['flash'] = array();
        }
        $_SESSION['flash'][] = $data;
  }

  public function parse_uri()
  {
    $rtn = array();
    $tmp = parse_url($_SERVER['REQUEST_URI'])['query'];
    parse_str($tmp,$rtn);
    return $rtn;
  }

  private function __clone()
  {
  }

  public function show_entries()
  {
    $entries = $this->_model->get_entries();
    echo $this->_view->evaluate('/show_entries.phtml', array('entries' => $entries));
  }
  public function redirect($uri){
    echo '<meta http-equiv="refresh" content="0; URL='.$uri.'">';
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
      $tag_list = !empty($_POST['tags']) ? $_POST['tags'] : '';

      $tags = explode(',',$tag_list);

      if($this->_model->put_entry($title, $text))
      {
        if(!empty($tags)){
            $id = $this->_model->_get_last_entry_id();
            $this->_model->add_tags($id,$tags);
        }
        $this->_flash('New entry was successfully posted',true);
        $this->redirect('/');
      }
      else
      {
        $this->_flash('Error encountered when inserting entry');
        $this->redirect('/');
      }
    } 

    else
    {
      $this->_flash('Title and Text are required fields');
      $this->redirect('/');
    }
  }

  public function login()
  {
    $error = '';
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
        $this->_flash('You were logged in',true);
      $this->redirect('/');
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
      $this->_flash('You were logged out',true);
    }

      $this->redirect('/');
    return;
  }
  public function delete_entry()
  {
      if(!$_SESSION['logged_in'])
        {
           header('Status: 403 Forbidden');
           echo $this->_view->evaluate('/40x.phtml', array('code' => 403));
        }
        $data = $this->parse_uri();
        $this->_model->delete_entry($data['id']);
        $this->_flash('Deleted an entry',true);
      $this->redirect('/');
        return;
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

  private static $_instance = null;

  public static function getInstance($routes)
  {
    self::$_instance = is_null(self::$_instance) ? new static($routes) : self::$_instance;
    return self::$_instance;
  }
}
