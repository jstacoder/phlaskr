<?php

class Model
{
  private $_db;

  public function __construct($path)
  {
    $this->_db = new PDO('sqlite:' . $path);
  }
  public function getDB(){
    return $this->_db;
  }

  public function add_tags($entry_id,$tags=array()){
    $sql = 'insert into tags (name) values (:name)';
    $q = $this->_db->prepare($sql);
    $results = array();
    $ids = array();
    foreach($tags as $tag){
        $q->bindParam(':name',$tag);
        $results[$tag] = $q->execute();
        $ids[] = $this->_db->lastInsertId();
    }
    $sql = 'insert into tags_entry (tag_id,entry_id) values (:tid,:eid)';
    $q = $this->_db->prepare($sql);
    foreach($ids as $id){
        $q->bindParam(':eid',$entry_id);
        $q->bindParam(':tid',$id);
        $results['tags_entrys'.$id] = $q->execute();
    }
    return $results;
  }

  public function get_entry_tags($eid){
      $s = $this->_db->prepare('select name from tags join tags_entry on tags_entry.tag_id = tags.id where tags_entry.entry_id = :eid');
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

  private function _get_date()
  {
    return date("Y-m-d\TH:i:s",time());
  }

  public function put_entry($title, $text)
  {
    $query = $this->_db->prepare('insert into entries (title, text) values (:title, :text)');
    $query->bindParam(':title', $title);
    $query->bindParam(':text', $text);
    //$query->bindParam(':added',$this->_get_date());
    return $query->execute();
  }
  public function delete_entry($id)
  {
    $query = $this->_db->prepare("delete from entries where `id` = :id");
    $query->bindParam(':id',$id);
    return $query->execute();
  }
}
