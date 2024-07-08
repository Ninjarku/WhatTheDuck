#!/bin/bash

source /home/backupuser/config/backup_config.sh

LOG_DIR="/var/lib/jenkins/logs"     
DEST_DIR="/home/backupuser/tmp"     
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")  
REMOTE_DIR="/home/backupuser/logs/jenkins"

mkdir -p "$DEST_DIR"

ZIP_FILE="logs_backup_$TIMESTAMP.zip"
(cd "$LOG_DIR" && zip -r "$DEST_DIR/$ZIP_FILE" .)

echo "Directory $LOG_DIR has been zipped as $ZIP_FILE."

ssh -i "$SSH_KEY" "$REMOTE_USER@$REMOTE_IP" "mkdir -p $REMOTE_DIR"

scp -i "$SSH_KEY" "$DEST_DIR/$ZIP_FILE" "$REMOTE_USER@$REMOTE_IP:$REMOTE_DIR/"

if [ $? -eq 0 ]; then
    echo "Transfer successful for $ZIP_FILE, deleting local zipped file."
    rm "$DEST_DIR/$ZIP_FILE"
else
    echo "Transfer failed for $ZIP_FILE, not deleting local zipped file."
fi
