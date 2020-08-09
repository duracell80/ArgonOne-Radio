<?php 
/** MOODE REMOTE API
  *
  * Lee Jordan @duracell80
  * 05/01/2020

    

*/


//header("Expires: on, 01 Jan 1970 00:00:00 GMT");
//header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
//header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


error_reporting(E_ALL);
ini_set('display_errors', 'on');

// Main Params
$dir        = $_GET["dir"];
$mod        = $_GET["mod"];
$cmd        = $_GET["cmd"];
//$src        = $_GET["src"];
//$ch         = $_GET["ch"];
$name       = $_GET["name"];


$apiPath        = "/var/www/command/remote";
$cmdPath        = "/var/www/command/";
$playlistPath   = "/var/lib/mpd/playlists";
$radioList      = $playlistPath . "/preset_0.m3u";

//$db             = new SQLite3('/var/local/www/db/moode-sqlite3.db');






// FIGURE OUT SIGNAL DIRECTION
if(isset($dir) && !empty($dir)){
    
    
    switch ($dir) { 
        // TRANSMIT
        case "tx":
            
            switch ($mod) {      
            
            case "system":
                if(isset($cmd) && !empty($cmd)){
                    switch ($cmd) {
                        case "reboot":
                            echo(shell_exec("sudo reboot"));
                            break;

                         case "off":
                            //echo(shell_exec("sudo poweroff"));
                            break;

                        default:
                            break;
                    }
                }
            break;       
                
                
            case "display":
                if(isset($cmd) && !empty($cmd)){
                    switch ($cmd) {


                        // OFF
                        case "off":
                            $runcmd = shell_exec("xset -display :0 s activate");
                            break;
                        // ON   
                        case "on":
                            $runcmd = shell_exec("xset -display :0 s reset");
                            break;
                        // PEEK   
                        case "peek":
                            $runcmd = shell_exec("xset -display :0 s reset; sleep 10; xset -display :0 s activate");
                            break;

                        // BRIGHTNESS SET
                        case "night":
                            $runcmd = shell_exec("sudo chmod 777 /sys/class/backlight/rpi_backlight/brightness");
                            $runcmd = shell_exec("echo 16 > /sys/class/backlight/rpi_backlight/brightness");
                            break;
                        // BRIGHTNESS SET
                        case "day":
                            $runcmd = shell_exec("echo 255 > /sys/class/backlight/rpi_backlight/brightness");
                            break;


                        default:
                            break;
                    }
                }    

            case "mpc":
                if(isset($cmd) && !empty($cmd)){
                    switch ($cmd) {

                        // UPDATE DATABASE
                        case "update":
                            $runcmd = "mpc update";
                            echo(shell_exec($runcmd));
                            break;

                        // STATUS
                        case "status":
                            $runrslt = shell_exec("mpc status | awk 'NR==2' | grep playing");
                            if($runrslt != ""){
                                $jsonObj->status = "playing";
                            } else {
                                $jsonObj->status = "paused";
                            }
                            
                            $jsonObj->vol = shell_exec("mpc status | sed -n '/volume/p' | cut -c8-10 | sed 's/^[ \t]*//'");
                            
                            $runrslt = shell_exec("mpc status | awk 'NR==3' | grep 'repeat: on'");
                            if($runrslt == ""){
                                $jsonObj->repeat = "off";
                            } else {
                                $jsonObj->repeat = "on";
                            }
                            
                            $runrslt = shell_exec("mpc status | awk 'NR==3' | grep 'random: on'");
                            if($runrslt == ""){
                                $jsonObj->random = "off";
                            } else {
                                $jsonObj->random = "on";
                            }
                            
                            $runrslt = shell_exec("mpc status | awk 'NR==3' | grep 'single: on'");
                            if($runrslt == ""){
                                $jsonObj->single = "off";
                            } else {
                                $jsonObj->single = "on";
                            }
                            
                            $runrslt = shell_exec("mpc status | awk 'NR==3' | grep 'consume: on'");
                            if($runrslt == ""){
                                $jsonObj->consume = "off";
                            } else {
                                $jsonObj->consume = "on";
                            }
                            
                            $json_out = json_encode($jsonObj);
                            echo($json_out);
                            
                            break; 

                        // VOLUME + 5
                        case "volup":
                            //$runcmd = "/var/www/vol.sh -up 5";
                            //echo(shell_exec($runcmd));
                            break;

                        // VOLUME -5
                        case "voldown":
                            //$runcmd = "/var/www/vol.sh -dn 5";
                            //echo(shell_exec($runcmd));
                            break;

                        // VOLUME MUTE
                        case "mute":
                            //$runcmd = "/var/www/vol.sh -mute";
                            //echo(shell_exec($runcmd));
                            break;


                        // LIST Playlists
                        case "list":
                            $runcmd = "mpc lsplaylists";
                            $runrst = shell_exec($runcmd); 
                            echo($runrst);
                            break;

                        // CROP Playlist
                        case "crop":
                            $runcmd = "mpc crop";
                            $runrst = shell_exec($runcmd); 
                            echo($runrst);
                            break;

                        // CLEAR Playlist
                        case "clear":
                            $runcmd = "mpc clear";
                            $runrst = shell_exec($runcmd); 
                            echo($runrst);
                            break;    

                        // LOAD Playlist
                        case "load":
                            $runcmd = "mpc load " . $name;
                            $runrst = shell_exec($runcmd); 
                            echo($runrst);
                            break;

                        // LOAD and PLAY Playlist
                        case "playlist":
                            shell_exec("mpc clear");
                            $runcmd = "mpc load " . $name;
                            $runrst = shell_exec($runcmd);
                            shell_exec("mpc play");

                            echo($runrst);
                            break;

                        // SHUFFLE
                        case "shuffle":
                            $runcmd = "mpc shuffle";
                            echo(shell_exec($runcmd));
                            break;

                        // CONSUME
                        case "consume":
                            $runcmd = "mpc consume";
                            echo(shell_exec($runcmd));
                            break;


                        // STOP
                        case "stop":
                            $runcmd = "mpc stop";
                            echo(shell_exec($runcmd));
                            break;

                        // PLAY
                        case "play":
                            $runcmd = "mpc play";
                            echo(shell_exec($runcmd));
                            break;

                        // PAUSE
                        case "pause":
                            $runcmd = "mpc pause-if-playing";
                            echo(shell_exec($runcmd));
                            break;

                        // TOGGLE
                        case "toggle":
                            
                            // AN ACTUAL PAUSE PLAY FOR RADIO THAT WON'T RESET THE COUNTER
                            $runrslt = shell_exec("mpc toggle | awk 'NR==2' | grep playing");
                            if($runrslt != ""){
                                $jsonObj->status = "playing";
                            } else {
                                $jsonObj->status = "paused";
                            }
                            
                            $json_out = json_encode($jsonObj);
                            echo($json_out);
                            
                            break;    

                        // PREV
                        case "prev":
                            $runcmd = "mpc prev";
                            echo(shell_exec($runcmd));
                            break;

                        // NEXT
                        case "next":
                            $runcmd = "mpc next";
                            echo(shell_exec($runcmd));
                            break;

                        // SKIP FORWARD 15s
                        case "fwd15":
                            $runcmd = "mpc seek +15";
                            echo(shell_exec($runcmd));
                            break;

                        // SKIP FORWARD 30s
                        case "fwd30":
                            $runcmd = "mpc seek +30";
                            echo(shell_exec($runcmd));
                            break;

                        // SKIP FORWARD 60s
                        case "fwd60":
                            $runcmd = "mpc seek +60";
                            echo(shell_exec($runcmd));
                            break;

                        // SKIP FORWARD 5m
                        case "fwd5m":
                            $runcmd = "mpc seek +300";
                            echo(shell_exec($runcmd));
                            break;

                        // SKIP BACK 15s
                        case "bck15":
                            $runcmd = "mpc seek -15";
                            echo(shell_exec($runcmd));
                            break;

                        // SKIP BACK 30s
                        case "bck30":
                            $runcmd = "mpc seek -30";
                            echo(shell_exec($runcmd));
                            break;

                        // SKIP BACK 60s
                        case "bck60":
                            $runcmd = "mpc seek -60";
                            echo(shell_exec($runcmd));
                            break;

                        // SKIP BACK 5m
                        case "bck5m":
                            $runcmd = "mpc seek -300";
                            echo(shell_exec($runcmd));
                            break;




                        default:
                            break;
                    }
                }
                break;

            case "cast":

                // EXAMPLE http://moode/command/remote/?dir=tx&mod=cast&src=http://ice55.securenetsystems.net/DASH7
                $m3u_content  = "#EXTM3U\n";
                $m3u_content .= "#EXTINF:-1,Cast To Moode Audio\n";
                $m3u_content .= $src;

                shell_exec("sudo touch /var/lib/mpd/playlists/Radio_Play.m3u");
                shell_exec("sudo chmod 777 /var/lib/mpd/playlists/Radio_Play.m3u");
                file_put_contents($radioList, $m3u_content); 

                $runcmd = "mpc clear; mpc load Radio_Play"; shell_exec($runcmd);
                $runcmd = "mpc play";
                echo(shell_exec($runcmd));

                sleep(2);
                break;        

            case "radio":
                // EXAMPLE http://moode/command/remote/?dir=tx&mod=radio&ch=1
                if(!$ch || $ch == ""){
                    $ch = 1;
                }

                $m3u_content    = "#EXTM3U\n";
                $stationfound   = 0;
                $results        = $db->query('SELECT station,name FROM cfg_radio WHERE id =' . $ch);

                while ($row = $results->fetchArray()) {

                    $m3u_content .= "#EXTINF:-1," . $row['name'] . "\n";
                    $m3u_content .= $row['station'];
                    $stationfound = 1;
                }



                if($stationfound == 1){
                    shell_exec("sudo touch /var/lib/mpd/playlists/Radio_Play.m3u");
                    shell_exec("sudo chmod 777 /var/lib/mpd/playlists/Radio_Play.m3u");
                    file_put_contents($radioList, $m3u_content); 

                    $runcmd = "mpc clear; mpc load Radio_Play"; shell_exec($runcmd);
                    $runcmd = "mpc play";
                    echo(shell_exec($runcmd));
                    //header("Location: /");    
                } else {
                    echo("Error: Station ID Not Found");
                }

                break;        


            exit();
            default:
                break;
            } // END TRANSMISSION
            exit();

        // RECEIVE
        case "rx":
            
            switch ($mod) {      
            
            case "stationstream":
                $jsonObj->streamfile      = shell_exec("sudo mpc -f %file% | head -1");
                $json_out = json_encode($jsonObj);
                echo($json_out);
            break;        
                    
            case "station":
                    
                    
                    $src = shell_exec("sudo mpc -f %file% | head -1");
            
                    // Set Lookup Method
                    stream_context_set_default(
                        array(
                            'http' => array(
                                'method' => 'GET',
                                'user_agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/534.30 (KHTML, like Gecko) Ubuntu/10.10 Chromium/12.0.742.112 Chrome/12.0.742.112 Safari/534.30'
                            )
                        )
                    );
                    
                     
                    
                    $header     = get_headers($src, 1);
                    $station_name = "Radio Station";
                    
                    
                    $lookup = [
                        "type" => $header["Content-Type"],
                        "server" => $header["Server"],
                    ];
                    
                    
                   
                    
                    if(isset($header["icy-name"]) && $header["icy-name"] != ""){
                        $station_name = $header["icy-name"];
                    } else {
                        
                        // Check Location for MP3 file ... echo($header["Location"]);
                        
                        if(strpos(basename($header["Location"]).PHP_EOL, "mp3") !== false){
                            $station_name = basename($header["Location"], ".mp3").PHP_EOL;
                        } else{
                            // Get Station Website URL
                            $url        = $header["Location"];
                            $parent     = parse_url($url);

                            if($header["icy-url"] != "") {
                                $lookup_url = $header["icy-url"];
                            } else {
                                $lookup_url = $parent["host"];
                            }

                            // Lookup from Website
                            function get_title($url){
                              $str = file_get_contents($url);
                              if(strlen($str)>0){
                                $str = trim(preg_replace('/\s+/', ' ', $str)); // supports line breaks inside <title>
                                preg_match("/\<title\>(.*)\<\/title\>/i",$str,$title); // ignore case
                                return $title[1];
                              }
                            }
                            $station_name = get_title($lookup_url);
                        }
                    }
                    $lookup["station"] = $station_name; 
                    $lookup["response"] = json_encode($header);
                    echo json_encode($lookup);
                    //echo json_encode($header);
                    
                    
                break;    
                    
                    
            case "status":
                if(!$cmd || $cmd == ""){$cmd = "device";}
                
                    
                if(isset($cmd) && !empty($cmd)){
                    
                    $sysinfo = explode("\n", shell_exec("sudo " . $cmdPath . "sysinfo.sh | awk '/\t/'"));
                    foreach ($sysinfo as $a) {
                        $b = explode("=", $a);
                        $device[strtolower(trim($b[0]))]=strtolower($b[1]);
                    }

                    function TimeToSec($time) {
                        $sec = 0;
                        foreach (array_reverse(explode(':', $time)) as $k => $v) $sec += pow(60, $k) * $v;
                        return $sec;
                    }
                    
                    //header("Content-Type: application/json");
                    
                    switch ($cmd) {
                        case "track":
                        $track_title    = shell_exec("sudo mpc -f %title% | head -1");
                        $track_album    = shell_exec("sudo mpc -f %album% | head -1");
                        $track_artist   = "";
                        $track_time     = shell_exec("sudo mpc -f %time% | head -1");
                        $track_percent  = shell_exec("sudo mpc status | awk 'NR==2 {print}' | grep -o '(.*)' | tr -d '(%)'");
                        $track_elapsed  = shell_exec("sudo mpc status | awk '/^\[playing\]/ { sub(/\/.+/,\"\",$3); split($3,a,/:/); print a[1]*60+a[2] }'");
                            
                        $track_remaining    = TimeToSec($track_time) - $track_elapsed;
                        $track_total        = TimeToSec($track_time);
                            
                        if($track_elapsed < 1) {
                            $track_elapsed = shell_exec("sudo mpc status | awk '/^\[paused\]/ { sub(/\/.+/,\"\",$3); split($3,a,/:/); print a[1]*60+a[2] }'");
                            $track_playing  = "no";
                            $track_paused   = "yes";
                            $track_volume   = $device["volume knob"];
                        } else {
                            $track_playing  = "yes";
                            $track_paused   = "no";
                            $track_volume   = shell_exec("mpc status | sed -n '/volume/p' | cut -c8-10 | sed 's/^[ \t]*//'");
                            $track_artist   = shell_exec("sudo mpc -f %artist% | head -1");
                        }   
                            
                        $json_out = '
                            {"track": {
                              "artist":  "'     . $track_artist.'",
                              "album":  "'      . $track_album.'",
                              "title":  "'      . $track_title.'",
                              "playing":  "'    . $track_playing.'",
                              "paused":  "'     . $track_paused.'",
                              "total":  "'      . gmdate("H:i:s", $track_total).'",
                              "elapsed":  "'    . gmdate("H:i:s", $track_elapsed).'",
                              "remaining":  "'  . gmdate("H:i:s", $track_remaining).'",
                              "percent":  "'    . $track_percent.'",
                              "volume":  "'     . $track_volume.'"
                            }}';
                            echo($json_out);
                        break; 
                         
                        case "temp":
                            $myObj->temp = $device["soc temperature"];

                            $json_out = json_encode($myObj);
                            echo($json_out);
                        break;
                        
                        case "mem":
                            $myObj->mem = $device["memory free"];

                            $json_out = json_encode($myObj);
                            echo($json_out);
                        break;
                            
                        case "vol":
                            $jsonObj->vol = $device["volume knob"];
                            
                            $json_out = json_encode($jsonObj);
                            echo($json_out);
                        break;
                        
                        case "net":
                            $jsonObj->wirelessip  = $device["wlan address"];
                            $jsonObj->wirelessmac = shell_exec("sudo cat /sys/class/net/wlan0/address");

                            $jsonObj->wiredip     = $device["ethernet address"];
                            $jsonObj->wiredmac    = shell_exec("sudo cat /sys/class/net/eth0/address");
                            $jsonObj->wiredspeed  = shell_exec("sudo cat /sys/class/net/eth0/speed");

                            $jsonObj->btctrl      = $device["bluetooth controller"];
                            $jsonObj->btpair      = $device["pairing agent"];
                            
                            $json_out = json_encode($jsonObj);
                            echo($json_out);
                        break;
                            
                        case "datausage":
                            
                            function formatBytes($size, $precision = 0){
                                $unit = ['b','kb','mb','gb','tb','pb','eb','zb','yb'];

                                for($i = 0; $size >= 1024 && $i < count($unit)-1; $i++){
                                    $size /= 1024;
                                }

                                return round($size, $precision).' '.$unit[$i];
                            }

                            
                            $wired          = 0;
                            $wireless       = 0;
                            $wired          = shell_exec("sudo cat /sys/class/net/eth0/statistics/rx_bytes");
                            $wireless       = shell_exec("sudo cat /sys/class/net/wlan0/statistics/rx_bytes");    
                            
                            if($wired > $wireless){
                                $jsonObj->bytes = $wired;
                                $jsonObj->dataformat  = formatBytes($wired, 2);
                            } else {
                                $jsonObj->bytes = $wireless;
                                $jsonObj->dataformat  = formatBytes($wireless, 2);
                            }
                            
                            $jsonObj->wirelessip  = shell_exec("sudo ip addr list wlan0 | grep inet | cut -d' ' -f6|cut -d/ -f1 | head -1");
                            $jsonObj->wiredip     = shell_exec("sudo ip addr list eth0 | grep inet | cut -d' ' -f6|cut -d/ -f1 | head -1");
                            
                            $json_out = json_encode($jsonObj);
                            echo($json_out);    
                            break;
                            
                        case "services":
                            $jsonObj->spotify     = $device["spotify receiver"];
                            $jsonObj->airplay     = $device["airplay receiver"];
                            $jsonObj->squeezelite = $device["squeezelite"];
                            $jsonObj->upnp        = $device["upnp client"];
                            
                            $json_out = json_encode($jsonObj);
                            echo($json_out);
                        break;
                            
                        case "display":
                            $jsonObj->lcd         = $device["local ui display"];
                            $jsonObj->lcdbr       = $device["brightness"];

                            $json_out = json_encode($jsonObj);
                            echo($json_out);
                        break;
                            
                        default:
                       
                            $jsonObj->mem         = trim($device["memory free"]);
                            $jsonObj->temp        = trim($device["soc temperature"]);
                            $jsonObj->vol         = trim($device["volume knob"]);
                            $jsonObj->wirelessip= shell_exec("sudo ip addr list wlan0 | grep inet | cut -d' ' -f6|cut -d/ -f1 | head -1");
                            $jsonObj->wiredip   = shell_exec("sudo ip addr list eth0 | grep inet | cut -d' ' -f6|cut -d/ -f1 | head -1");
                            $jsonObj->btctrl      = trim($device["bluetooth controller"]);
                            $jsonObj->btpair      = trim($device["pairing agent"]);
                            $jsonObj->spotify     = trim($device["spotify receiver"]);
                            $jsonObj->airplay     = trim($device["airplay receiver"]);
                            $jsonObj->squeezelite = trim($device["squeezelite"]);
                            $jsonObj->upnp        = trim($device["upnp client"]);
                            $jsonObj->lcd         = trim($device["local ui display"]);
                            $jsonObj->lcdbr       = trim($device["brightness"]);

                            $json_out = json_encode($jsonObj);
                            echo($json_out);
                            
                            
                        
                            
                        break;
                    }
                }
            break;          
                    
                    
                    
            case "system":
                if(isset($cmd) && !empty($cmd)){
                    switch ($cmd) {
                        case "temp":
                            echo(shell_exec("sudo usermod -G video www-data"));
                            echo(shell_exec("sudo /opt/vc/bin/vcgencmd measure_temp"));
                            break;
                        default:
                            break;
                    }
                }
            break;           
                    
                
            case "mpc":
                if(isset($cmd) && !empty($cmd)){
                    switch ($cmd) {
                        
                            
                        // LOAD and return the contents of a playlist as a download
                        case "playlist":
                            
                            $playlistFile = $playlistPath . "/" . $name . ".m3u";
                            $playlistName = $name . ".m3u";
        
                            if (file_exists($playlistFile)) {
                                header("Content-type: text/plain");
                                header("Content-Disposition: attachment; filename=".$playlistName);
                                echo(file_get_contents($playlistFile));
                            } else {
                                header("HTTP/1.0 404 Not Found");
                            }
                            break;

                        default:
                            break;
                    }
                }    
            break;
                    
                    
                    
            case "radio":
                    
                
                // RETURN a radio station via channel number to the browser as a playable stream   
                
                if(!$ch || $ch == ""){$ch = 1;}  
                if(!$cmd || $cmd == ""){$cmd = "listen";}  
                    
                $m3u_content    = "#EXTM3U\n";
                $stationfound   = 0;
                $results        = $db->query('SELECT station,name FROM cfg_radio WHERE id =' . $ch);
                

                while ($row = $results->fetchArray()) {
                    $m3u_content .= "#EXTINF:-1," . $row['name'] . "\n";
                    $m3u_content .= $row['station'];
                    $stationfound = 1;
                    $playlistName = $row['name'] . ".m3u";
                    $playfileURL  = $row['station'];
                    
                    // GET THE HEADERS
                    print_r(get_headers($playfileURL, 1));
                    $stationHeaders = get_headers($playfileURL, 1);
                    
                    
                    if (in_array("audio/mpeg", $stationHeaders)) {
                        $playfileName = $row['name'] . ".mp3";
                        $playfileType = "audio/mpeg";
                    }
                    
                    if (in_array("audio/aac", $stationHeaders)) {
                        $playfileName = $row['name'] . ".aac";
                        $playfileType = "audio/aac";
                    }
                    
                    if (in_array("audio/flac", $stationHeaders)) {
                        $playfileName = $row['name'] . ".flac";
                        $playfileType = "audio/flac";
                    }
                    
                    if (in_array("audio/vorbis", $stationHeaders)) {
                        $playfileName = $row['name'] . ".ogg";
                        $playfileType = "audio/vorbis";
                    }
                    
                    if (in_array("audio/ogg", $stationHeaders)) {
                        $playfileName = $row['name'] . ".ogg";
                        $playfileType = "audio/ogg";
                    }
            
                    
                    
                }
                    
    
                if($stationfound == 1){    
                    
                    if(isset($cmd) && !empty($cmd)){
                        
                        
                        switch ($cmd) {
                            case "download":
                                header("Content-type: text/plain");
                                header("Content-Disposition: attachment; filename=". $playlistName);
                                echo($m3u_content);  
                                break;
                                
                            

                            default:
                                // STREAM THE RADIO (So Simple it's brilliant)
                                header("Content-Type: " . $playfileType);
                                header("Location: ". $playfileURL,TRUE,302);
                                break;
                        }
                    }
                    
                    
                
                   
                } else {
                    header("HTTP/1.0 404 Not Found");
                }
                    
                

                break;          







        exit();
        default:
            break;
        } // END RECEPTION

    default:
        break;                
    } // END SIGNAL CASE        
            
            
} // END DIRECTION CHECK
exit();
?>
