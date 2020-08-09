function secondsTimeSpanToHMS(s) {
    var h = Math.floor(s/3600); //Get whole hours
    s -= h*3600;
    var m = Math.floor(s/60); //Get remaining minutes
    s -= m*60;
    return h+":"+(m < 10 ? '0'+m : m)+":"+(s < 10 ? '0'+s : s); //zero padding on minutes and seconds
}


function engineSys() {    
    $.ajax({
        type: 'GET',
        url: '/status/',
        dataType: 'json',
        async: true,
        cache: false,
        success: function(data) {    
            //console.log("status check: succeeded");
            
            
            
            
            $('.meta .elapsed').text(secondsTimeSpanToHMS(data.elapsed));
            $('.meta .station').text(data.station);
            $('.meta .playing').text(data.playing);
            $('.sys .temp .value').text(data.temp);
            $('.sys .volume .value').text(data.volume + "%");
            
            
            var etho = data.eth.split(":");
            var wlan = data.wlan.split(":");
            
            
            $('.network .wired .addr').text(etho[0]);
            $('.network .wired .bandwidth').text(etho[1]);
            
            $('.network .wireless .addr').text(wlan[0]);
            $('.network .wireless .bandwidth').text(wlan[1]);
            
            $('.sys .data .value').text(etho[1]);
            
            setTimeout(engineSys, 1000);

        },
        complete: function (data) {
            //console.log("status check: completed");
            
        },
        error: function(data) {
            //setTimeout(engineSys, 2000);
            
            $('.meta .elapsed').text("Reload");
            $('.meta .station').text("");
            $('.meta .playing').text("Radio Stopped");
            
            //console.log("status check: failed");
            //window.location.href="/";
            
        }
    });
}