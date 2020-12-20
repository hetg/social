#!/bin/bash
set -e

### Configuration ###

SERVER=ubuntu@hetg.net
APP_DIR=social/backend

### Library ###

function run()
{
  echo "Running: $@"
  "$@"
}


### Automation steps ###
echo "---- Running deployment script on remote server ----"
run ssh $SERVER "cd "$APP_DIR" && deploy/run.sh"
echo "---- Finished ----"
