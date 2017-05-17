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

  public function beginTransaction() {
    return mysql_query("BEGIN",$this->getConnection());
  }

  public function commit() {
    return mysql_query("COMMIT",$this->getConnection());
  }

  public function rollback() {
    return mysql_query("ROLLBACK",$this->getConnection());
  }

  public function execute($query) {
    $result = mysql_query($query,$this->getConnection());
    if ( !$result ) {
      $this->rollback();
      throw new Exception( mysql_error($this->getConnection()) );
    }
    return $result;
  }

}
