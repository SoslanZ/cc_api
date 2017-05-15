#!/bin/bash

QUEUE_FILE_PATH=/etc/asterisk/queues_additional.conf
ARG_COUNT=0
QUEUE=

function rm_members {
  sed -i '/\['$QUEUE'\]/,/\[/ {/member=/d}' $QUEUE_FILE_PATH
}

function add_member {
  sed -i '/\['$QUEUE'\]/,/\[/!b;/\['$QUEUE'\]/amember=Local/'$1'@from-queue/n,0' $QUEUE_FILE_PATH
}

for var in "$@"
do
  ((ARG_COUNT++))
  if [ $ARG_COUNT == 1 ]
  then
   QUEUE=$var
   rm_members
   else
   add_member $var
  fi
done

