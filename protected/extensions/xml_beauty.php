<?php
$data = '<?xml version="1.0" encoding="UTF-8"?> <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://siebel.com/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:ns2="http://schemas.xmlsoap.org/ws/2002/07/secext"> <SOAP-ENV:Header> <ns2:Security> <ns2:UsernameToken> <ns2:Username>IDB_ADMIN</ns2:Username> <ns2:Password>idb_admin</ns2:Password> </ns2:UsernameToken> </ns2:Security> </SOAP-ENV:Header> <SOAP-ENV:Body> <ns1:VBR_spcAction_spcWR_spcUpsert_Input> <webResponses> <wr> <emailSenderName> </emailSenderName> <description> </description> <type>Email - Inbound</type> <mailBody> </mailBody> <attachments xsi:type="ns1:attachments"/> <lastName> </lastName> <firstName> </firstName> <middleName> </middleName> <phone> </phone> <interestProduct> </interestProduct> <interestFANumber> </interestFANumber> </wr> </webResponses> </ns1:VBR_spcAction_spcWR_spcUpsert_Input> </SOAP-ENV:Body> </SOAP-ENV:Envelope>';
$dom = new DOMDocument('1.0','UTF-8');
$dom->preserveWhiteSpace = FALSE;
$dom->loadXML($data);
$dom->formatOutput = TRUE;
echo '<pre>'.htmlentities($dom->saveXml()).'</pre>';
?>
