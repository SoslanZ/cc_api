#!/bin/bash

#CONFIG
EXT_FILE_PATH=/etc/asterisk/extensions_additional.conf 
ACTION=$1
ANNID=$2
RECNAME=$3

#FUNC
function add_rec {
  sed -i '/\[app-announcement-'$ANNID'\]/,/; end of \[app-announcement-'$ANNID'\]/{ s/s,n,Playback.*/s,n,Playback(custom\/'$RECNAME',noanswer)/ }' $EXT_FILE_PATH
  sed -i '/\[app-announcement-'$ANNID'\]/,/; end of \[app-announcement-'$ANNID'\]/{ s/s,n(play),Background.*/s,n,Playback(custom\/'$RECNAME',noanswer)/ }' $EXT_FILE_PATH
}

function rm_rec {
  sed -i '/\[app-announcement-'$ANNID'\]/,/; end of \[app-announcement-'$ANNID'\]/{ s/s,n,Playback.*/s,n,Playback(,noanswer)/ }' $EXT_FILE_PATH
  sed -i '/\[app-announcement-'$ANNID'\]/,/; end of \[app-announcement-'$ANNID'\]/{ s/s,n(play),Background.*/s,n,Playback(,noanswer)/ }' $EXT_FILE_PATH
}

if [ ! $ACTION ]
 then
  echo "ERROR: action error"
  exit 1
fi

if [ ! $ANNID ]
 then
   echo "ERROR: announcement error"
 exit 1
fi

if [ $ACTION == 'add' ]
 then
  if [ ! $RECNAME ]
   then
    echo "ERROR: recname error"
    exit 1
   fi
  add_rec
fi

if [ $ACTION == 'rm' ]
 then
  rm_rec
fi

