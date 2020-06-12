# Argon One Radio

A script to turn the ArgonOne Raspberri Pi Case into a headless internet radio. The power button is reconfigured to provide "skip to next station" upon double press.

## Features
- When off press power button once
- MPC auto plays a default playlist of stations within about a minute of bootup
- MPC is set to auto repeat all stations
- Stations managed by m3u file accessible via file share
- Double press power button to skip to next station
- Hold power button for about 4 seconds to power off

## Ready the SD Card
- Flash a version of Raspbian Lite to a micro SD card.
- Hook up an ethernet cable, headphones, monitor, keyboard and mouse to the Argon One, insert the card and power on
- Login with user pi and password raspberry

## Set Wi-Fi Country, Network and SSH
Set the Pi up, change the locale, activate SSH and anything else you would normally do in raspi-config, if you know you won;t need audio over HDMI go ahead in advanced options > audio and force 3.5mm.
```
$ sudo raspi-config
```
Tip: If using in car set the WiFi as the SSID of your phone's hotspot feature, then use the Ethernet connection in the house to access the Pi. For now continue on the Ethernet connection, use ifconfig to find the IP address to connect via SSH from this point forward.

## Install
```
$ sudo apt-get update
$ sudo apt-get install git -y
$ git clone https://github.com/duracell80/ArgonOne-Radio.git
$ cd ArgonOne-Radio
$ chmod +x *.sh

$ ./install.sh
```

The script will run, if promoted by Samba's install say No to WINS and enter a password for the pi user such as "raspberry". You can then use this samba share to access and edit the radio playlist which contains the stations located in /var/lib/mpd/playlists. Wait for the Pi to reboot and listen for the first station in the sample playlist to start playing.

To advance to the next station double press the power button. To shutdown the pi, hold the power button for about 4 or 5 seconds.
