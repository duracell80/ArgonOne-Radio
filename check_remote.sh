#!/bin/bash
#sudo rfkill unblock 0

SETS=$(ls -dq /var/lib/mpd/playlists/*preset* | wc -l)
mpc update

while :
do

	# Keypress Check Using KBD
	KEYS=$(sudo showkey -k | grep "keycode" | head -1)

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
					mpc clear
                			mpc repeat on
                			mpc load preset_$NSET
                			mpc play
				else
					mpc clear
                                        mpc repeat on
                                        mpc load preset_0
                                        mpc play
				fi
			fi
		done
	fi

	# MUTE KEY
	if [[ $KEYS == *"113"* ]]; then
                echo "Remote: Mute (Stop or Play)"
		if mpc status | awk 'NR==2' | grep playing; then
			mpc stop
		else
                        mpc play
                fi
        fi

	# NEXT STATION
        if [[ $KEYS == *"103"* ]]; then
                echo "Remote: Next Station"
		mpc play
		mpc next
        fi

	# PREV STATION
        if [[ $KEYS == *"108"* ]]; then
                echo "Remote: Next Station"
                mpc play
                mpc prev
        fi

	# VOLUME UP
        if [[ $KEYS == *"106"* ]]; then
                echo "Remote: Volume Up"
                mpc volume +5
        fi

	# VOLUME DOWN
        if [[ $KEYS == *"105"* ]]; then
                echo "Remote: Volume Down"
                mpc volume -5
        fi

	# VOLUME UP ANDROID TV REMOTE
        if [[ $KEYS == *"115"* ]]; then
                echo "Remote: Volume Up"
                mpc volume +5
        fi

        # VOLUME DOWN ANDROID TV REMOTE
        if [[ $KEYS == *"114"* ]]; then
                echo "Remote: Volume Down"
                mpc volume -5
        fi



	# BACK OR HOME KEY
        if [[ $KEYS == "keycode   1 release" ]]; then
                echo "Remote: Loading Default Playlist ..."
                mpc clear
                mpc repeat on
                mpc load preset_0
                mpc play
        fi
	
	# PLAY PAUSE TOGGLE (p)
        if [[ $KEYS == *"25"* ]]; then
                echo "Remote: Play Pause"
                mpc toggle
        fi


	# TAG NOW PLAYING
        if [[ $KEYS == *"28"* ]]; then

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
	fi

	# KILL REMOTE WATCHDOG (q)
        if [[ $KEYS == *"16"* ]]; then
                echo "Remote: Ending Remote Watchdog"
                mpc stop
		sudo pkill check_remote
		#pkill check_network
        fi

done &

while :
do
	sleep 5
	sudo pkill showkey
	echo "Secondary loop"
	
	# Basic Action Check
        read -n 1 k <&1
        if [[ $k == "p" ]] ; then
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

done&
