<?php error_reporting(0); ?>
<?php

// wget https://github.com/maxmind/GeoIP2-php/releases/download/v2.11.0/geoip2.phar
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

if(is_array($_GET)&&count($_GET) > 0)
    {
        if((isset($_GET["ip"])) and (isset($_GET['ip']))!= (empty($_GET['ip'])))
        {
            $ip = $_GET["ip"];
        } else {
            $ip = get_remote_addr();
        }
    } else {
        $ip = get_remote_addr();
    }

// country
$country = (new GeoIp2\Database\Reader('data/GeoLite2-Country.mmdb'))->country($ip);
$country_name = $country->country->names['en'];
//$country_name = $country->country->names['zh-CN'];
$country_code = $country->country->isoCode;
//$city_name = $city_name == $country_name ? '' : $city_name;
// city
$city = (new GeoIp2\Database\Reader('data/GeoLite2-City.mmdb'))->city($ip);
$region_name = $city->mostSpecificSubdivision->names['en'];
//$region_name = $city->mostSpecificSubdivision->names['zh-CN'];
$region_code = $city->mostSpecificSubdivision->isoCode;
$city_name = $city->city->names['en'];
//$city_name = $city->city->names['zh-CN'];
$city_name = $city_name == $country_name ? '' : $city_name;
$latitude = $city->location->latitude;
$longitude = $city->location->longitude;
$timezone = $city->location->timeZone;
// isp
$isp = (new GeoIp2\Database\Reader('data/GeoIP2-ISP.mmdb'))->isp($ip);
$isp_name = $isp->isp;
#$org = $isp->organization;
//$network = $isp->network;
#$isp_org = $isp->autonomousSystemOrganization;
//asn
$asn = (new GeoIp2\Database\Reader('data/GeoLite2-ASN.mmdb'))->asn($ip);
$asn_number = $asn->autonomousSystemNumber;
$org = $asn->autonomousSystemOrganization;
$network = $asn->network;
// connection-type
$connection = (new GeoIp2\Database\Reader('data/GeoIP2-Connection-Type.mmdb'))->connectionType($ip);
$connection_type = $connection->connectionType;
$content = array ("ip"=>"$ip","country"=>"$country_name","country_code"=>"$country_code","region"=>"$region_name","region_code"=>"$region_code","city"=>"$city_name","latitude"=>"$latitude","longitude"=>"$longitude","time_zone"=>"$timezone","isp"=>"$isp_name","asn"=>"AS$asn_number","org"=>"$org","connection_type"=>"$connection_type","network"=>"$network","user_agent"=>"$ua");
$text = "$ip\n $country_name\n $country_code\n $region_name\n $region_code\n $city_name\n $latitude\n $longitude\n $timezone\n $isp_name\n AS$asn_number\n $org\n $connection_type\n $network\n $ua\n";
$result = json_encode($content,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
$json = stripslashes($result);

header('access-control-allow-origin: *');
header('cache-control: no-cache, no-store');

if (isset($_GET['json'])) {
			header('content-type:application/json;charset=utf-8');
			echo $json;
}
elseif  (isset($_GET['text'])) {
			header('content-type:text/plain;charset=utf-8');
			echo $text;
}
else {
   echo $ip;
}
