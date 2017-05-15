<?php
class db {

  private $link;

  function __construct($db) {
    $this->link = mysql_connect($db->dbhost,$db->dbuser,$db->dbpass);
    mysql_select_db($db->db,$this->link);
  }

  function getConnection() {
    return $this->link;
  }

  function closeConnection() {
    mysql_close($this->link);
  }

}
