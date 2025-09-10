#!/bin/bash

set -e ; clear


SCRIPTDIR="$( dirname "$( realpath "${BASH_SOURCE[0]}" )" )"
source $SCRIPTDIR/../docs/xfer-lib.sh

# double-check
if [ ! -f $SSH_USER_KEY ]; then echo "Not found: $SSH_USER_KEY"; exit 1; fi

LOCAL_DB=frost-devel
DUMPFILE=$SCRIPTDIR/_$REMOTE_DB.sql
SESSIONFILE=$SCRIPTDIR/_sessions.sql


#
# cli args
#


for ARG in "$@"; do

	if [ "$ARG" == '+br' ]; then DO_BACKUP_REMOTE=1; fi
	if [ "$ARG" == '+pg' ]; then DO_PURGE_TABLES=1;  fi

	if [ "$ARG" == '-nb' ]; then SKIP_BACKUP_REMOTE=1; fi
	if [ "$ARG" == '-np' ]; then SKIP_PURGE_TABLES=1;  fi

	if [[ "$ARG" == '-a' ]] || [[ "$ARG" == '--all' ]]; then
		DO_BACKUP_REMOTE=1
		DO_PURGE_TABLES=1
	fi

done



#
# functions
#


backup_remote()
{

	if [ ! -e $DUMPFILE ]; then
		touch $DUMPFILE
		chgrp webusers $DUMPFILE
		chmod 0664     $DUMPFILE
	fi

	echo -e "\nBacking up remote database"
	$SSH_BIN -i $SSH_USER_KEY $REMOTE_USER@$REMOTE_HOST "/usr/bin/pg_dump $REMOTE_DB > $REMOTE_HOME/$REMOTE_DB.sql"

	echo -e "\nCopying remote database to ${DUMPFILE}"
	$SCP_BIN -i $SSH_USER_KEY $REMOTE_USER@$REMOTE_HOST:$REMOTE_HOME/$REMOTE_DB.sql $DUMPFILE

}


purge_tables()
{

	echo

	for TABLE in failed_jobs jobs job_batches; do
		echo "  Cleaning table: $TABLE"
		echo "TRUNCATE ${TABLE} RESTART IDENTITY;" | $PSQL_BIN -q $LOCAL_DB
	done

}


#
# backup local sesssions table
#


cat << EOM
*******************************
Backing up local sessions table
*******************************

EOM

$PGDUMP_BIN -at sessions $LOCAL_DB | sed '/^SE/d' > $SESSIONFILE

echo


#
# backup remote
#


if [ ! -z $SKIP_BACKUP_REMOTE ]; then

	echo "CLI: Skipping backing up remote"

elif [ ! -z $DO_BACKUP_REMOTE ]; then

	backup_remote

else

	while true; do

		read -p "Backup remote? [y/n/x] " answer

		case $answer in
			[Yy]* ) backup_remote; break;;
			[Nn]* ) break;;
			[Xx]* ) exit;;
		esac

	done

fi


#
# recreate and load
#


echo -e "\nRecreating Database $LOCAL_DB"
sed "s/DBNAME/$LOCAL_DB/g" $SCRIPTDIR/_createDB.sql | $PSQL_BIN -q template1

echo -e "\nLoading database on local server"
$PSQL_BIN -qf $DUMPFILE $LOCAL_DB

echo


#
# purge tables
#


if [ ! -z $SKIP_PURGE_TABLES ]; then

	echo "CLI: Skipping purging tables"

elif [ ! -z $DO_PURGE_TABLES ]; then

	purge_tables

else

	while true; do

		read -p "Purge tables? [y/n] " answer

		case $answer in
			[Yy]* ) purge_tables; break;;
			[Nn]* ) break;;
		esac

	done

fi


echo



#
# restore local sesssions table
#


cat << EOM
******************************
Restoring local sessions table
******************************

EOM

echo 'TRUNCATE sessions;' | $PSQL_BIN -q $LOCAL_DB
$PSQL_BIN -qf $SESSIONFILE $LOCAL_DB

echo



#
# local record updates
#


source $SCRIPTDIR/post-sync.sh


#
# flush redis cache
#


$SCRIPTDIR/flush-redis.sh
