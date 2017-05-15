#!/bin/bash

 EXT_FILE_PATH=/etc/asterisk/queues_additional.conf
 QUEUE=$1
 WEIGHT=$2

 sed -i '/\['$QUEUE'\]/,/weight/ { s/weight=[0-9]*/weight='$WEIGHT'/ }' $EXT_FILE_PATH

