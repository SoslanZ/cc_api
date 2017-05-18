<?php

#require 'models/class.Model.php';

class DialPlan extends Model {
  public static function dialPlanReloadNow() {
    exec( "asterisk -rx 'dialplan reload'" );
  }
}
