<?php

#require 'models/class.Model.php';

class DialPlan extends Model {
  public static function reloadDialPlanNow() {
    exec( "asterisk -rx 'dialplan reload'" );
  }
}
