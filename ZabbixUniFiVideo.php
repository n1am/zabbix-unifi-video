#!/usr/bin/php
<?php
/**
 * Created by andrea
 * Date: 27/06/2019
 * Time: 12:47
 */
require_once __DIR__ . '/vendor/autoload.php';
use UniFiNVR\UniFiNVR;
use Noodlehaus\Config;

try{
    $conf = Config::load(__DIR__ . '/config.json');
    $url = 'https://' . $conf->get('unifi.host') . ':' . $conf->get('unifi.port');
} catch (Exception $e){
    echo $e;
    exit;
}

if ($argc > 1){
    switch ($argv[1]) {
        case 'discovery':
            echo (new UniFiNVR($url, $conf->get('unifi.api')))->discoveryCameras();
            break;

        case 'free':
            echo (new UniFiNVR($url, $conf->get('unifi.api')))->getFreeSpace();
            break;

        case 'used':
            echo (new UniFiNVR($url, $conf->get('unifi.api')))->getUsedSpace();
            break;

        case 'camera-status':
            $status = (!isset($argv[2]) ? exit(1) : new UniFiNVR($url, $conf->get('unifi.api')))->isCameraAlive($argv[2]);
            echo $status;
            break;

        case 'camera-lastrec':
            $lastRec = (!isset($argv[2]) ? exit(1) : new UniFiNVR($url, $conf->get('unifi.api')))->getLastRecord($argv[2]);
            echo $lastRec;
            break;
    }
}