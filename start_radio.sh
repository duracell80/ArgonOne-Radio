#!/bin/bash
sudo rfkill unblock 0

clear


PLEN=$(cat /var/lib/mpd/playlists/preset_0.m3u | wc -l)
WLAN=$(ifconfig wlan0 | sed -En 's/127.0.0.1//;s/.*inet (addr:)?(([0-9]*\.){3}[0-9]*).*/\2/p')
ETHO=$(ifconfig eth0 | sed -En 's/127.0.0.1//;s/.*inet (addr:)?(([0-9]*\.){3}[0-9]*).*/\2/p')
YOLO=$(mpc status | sed -n '/volume/p' | cut -c8-10 | sed 's/^[ \t]*//')

NOWTEMP=$(vcgencmd measure_temp)
NOWDATA=$(ifconfig wlan0 | grep "RX" | head -1)
IFS='('
        read -ra DATABITS <<< "$NOWDATA"
printf "Online Radio Tuner \n[ vol=${YOLO} wlan=${WLAN} eth=${ETHO} ${NOWTEMP} data=(${DATABITS[1]} ]\n\n"
printf "	Q		= quit\n"
printf "        Up & Down       = Next Prev\n"
printf "        Left & Right    = Volume\n"
printf "	Remote Mute Key	= Play Pause\n"
printf "        Remote Menu Key	= Change Preset Stations\n"
printf "	Remote OK Key	= Tag now playing to file\n\n\n"

NOWNAME=$(mpc -f %name% | head -n 1)
printf "Now Playing: $NOWNAME\n\n\n"


SPEAKWLAN=$(echo $WLAN | sed 's/.\{1\}/& /g')
IFS='='
        read -ra TEMPBITS <<< "$NOWTEMP"

mpc volume 85

ispeak(){
        V=$(mpc status | sed -n '/volume/p' | cut -c8-10 | sed 's/^[ \t]*//')
	U=$((V + 0))
	while [[ $V -gt 20 ]] ; do
                V=$(($V - 5))
		mpc -q volume ${V}
                sleep 0.1
        done ;

        pico2wave -w ~/pa.wav "$1" && aplay ~/pa.wav

        while [[ $V -lt $U ]] ; do
                V=$(($V + 5))
		mpc -q volume ${V}
		sleep 0.1
        done ;
}


ispeak "Welcome, your wireless address is ${SPEAKWLAN} and the temperature is ${TEMPBITS[1]}"


mpc clear
mpc repeat on
mpc load preset_0
mpc -q play



