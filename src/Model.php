<?php

class Model
{
  private $_db;

  public function __construct()
  {
    //if(DBDRIVER=='sqlite'){
    //   $this->_db = new PDO('sqlite:' . $path);
    //}else{
        $this->_db = new PDO('pgsql:host=ec2-50-19-208-138.compute-1.amazonaws.com;port=5432;dbname=d5k68ubselumrp','obyeuglafabvok','YcB0PUlJzkoC2oWvLrxd2iyMwt');  
    //}
  }
  public function getDB(){
    return $this->_db;
  }

  public function add_tags($entry_id,$tags=array()){
    $sql = 'insert into tags (name,entry_id) values (:name,:eid)';
    $q = $this->_db->prepare($sql);
    $success = true;
    foreach($tags as $tag){
        
        $q->bindParam(':name',$tag);
        $q->bindParam(':eid',$entry_id);

        $current_success = $q->execute();
        if($success){
            if(!$current_success){
                $success = false;
            }
        }
    }
    return $success;
  }

  public function _get_last_entry_id(){
        $lid = $this->_db->prepare('select id from entries');
        $lid->execute();
        
        $ids = array();
        while($o = $lid->fetchObject()){
            $ids[] = $o->id;
        }
        return max($ids);
  }

  public function get_entry_tags($eid){
     $sql = 'SELECT tags.name, tags.id '
           .'FROM entries JOIN tags ON entries.id = tags.entry_id '
           .'WHERE entries.id = :eid';
      $s = $this->_db->prepare($sql);
      $s->bindParam(':eid',$eid);
      if($r = $s->execute()){
        return $s->fetchAll(PDO::FETCH_OBJ);
      }
      return false;
  } 
  public function get_entry($eid){
      $tags = $this->get_entry_tags($eid);
      $entry = $this->_get_entry($eid);
      $entry->tags = $tags;
      $entry->id = $eid;
      return $entry;
  }

  private function _get_entry($eid){
        $sql = $this->_db->prepare('select title,text,added from entries where entries.id = :eid');
        $sql->bindParam(':eid',$eid);
        if($sql->execute()){
            return $sql->fetchObject();
        }
        return false;
  }

  public function get_entries()
  {
    $q = $this->_db->prepare('select id from entries');
    $q->execute();
    $ids = array();
    while($o = $q->fetchObject()){
        $ids[] = $o->id;
    }
    $rtn = array();
    foreach($ids as $eid){
        $rtn[] = $this->get_entry($eid);
    }
    return $rtn;
    /*
    $query = $this->_db->prepare('select id, title, text, added from entries');

    if($query->execute())
    {
      return $query->fetchAll(PDO::FETCH_OBJ);
    }

    else
    {
      return array();
    }*/
  }

  public function put_entry($title, $text)
  {
    $query = $this->_db->prepare('insert into entries (title, text) values (:title, :text)');
    $query->bindParam(':title', $title);
    $query->bindParam(':text', $text);
    return $query->execute();
  }

  public function delete_entry($id)
  {
      $tags = $this->get_entry_tags($id);
      foreach($tags as $tag){
          $this->delete_tag($tag->id);
      }
      $query = $this->_db->prepare("delete from entries where entries.id = :id");
      $query->bindParam(':id',$id);
      return $query->execute();
  }
  public function delete_tag($id)
  {
      $query = $this->_db->prepare("delete from tags where tags.id = :id");
      $query->bindParam(':id',$id);
      return $query->execute();
  }
}
