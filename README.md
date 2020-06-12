# Argon One Radio

A script to turn the ArgonOne Raspberri Pi Case into a headless internet radio. The power button is reconfigured to provide "skip to next station" upon double press.

## Features
- When off press power button once
- MPC auto plays a default playlist of stations within about a minute of bootup
- MPC is set to auto repeat all stations
- Stations managed by m3u file accessible via file share
- Double press power button to skip to next station
- Hold power button for about 4 seconds to power off

## Install
- Flash a version of Raspbian Lite to a micro SD card.
- Hook up an ethernet cable, monitor, keyboard and mouse to the pi, insert the card and power on
- Login with user pi and password raspberry

## Set Wi-Fi Country, Network and SSH
Set the Pi up, change the locale and anything else you would normally do in raspi-config.
```
$ sudo raspi-config
```
Tip: If using in car set the WiFi as the SSID of your phone's hotspot feature, then use the Ethernet connection in the house to access the pi.
