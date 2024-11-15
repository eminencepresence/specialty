<?php
$blocked_ips = file('blocked_ips.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$ip = $_SERVER['REMOTE_ADDR'];

if (in_array($ip, $blocked_ips)) {
    header('HTTP/1.1 403 Forbidden');
    exit('Access denied');
}
?>
