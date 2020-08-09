<?php
header("Content-Type: application/json");

$json = file_get_contents("/var/www/html/status.json");
echo $json;
?>