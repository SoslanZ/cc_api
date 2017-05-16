#!/bin/bash

EXT_FILE_PATH=/etc/asterisk/extensions_additional.conf
ANNID=$1

if [ ! $ANNID ]
 then
  echo "ERROR: announcement error"
  exit 1
fi

sed -i '/\[app-announcement-'$ANNID'\]/,/; end of \[app-announcement-'$ANNID'\]/{ /.*/d }' $EXT_FILE_PATH