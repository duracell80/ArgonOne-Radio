#!/bin/bash
#sudo rfkill unblock 0

SETS=$(ls -dq /var/lib/mpd/playlists/*preset* | wc -l)
mpc update

while :
do
	KEYS=$(sudo showkey -k | grep "keycode" | head -1)

	# MENU KEY
	if [[ $KEYS == *"127"* ]]; then
  		echo "Remote: Cycling Preset ($SETS)"

		CURRENT="$(mpc -f "%file%" playlist | sha512sum )"
		mpc lsplaylist | while read line
		do
 			i="$(mpc -f "%file%" playlist $line | sha512sum )"
 			if [ "$i" = "$CURRENT" ]; then
    				IFS='_' read -ra pset <<< "$line"
				echo "Current preset is ${pset[1]}"
 			fi
		done

		mpc clear
                mpc repeat on
                mpc load preset_1
                mpc play
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

	# BACK or HOME KEY
        if [[ $KEYS == "keycode   1 release" ]]; then
                echo "Remote: Loading Default Playlist ..."
		mpc clear
		mpc repeat on
		mpc load radio_default
		mpc play
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
	sleep 1
	sudo pkill showkey
	echo "Secondary loop"
done

#clear
#printf "Online Radio Tuner [ ${PLEN} Stations in Memory - WiFi: ${WLAN} LAN: ${ETHO} ]\n\n"
