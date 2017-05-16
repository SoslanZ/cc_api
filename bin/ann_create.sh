#!/bin/bash

EXT_FILE_PATH=/etc/asterisk/extensions_additional.conf
ANNID=$1
RECNAME=$2
QUEUE=$3

if [ ! $ANNID ]
 then
  echo "ERROR: announcement error"
  exit 1
fi

if [ ! $RECNAME ]
 then
  echo "ERROR: rec error"
  exit 1
fi

if [ ! $QUEUE ]
  then
   echo "ERROR: queue error"
   exit 1
 fi
 
echo "
[app-announcement-"$1"]
include => app-announcement-"$1"-custom
exten => fax,1,Goto(\${CUT(FAX_DEST,^,1)},\${CUT(FAX_DEST,^,2)},\${CUT(FAX_DEST,^,3)})
exten => s,1,GotoIf(\$[\"\${CDR(disposition)}\" = \"ANSWERED\"]?begin)
exten => s,n,Answer
exten => s,n,Wait(1)
exten => s,n(begin),Noop(Playing announcement "custom $1")
exten => s,n,Playback(custom/"$2",noanswer)
exten => s,n,Goto(ext-queues,"$3",1)

; end of [app-announcement-"$1"]" >> $EXT_FILE_PATH
