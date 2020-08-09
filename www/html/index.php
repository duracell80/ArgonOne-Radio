<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>MPD Touch</title>

    
    <link href="/css/bootstrap-material-design.min.css" rel="stylesheet">
    <link href="/css/ui-touch-fonts.css" rel="stylesheet">
    <link href="/css/ui-touch.css" rel="stylesheet">
    
    <script src="/js/jquery.min.js"></script>
    <script src="/js/ui-main.js"></script>

</head>

<body>

    <script>
    $( document ).ready(function() {
        engineSys();
        
        $( ".meta .elapsed" ).click(function() {
          window.location.href="/";
        });
    });
    </script>
    
    <div class="sys">
        <span class="sys-icon volume"><i class="material-icons">volume_up</i> <span class="value"></span></span>
        <span class="sys-icon temp"><i class="material-icons">power</i> <span class="value"></span></span>
        <span class="sys-icon data"><i class="material-icons">wifi</i> <span class="value"></span></span>
    </div>
    <div class="hud play-type play-type-radio">
        <div class="meta">
            <ul>
                <li><span class="station"></span></li>
                <li><span class="playing"></span></li>
                <li><span class="elapsed"></span></li>
            </ul>
        </div>
        
        
        <!--<div class="network">
            <span class="wireless">
                <span class="addr"></span> 
                <span class="bandwidth"></span>
            </span>
            <span class="wired">
                <span class="addr"></span>
                <span class="bandwidth"></span>
            </span>
        </div>-->
        
        
    </div>
    
</body>
</html>
