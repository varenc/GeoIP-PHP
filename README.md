# GeoIP-PHP
## 依赖包下载
```bash
wget https://github.com/maxmind/GeoIP2-php/releases/download/v2.11.0/geoip2.phar
```
## 使用
```
curl ip.xxx.com
```
## 返回
```
{
    "ip": "204.44.x.x",
    "country": "United States",
    "isoCode": "US",
    "city": "Los Angeles",
    "isp": "QuadraNet",
    "isp_org": "QuadraNet, Inc",
    "asn": "AS8100",
    "asn_org": "ASN-QUADRANET-GLOBAL",
    "connection_type": "Corporate",
    "network": "204.44.0.0/23",
    "user_agent": "curl/7.68.0"
}
```
