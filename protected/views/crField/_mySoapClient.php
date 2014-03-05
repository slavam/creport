<?php 


//$uri='https://secure.credithistory.com.ua/service/api/index.php?wsdl';
//$uri='https://test2.credithistory.com.ua/Service/Service.asmx?wsdl';

//$uri= 'https://test2.credithistory.com.ua/Service/api/index.php?wsdl';
//$client = new SoapClient($uri, array( 'soap_version' => SOAP_1_1));
//var_dump($client->Queryresult('test.vbr', 'Test@1234','1947212986',130));

//var_dump($client->Queryresult(array('Username'=> 'test.vbr', 'Password'=>'Test@1234','Number'=>'1806207029','NumberType'=>130)));
//var_dump($client->GetPhoto('test.vbr', 'Test@1234','1806207029'));
//var_dump($client->GetReport('test.vbr', 'Test@1234','1_0','ru-RU',16162150));

//$uri='https://test2.credithistory.com.ua/Service/Service.asmx?wsdl';
$uri='https://test2.credithistory.com.ua/Service/api/index.php?wsdl';
$client = new SoapClient($uri, array( 'soap_version' => SOAP_1_2));
$res=$client->SearchFrontOffice('test.vbr', 'Test@1234','1_0','uk-UA','',0); //,'1806207029'));
//var_dump($client->__soapCall('SearchFrontOffice', array('test.vbr', 'Test@1234','1_0','ru-RU','')));
if (is_soap_fault($res)) {
    trigger_error("SOAP Fault: (faultcode: {$res->faultcode}, faultstring: {$res->faultstring})", E_USER_ERROR);
}

//var_dump($client->GetVersion());
//	var_dump($client->__getTypes());
//var_dump($client->__getFunctions());
//var_dump($client);
//var_dump($client->__getLastRequest());
?>