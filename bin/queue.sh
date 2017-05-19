#!/bin/bash

QUEUE_FILE_PATH=/etc/asterisk/queues_additional.conf
EXT_FILE_PATH=/etc/asterisk/extensions_additional.conf
ACT=$1
QUEUE=$2
ARG_COUNT=0

if [ ! $ACT ]
 then
   echo "ERROR: act not set"
   exit 1
fi

if [ ! $QUEUE ]
 then
   echo "ERROR: queue not set"
   exit 1
fi

function create_queue {
echo "["$QUEUE"]
announce-frequency=0
announce-holdtime=no
announce-position=no
autofill=no
eventmemberstatus=no
eventwhencalled=no
joinempty=yes
leavewhenempty=no
maxlen=0
periodic-announce-frequency=0
queue-callswaiting=silence/1
queue-thereare=silence/1
queue-youarenext=silence/1
reportholdtime=no
retry=0
ringinuse=yes
servicelevel=60
strategy=ringall
timeout=60
weight=0
wrapuptime=0
" >> $QUEUE_FILE_PATH
sed -i '/include => ext-queues-custom/a \
exten => '$QUEUE',1,Macro(user-callerid,) \
exten => '$QUEUE',n,Answer \
exten => '$QUEUE',n,Set(__BLKVM_OVERRIDE=BLKVM/${EXTEN}/${CHANNEL}) \
exten => '$QUEUE',n,Set(__BLKVM_BASE=${EXTEN}) \
exten => '$QUEUE',n,Set(DB(${BLKVM_OVERRIDE})=TRUE) \
exten => '$QUEUE',n,ExecIf($["${REGEX("(M[(]auto-blkvm[)])" ${DIAL_OPTIONS})}" != "1"]?Set(_DIAL_OPTIONS=${DIAL_OPTIONS}M(auto-blkvm))) \
exten => '$QUEUE',n,Set(__NODEST=${EXTEN}) \
exten => '$QUEUE',n,Set(MONITOR_FILENAME=/var/spool/asterisk/monitor/q${EXTEN}-${STRFTIME(${EPOCH},,%Y%m%d-%H%M%S)}-${UNIQUEID}) \
exten => '$QUEUE',n,Queue('$QUEUE',tr,,) \
exten => '$QUEUE',n,Noop(Deleting: ${BLKVM_OVERRIDE} ${DB_DELETE(${BLKVM_OVERRIDE})}) \
exten => '$QUEUE',n,Set(__NODEST=) \
exten => '$QUEUE',n,Goto(app-blackhole,hangup,1) \
exten => '$QUEUE'*,1,Macro(agent-add,'$QUEUE',) \
exten => '$QUEUE'**,1,Macro(agent-del,'$QUEUE')' $EXT_FILE_PATH
sed -i '/include => from-queue-custom/a \
exten => '$QUEUE',1,Goto(from-internal,${QAGENT},1)' $EXT_FILE_PATH
}

function delete_queue {
  sed -i '/\['$QUEUE'\]/,/^$/ { /.*/d }' $QUEUE_FILE_PATH
  sed -i '/exten => '$QUEUE',/d' $EXT_FILE_PATH
  sed -i '/exten => '$QUEUE'*,/d' $EXT_FILE_PATH
  sed -i '/exten => '$QUEUE'**,/d' $EXT_FILE_PATH
}

function add_member {
  sed -i '/\['$QUEUE'\]/,/^$/!b;/\['$QUEUE'\]/amember=Local/'$1'@from-queue/n,0' $QUEUE_FILE_PATH
}

for var in "$@"
do
  ((ARG_COUNT++))
  if [[ $ARG_COUNT == 1 ]]
  then
    if [ $var == "rm" ]
    then
      delete_queue
      exit 0
    fi
    if [ $var == "add" ]
    then
      create_queue
    fi
  fi
  if [[ $ARG_COUNT > 2 ]]
  then
    add_member $var
  fi
done
