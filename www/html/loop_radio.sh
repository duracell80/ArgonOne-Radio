#!/bin/bash

PLEN=$(cat /var/lib/mpd/playlists/preset_0.m3u | wc -l)

while :
do
	WLAN=$(ifconfig wlan0 | sed -En 's/127.0.0.1//;s/.*inet (addr:)?(([0-9]*\.){3}[0-9]*).*/\2/p')
    ETHO=$(ifconfig eth0 | sed -En 's/127.0.0.1//;s/.*inet (addr:)?(([0-9]*\.){3}[0-9]*).*/\2/p')
    
    # SIGTERM SHOWKEY TO SPEED UP REMOTE CONTROL RESPONSIVENESS
	sleep 0.75
	sudo pkill showkey

	YOLO=$(mpc status | sed -n '/volume/p' | cut -c8-10 | sed 's/^[ \t]*//')
	NOWTEMP=$(vcgencmd measure_temp)
	WNOWDATA=$(ifconfig wlan0 | grep "RX" | head -1)
	IFS='('
	read -ra WDATABITS <<< "$WNOWDATA"
    
    ENOWDATA=$(ifconfig eth0 | grep "RX" | head -1)
	IFS='('
	read -ra EDATABITS <<< "$ENOWDATA"

	clear
	printf "Online Radio Tuner \n[ vol=${YOLO} wlan=${WLAN} eth=${ETHO} ${NOWTEMP} data=(${WDATABITS[1]} ]\n\n"

	printf "        Q               = quit\n"
	printf "        Up & Down       = Next Prev\n"
	printf "        Left & Right    = Volume\n"
	printf "        Remote Mute Key = Play Pause\n"
	printf "        Remote Menu Key = Change Preset Stations\n"
	printf "        Remote OK Key   = Tag now playing to file\n\n\n"


	NOWNAME=$(mpc -f %name% | head -n 1)
	NOWPLAYING=$(mpc -f "%album% %track% %title%"  | head -n 1 | awk '{gsub(/^ +| +$/,"")} {print $0}')
    NOWELAPSED=$(mpc status | awk '/^\[playing\]/ { sub(/\/.+/,"",$3); split($3,a,/:/); print a[1]*60+a[2] }')
	printf "Now Playing: $NOWNAME - $NOWPLAYING \n\n\n"
    
    printf "station=${NOWNAME}\nplaying=${NOWPLAYING}\nelapsed=${NOWELAPSED}\nvolume=${YOLO}\nwlan=${WLAN}-${DATABITS[1]//)}\neth=${ETHO}-${EDATABITS[1]//)}\n${NOWTEMP//temp=}" > /var/www/html/status.txt
    
printf '{
    "station":"'${NOWNAME}'", 
    "playing": "'${NOWPLAYING}'",
    "elapsed": "'${NOWELAPSED}'",
    "volume": "'${YOLO}'",
    "wlan": "'${WLAN}':'${DATABITS[1]//)}'",
    "eth": "'${ETHO}':'${EDATABITS[1]//)}'",
    "temp": "'${NOWTEMP//temp=}'"
}' > /var/www/html/status.json
    
done
