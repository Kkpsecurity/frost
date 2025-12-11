#!/bin/bash
if [[ $EUID -ne 0 ]]; then /usr/bin/sudo "$0" "$@"; exit; fi

LOGFILE='./storage/logs/laravel.log'

chown nginx.webusers ${LOGFILE}
chmod 664            ${LOGFILE}

