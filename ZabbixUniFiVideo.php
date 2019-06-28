<?php
/**
 * Created by andrea
 * Date: 27/06/2019
 * Time: 12:47
 */
require_once __DIR__ . '/vendor/autoload.php';
use UniFiNVR\UniFiNVR;
use Noodlehaus\Config;

$conf = Config::load('config.json');

$url = 'https://' . $conf->get('unifi.host') . ':' . $conf->get('unifi.port');
print_r((new UniFiNVR($url, $conf->get('unifi.api')))->getLastRecord('5d11f795e4b0d0ed9a52e0fb'));
echo "\n";
print_r((new UniFiNVR($url, $conf->get('unifi.api')))->getFreeSpace());
echo "\n";
print_r((new UniFiNVR($url, $conf->get('unifi.api')))->getUsedSpace());