#!/bin/bash
# This script runs AFTER the deployment goes to /var/app/current in AL2023

# Gives permission for the PHP user (webapp) to write to the logs and sessions
#chown -R webapp:webapp /var/app/current/storage
#chmod -R 775 /var/app/current/storage

#chmod 775 /var/app/current/storage/logs
#chmod 775 /var/app/current/storage/sessions

#chmod g+s /var/app/current/storage/sessions

#echo "Storage permissions successfully applied for PHP Vanilla!"

#chown -R webapp:webapp /var/app/current/storage
#chmod -R 775 /var/app/current/storage
date "+%d/%m/%Y %H:%M:%S" >> /var/app/current/storage/deploy_date.txt
