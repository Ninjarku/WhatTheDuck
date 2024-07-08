#!/bin/bash

source /home/backupuser/config/backup_config.sh

TIMESTAMP=$(date +"%F-%H%M")
REMOTE_DIR="/home/backupuser/sqlbackup"
BACKUP_DIR="/home/backupuser/sqlbackup"

mysqldump --defaults-file=/home/backupuser/pvt/.my.cnf $MYSQL_DATABASE > $BACKUP_DIR/db-backup-$TIMESTAMP.sql

scp -i "$SSH_KEY" $BACKUP_DIR/db-backup-$TIMESTAMP.sql "$REMOTE_USER@$REMOTE_IP:$REMOTE_DIR/"
