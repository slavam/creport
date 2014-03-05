<?php

$INN = "2911001195";

$UBKI_LOGIN    = "v.morhachov";
$UBKI_PASSWORD = "ntcnbhjdfybt";

$UBKI_URL_BASE = "https://www.ubki2.com.ua/api/xmlrequest.php?login=";
$UBKI_URL_ATTR1= "&passw=";
$UBKI_URL_ATTR2= "&typerequest=BLC";
$UBKI_URL_ATTR3= "&inn=";
$UBKI_URL_ATTR4= "&lnameua=&fnameua=&mnameua=&lnameru=&fnameru=&mnameru=&bdate=coding=&pser=&pnom=";

$UBKI_HOST = $UBKI_URL_BASE.$UBKI_LOGIN.$UBKI_URL_ATTR1.$UBKI_PASSWORD.$UBKI_URL_ATTR2.$UBKI_URL_ATTR3.$INN.$UBKI_URL_ATTR4;

$ch = curl_init();

curl_setopt ($ch, CURLOPT_URL, $UBKI_HOST); 
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

/*
t's a pretty common problem in Windows. You need just to set cacert.pem to curl.cainfo.

Since PHP 5.3.7 you could do:

download http://curl.haxx.se/ca/cacert.pem and save it somewhere.
update php.ini -- add curl.cainfo = "PATH_TO/cacert.pem"
Otherwise you will need to do the following for every cURL resource:

curl_setopt ($ch, CURLOPT_CAINFO, "PATH_TO/cacert.pem");
*/
curl_setopt ($ch, CURLOPT_CAINFO, "d:\\prj\\credithistory\\cacert.pem");

curl_exec ($ch);

$result = curl_multi_getcontent ($ch);

if(!$result) var_dump(curl_error($ch));
else var_dump($result);

curl_close ($ch);

?>