SETS=$(ls -dq /var/lib/mpd/playlists/*preset* | wc -l)
mpc -q update

while :
do

	# Keypress Check Using KBD
	KEYS=$(sudo showkey -k | grep "keycode" | head -1)
	NOWPLAYING=$(mpc -f "%album% %track% %title%"  | head -n 1)

	# KILL THIS SCRIPT
        if [[ $KEYS == *"172"* ]]; then
                echo "Remote: Exit Remote Control"
                mpc stop
		pkill start_radio.sh
        fi

	# TAG NOW PLAYING
        if [[ $KEYS == *"28"* ]]; then
                echo "Remote: Remember Now Playing"
                NOWPLAYING=$(mpc -f "%album% %track% %title%"  | head -n 1)
                TLEN=$(mpc -f "%album% %track% %title%" | head -n 1 | wc -m)
                if [ $TLEN -lt 10 ] ; then
                  echo "Not Tagged: Missing Station Metadata"
			SNAME=$(mpc -f %name% | head -n 1)
                        ispeak "You are listening to ${SNAME}"

                else
                  echo "\n\nTagged: ${NOWPLAYING}"
                  NOWDATETIME=$(date +"%D %T")
                  NOWTAGGED="[ ${NOWDATETIME} ] ${NOWPLAYING}"
                  echo $NOWTAGGED >> tagged.txt
			ispeak "Now Playing ${NOWPLAYING}"
                fi
        fi




	# MENU KEY
	if [[ $KEYS == *"127"* ]]; then
  		echo "Remote: Cycling Presets ($SETS in memory)"

		CURRENT="$(mpc -f "%file%" playlist | sha512sum )"
		mpc lsplaylist | while read line
		do
 			i="$(mpc -f "%file%" playlist $line | sha512sum )"
 			if [ "$i" = "$CURRENT" ]; then
    				IFS='_' read -ra pset <<< "$line"
				#echo "Current preset is ${pset[1]}"

				CSET=${pset[1]}
				NSET=$((CSET+1))

 				if [[ "$NSET" -lt "$SETS" ]]; then
					echo "Loading Preset ${NSET}"
                        		ispeak "Loading Station Set ${NSET}"
					mpc clear
                			mpc repeat on
                			mpc load preset_$NSET
                			mpc -q play
					SNAME=$(mpc -f %name% | head -n 1)
                			ispeak "Now Tuned To ${SNAME}"
				else
                                        echo "Loading Preset 0"
					ispeak "Loading Default Stations"
                                        mpc clear
                                        mpc repeat on
                                        mpc load preset_0
                                        mpc -q play
					SNAME=$(mpc -f %name% | head -n 1)
                			ispeak "Now Tuned To ${SNAME}"
				fi
			fi
		done
	fi

	# MUTE KEY
	if [[ $KEYS == *"113"* ]]; then
                echo "Remote: Mute (Stop or Play)"
		if mpc status | awk 'NR==2' | grep playing; then
			ispeak "Radio Playback Stopping"
			mpc -q stop
		else
                        NOWDATA=$(ifconfig wlan0 | grep "RX" | head -1)
			IFS='('
        			read -ra DATABITS <<< "$NOWDATA"
			mpc -q volume 85
			ispeak "Welcome Back. ${DATABITS[1]} of data used."
			mpc -q play
                fi
        fi

	# NEXT STATION
        if [[ $KEYS == *"103"* ]]; then
                echo "Remote: Next Station"
		mpc -q play
		mpc -q next

		SNAME=$(mpc -f %name% | head -n 1)
                ispeak "Now Tuned To ${SNAME}"

		sleep 2
		NOWPLAYING=$(mpc -f "%album% %track% %title%"  | head -n 1)
                TLEN=$(mpc -f "%album% %track% %title%" | head -n 1 | wc -m)
                if [ $TLEN -lt 10 ] ; then
                  echo "Now Playing: Missing Station Metadata"
                else
                  echo "\n\nYou are listening to: ${NOWPLAYING}"
                        ispeak "You are listening to ${NOWPLAYING}"
                fi

        fi

	# PREV STATION
        if [[ $KEYS == *"108"* ]]; then
                echo "Remote: Previous Station"
                mpc -q play
                mpc -q prev

                SNAME=$(mpc -f %name% | head -n 1)
                ispeak "Now Tuned To ${SNAME}"

        fi

	# VOLUME UP
        if [[ $KEYS == *"106"* ]]; then
                echo "Remote: Volume Up 10%"
                mpc -q volume +10
        fi

	# VOLUME DOWN
        if [[ $KEYS == *"105"* ]]; then
                echo "Remote: Volume Down 10%"
                mpc -q volume -10
        fi

	# VOLUME UP ANDROID TV REMOTE
        if [[ $KEYS == *"115"* ]]; then
                echo "Remote: Next Station"
                mpc -q play
		mpc -q next
        fi

        # VOLUME DOWN ANDROID TV REMOTE
        if [[ $KEYS == *"114"* ]]; then
                echo "Remote: Previous Station"
                mpc play
		mpc prev
        fi



	# BACK OR HOME KEY
        if [[ $KEYS == "keycode   1 release" ]]; then
                echo "Remote: Loading Default Playlist ..."
                mpc clear
                mpc repeat on
                mpc load preset_0
                mpc -q play
        fi

	# PLAY PAUSE TOGGLE (p)
        if [[ $KEYS == *"25"* ]]; then
                echo "Remote: Play Pause"
                mpc -q toggle
        fi

	# KILL REMOTE WATCHDOG (q)
        if [[ $KEYS == *"16"* ]]; then
                echo "Remote: Exit Remote Control"
                mpc -q stop
		sudo pkill start_radio.sh
		pkill check_network.sh
        fi

done &

while :
do
	# SIGTERM SHOWKEY TO SPEED UP REMOTE CONTROL RESPONSIVENESS
	sleep 0.2
	sudo pkill showkey

	YOLO=$(mpc status | sed -n '/volume/p' | cut -c8-10 | sed 's/^[ \t]*//')
	NOWTEMP=$(vcgencmd measure_temp)
	NOWDATA=$(ifconfig wlan0 | grep "RX" | head -1)


	IFS='('
	read -ra DATABITS <<< "$NOWDATA"

	clear
	printf "Online Radio Tuner \n[ vol=${YOLO} wlan=${WLAN} eth=${ETHO} ${NOWTEMP} data=(${DATABITS[1]} ]\n\n"

	printf "        Q               = quit\n"
	printf "        Up & Down       = Next Prev\n"
	printf "        Left & Right    = Volume\n"
	printf "        Remote Mute Key = Play Pause\n"
	printf "        Remote Menu Key = Change Preset Stations\n"
	printf "        Remote OK Key   = Tag now playing to file\n\n\n"


	NOWNAME=$(mpc -f %name% | head -n 1)
	NOWPLAYING=$(mpc -f "%album% %track% %title%"  | head -n 1)
	printf "Now Playing: $NOWNAME - $NOWPLAYING \n\n\n"

done&
