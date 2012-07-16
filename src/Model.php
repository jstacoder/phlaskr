<?php

class Model
{
  private $_db;

  public function __construct($path)
  {
    $this->_db = new PDO('sqlite:' . $path);
  }

  public function get_entries()
  {
    $query = $this->_db->prepare('select title, text from entries');

    if($query->execute())
    {
      return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    else
    {
      return array();
    }
  }

  public function put_entry($title, $text)
  {
    $query = $this->_db->prepare('insert into entries (title, text) values (:title, :text)');
    $query->bindParam(':title', $title);
    $query->bindParam(':text', $text);
    return $query->execute();
  }
}
