#! /bin/bash
set -e

mkdir -p /tmp/logs
chown -R 33 /tmp/logs

apache2-foreground