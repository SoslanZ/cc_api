#!/bin/bash

EXT_FILE_PATH=queue.conf
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
" >> $EXT_FILE_PATH
}

function delete_queue {
  sed -i '/\['$QUEUE'\]/,/^$/ { /.*/d }' $EXT_FILE_PATH
}

function add_member {
  sed -i '/\['$QUEUE'\]/,/^$/!b;/\['$QUEUE'\]/amember=Local/'$1'@from-queue/n,0' $EXT_FILE_PATH
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