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

  function begin() {
    mysql_query("START TRANSACTION;",$this->getConnection());
  }

  function commit() {
    mysql_query("COMMIT;",$this->getConnection());
  }

  function rollback() {
    mysql_query("ROLLBACK;",$this->getConnection());
  }

}
