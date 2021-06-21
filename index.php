<?php

if (isset($_GET['raw'])) {
	readfile($_SERVER["SCRIPT_FILENAME"]);
	exit;
}

// wget https://github.com/maxmind/GeoIP2-php/releases/download/v2.11.0/geoip2.phar
// wget https://phus.lu/server/GeoIP2-City.mmdb
require_once 'geoip2.phar';

function get_remote_addr()
{
    if (isset($_SERVER["HTTP_X_REAL_IP"])) {
        return $_SERVER["HTTP_X_REAL_IP"];
    } else if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
        return preg_replace('/^.+,\s*/', '', $_SERVER["HTTP_X_FORWARDED_FOR"]);
    } else {
        return $_SERVER["REMOTE_ADDR"];
    }
}

function get_user_agent()
{
   return $_SERVER["HTTP_USER_AGENT"]; 
}

$ua = get_user_agent();
$ip = get_remote_addr();
// country
$country = (new GeoIp2\Database\Reader('data/GeoLite2-Country.mmdb'))->country($ip);
$country_name = $country->country->names['en'];
$isoCode = $country->country->isoCode;
//$city_name = $country->country->names['zh-CN'];
//$city_name = $city_name == $country_name ? '' : $city_name;
// city
$city = (new GeoIp2\Database\Reader('data/GeoLite2-City.mmdb'))->city($ip);
//$country_name = $city->country->names['zh-CN'];
$city_name = $city->city->names['en'];
$city_name = $city_name == $country_name ? '' : $city_name;
// isp
$isp = (new GeoIp2\Database\Reader('data/GeoIP2-ISP.mmdb'))->isp($ip);
$isp_name = $isp->isp;
$org = $isp->organization;
$network = $isp->network;
$isp_org = $isp->autonomousSystemOrganization;
//asn
$asn = (new GeoIp2\Database\Reader('data/GeoLite2-ASN.mmdb'))->asn($ip);
$asn_number = $asn->autonomousSystemNumber;
$asn_org = $asn->autonomousSystemOrganization;

// connection-type
$connection = (new GeoIp2\Database\Reader('data/GeoIP2-Connection-Type.mmdb'))->connectionType($ip);
$connection_type = $connection->connectionType;
$content = array ("ip"=>"$ip","country"=>"$country_name","isoCode"=>"$isoCode","city"=>"$city_name","isp"=>"$isp_name","isp_org"=>"$isp_org","asn"=>"AS$asn_number","asn_org"=>"$asn_org","connection_type"=>"$connection_type","network"=>"$network","user_agent"=>"$ua");
$results = json_encode($content,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
$result = stripslashes($results);

header('access-control-allow-origin: *');
header('content-type:application/json;charset=utf-8');
header('cache-control: no-cache, no-store');
//header('content-length: ' . strlen($content));
//print($content);

echo $result;
