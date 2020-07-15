#!/bin/bash
mpc clear
mpc repeat on
mpc load radio_default
mpc play
mpc volume 85

PLEN=$(cat /var/lib/mpd/playlists/radio_default.m3u | wc -l)

clear    
printf "Online Radio Tuner [ ${PLEN} Stations in Memory ]\n\n"

printf "Press q to quit p to play or pause else ...\n\n"
printf "    Up            = Next\n"
printf "    Down          = Previous\n"
printf "    Left & Right  = Volume\n"
printf "    Enter         = Tag Now Playing to File \n"
printf "    Shift+S       = Stop WiFi Reconnect Auto Play \n\n\n"

while : ; do
        read -n 1 k <&1
        if [[ $k == *"A"* ]] ; then
            mpc next
        elif [[ $k == *"B"* ]] ; then
            mpc prev
        elif [[ $k == *"C"* ]] ; then
            mpc volume +5
        elif [[ $k == *"D"* ]] ; then
            mpc volume -5        
        elif [[ $k = "" ]] ; then
            NOWPLAYING=$(mpc -f "%album% %track% %title%"  | head -n 1)
            TLEN=$(mpc -f "%album% %track% %title%" | head -n 1 | wc -m)
            if [ $TLEN -lt 10 ] ; then
                  echo "Not Tagged: Missing Station Metadata"
            else
                  echo "\n\nTagged: ${NOWPLAYING}"
                  NOWDATETIME=$(date +"%D %T")
                  NOWTAGGED="[ ${NOWDATETIME} ] ${NOWPLAYING}" 
                  echo $NOWTAGGED >> tagged.txt
            fi
            
        elif [[ $k == "p" ]] ; then
            mpc toggle
        elif [[ $k == "q" ]] ; then
            break
        elif [[ $k == "S" ]] ; then
            pkill check_network
        else
            NOWPLAYING=$(mpc -f "Station [ %track% ] - %artist% - %title% - %file%"  | head -n 1)
        fi
done
