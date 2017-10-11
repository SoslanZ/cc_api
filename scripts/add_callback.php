<?php
require '../../cc-line24/inc/db_functions.inc.php';

$QUEUE_ID = check_value($argv[1]);
$CALLERID = check_value($argv[2]);

function check_value($value) {
  $value = trim($value);
  $value = mysql_real_escape_string($value);
  return $value;
}

if (!empty($QUEUE_ID)&&!empty($CALLERID)) {
  queue_callback($QUEUE_ID, $CALLERID, 0);
}
