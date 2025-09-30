#!/bin/bash

set -e ; clear


REMOTE_HOST='criustemp.cisadmin.com'
REMOTE_DIR='/var/www/KKP'
LOCAL_DIR="$( dirname $( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd ) )"

if [ ! -d "${LOCAL_DIR}/Laravel" ]; then
	echo "Not found: ${LOCAL_DIR}/Laravel"
	exit 1
fi

#
# prep
#

echo "Setting owner/perms..."
chown -R jonesy.webusers ${LOCAL_DIR}
chmod -R g+w ${LOCAL_DIR}

#
# transfer
#

SSH_USER='jonesy'
SSH='/usr/bin/ssh -p 4022'
RSYNC='/usr/bin/rsync'
RSYNCOPTS='-a --out-format=%n --timeout=10 --delete --inplace'

export RSYNC_RSH="${SSH} -i /home/${SSH_USER}/.ssh/id_rsa"

echo -e "\nRsyncing...\n"
${RSYNC} ${RSYNCOPTS} \
	${LOCAL_DIR}/ \
	${SSH_USER}@${REMOTE_HOST}:${REMOTE_DIR}
echo
