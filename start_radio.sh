#!/bin/bash
sudo rfkill unblock 0

mpc clear
mpc repeat on
mpc load preset_0
mpc play
mpc volume 85

PLEN=$(cat /var/lib/mpd/playlists/preset_0.m3u | wc -l)
WLAN=$(ifconfig wlan0 | sed -En 's/127.0.0.1//;s/.*inet (addr:)?(([0-9]*\.){3}[0-9]*).*/\2/p')
ETHO=$(ifconfig eth0 | sed -En 's/127.0.0.1//;s/.*inet (addr:)?(([0-9]*\.){3}[0-9]*).*/\2/p')

clear
printf "Online Radio Tuner [ ${PLEN} Stations in Memory - WiFi: ${WLAN} LAN: ${ETHO} ]\n\n"

printf "Press q to quit p to play or pause else ...\n\n"
printf "        Up              = Next\n"
printf "        Down            = Previous\n"
printf "        Left & Right    = Volume\n"
printf "        Enter           = Tag Now Playing to File \n"
printf "        Shift+S         = Stop WiFi Reconnect Auto Play \n"
printf "        Shift+R         = Reboot\n"
printf "        Shift+P         = Power Off \n\n\n"


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
        elif [[ $k == "R" ]] ; then
            sudo reboot
        elif [[ $k == "P" ]] ; then
            sudo shutdown -h now
        else
            NOWPLAYING=$(mpc -f "Station [ %track% ] - %artist% - %title% - %file%"  | head -n 1)
        fi
done
