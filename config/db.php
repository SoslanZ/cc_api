<?php
class db {

  private $link;

  function __construct($db) {
    $this->link = mysql_connect($db->dbhost,$db->dbuser,$db->dbpass);
    mysql_select_db($db->db,$this->link);
  }

  public function getConnection() {
    return $this->link;
  }

  public function closeConnection() {
    mysql_close($this->link);
  }

  public function begin() {
    mysql_query("BEGIN",$this->link);
  }

  public function commit() {
    mysql_query("COMMIT",$this->link);
  }

  public function rollback() {
    mysql_query("ROLLBACK",$this->link);
  }

}
