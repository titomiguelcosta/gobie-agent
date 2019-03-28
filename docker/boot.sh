#!/bin/bash

php -S 0.0.0.0:7000 -t /app/public

while true
do
    echo "Press [CTRL+C] to stop.."
    sleep 1
done
