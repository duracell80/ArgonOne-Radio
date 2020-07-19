#!/bin/bash

CURRENT="$(mpc -f "%file%" playlist | sha512sum )"
mpc lsplaylist | while read line
do
 i="$(mpc -f "%file%" playlist $line | sha512sum )"
 if [ "$i" = "$CURRENT" ]; then
    echo "Current playlist is $line"
 fi
done
