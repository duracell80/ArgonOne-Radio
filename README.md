# Argon One Case Internet Radio

A script to turn the ArgonOne Raspberry Pi Case into a headless internet radio. The power button is reconfigured to provide "skip to next station" upon double press. MPD is used to auto play stations from one default playlist. The aim is to be as barebones as possible without the need for Mopidy, Volumio or Moode.

## Features
- WiFi keep alive, when using in car and phone hotspot goes down, resume MPC on reconnect!!
- MPC auto plays a default playlist of stations within about a minute of bootup
- MPC is set to auto repeat all stations at 85% volume
- Stations managed by .m3u file accessible via file share or in /var/lib/mpd/playlists
- Double press power button to skip to next station
- Hold power button for about 4 seconds to power off
- When off press power button once to turn on

![Argon One Case](https://i.pcmag.com/imagery/articles/01P55E1Jzz6NT5bZBlIexMv-1.fit_scale.size_2698x1517.v1588677265.jpg)


## Ready the SD Card
- Flash a version of Raspbian Lite to a micro SD card.
- Hook up an ethernet cable, headphones, monitor, keyboard and mouse to the Argon One, insert the card and power on
- Login with user pi and password raspberry

## Set Wi-Fi Country, Network and SSH
Set the Pi up, change the locale, activate SSH and anything else you would normally do in raspi-config, if you know you won't need audio over HDMI go ahead in advanced options > audio and force 3.5mm.
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

## Adjusting the Volume
Via SSH or directly with a keyboard use the commands:
```
Volume 85%
$ mpc volume 85

Volume 25%
$ mpc volume 25
```

## Using an MPD Client
I use the M.A.L.P MPD client to interact with the radio. If using a phone's hotspot feature in car using MALP is highly recommended. I also use Android phones and Termux is an excellent app for SSH into the Pi from the phone. Using nmap can find the address of the Pi.

- Download the app
- Add a profile using your IP
- For example in the car my Pi's address is 192.168.43.51
- Over Ethernet my IP address is 192.168.2.58

## Add and Remove Stations
Obtain the Pi's IP address and use this to connect to the fileshare. For example on Windows 10, hit start and type `\\192.168.2.58\` enter pi as the username and raspberry as the password. You now have access to the MPD playlists folder via the "radio" folder. Edit the file in there to add or remove stations.

Example format:
```
#EXTM3U
#EXTINF:-1,Cafe Del Mar Radio
https://streams.radio.co/se1a320b47/listen
```

## Usage in a car
The M.A.L.P MPD client really is excellent on a phone, this will let you hide the Pi in a glove box or center console. 

As long as you have a USB power supply and an AUX in port for your car's audio system, you'll be able to hook up the Argon One to the car. 

Upon pressing the power button wait for a station to start playing. If there's no audio, make sure you have your phone's hotspot turned on and that you've managed previously to connect to the WiFi hotspot using this Pi. I found the 5GhZ band to be smoother and free from network congestion. Avoid setting any of your home's WiFi networks in raspi-config as this may stop your phone's hotspot from being picked when leaving home. Use the Ethernet connection in the house.

Note: Always keep your attention on the road, this one button operation is meant to be as distraction free as possible. I stayed away from Bluetooth because of wanting a set and forget system in the car.


## ToDo (suggestions welcome)
- Single press to skip forward
- Double press to skip back
- Config option to download one episode of a podcast to play as first playlist item
- GPIO pins for a button HAT (possiblity of different "presets" or "station memory" for different .m3u files)
