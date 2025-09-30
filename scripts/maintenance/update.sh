#!/bin/bash
#
# sudo mkdir -m 0775 /var/www/KKP && sudo chown jonesy.webusers /var/www/KKP
# scp -rP 4022 jonesy@criustemp.cisadmin.com:/var/www/KKP/* /var/www/KKP/ && /var/www/KKP/scripts/update.sh
#

set -e


REMOTE_HOST='criustemp.cisadmin.com'
REMOTE_DIR='/var/www/KKP'
LOCAL_DIR="$( dirname $( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd ) )"

if [ ! -d "${LOCAL_DIR}/Laravel" ]; then
	echo "Not found: ${LOCAL_DIR}/Laravel"
	exit 1
fi

#
# transfer
#

SSH_USER='jonesy'
SSH='/usr/bin/ssh -p 4022'
RSYNC='/usr/bin/rsync'
RSYNCOPTS='-a --out-format=%n --timeout=10 --delete --inplace'

export RSYNC_RSH="${SSH} -i /home/${SSH_USER}/.ssh/id_rsa"

${RSYNC} ${RSYNCOPTS} \
	${SSH_USER}@${REMOTE_HOST}:${REMOTE_DIR}/ \
	${LOCAL_DIR}
