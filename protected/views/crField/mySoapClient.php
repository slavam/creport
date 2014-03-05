<?php
header("Content-Type: text/html; charset=utf-8");

//define('DEBUG_LOG',1);

class CreditHistorySoapHeaderClass
{
    public $UserName;
    public $Password;
    public $Version;
    public $Culture;
    public $SecurityToken;
    public $UserId;

    public $namespace = 'http://ws.creditinfo.com/';
    public $name      = 'CigWsHeader';

    private $login = 'test.vbr';
    private $pass  = 'Test@1234';
    private $lang  = 'ru-RU';
    private $ver   = '1_0';

    function __construct(){
        $this->UserName = $this->login;
        $this->Password = $this->pass;
        $this->Version  = $this->ver;
        $this->Culture  = $this->lang;
        //HACK!?
        $this->UserId   = 0;
    }
}

class SearchFrontOfficeKeyValueClass{
    public $typeExportCode;
    public $identification;
    public $ip;
    public $identificationType;
}

class GetReportKeyValueClass{
    public $ciid;
    public $creditinfoId;
    public $remoteIp;
    public $reportVersion;
    public $cipScore;
}
class GetReportParametersClass{
    public $keyValue;
    function __construct($keyValue) {
        $this->keyValue = $keyValue;
    }
}

class SearchFrontOfficeParametersClass
{
	public $keyValue;
}

class CreditHistorySoapClass
{
//    private $soap;
    private $wsdl = 'https://test2.credithistory.com.ua/Service/Service.asmx?WSDL';

    function __construct(){
        try {
            $this->soapclient = new SoapClient($this->wsdl,
                array(
                        'soap_version' => SOAP_1_1,
                        'trace' => true,
                        'encoding' => 'UTF-8'
                        )
                );
            if(defined('DEBUG_LOG')){
                echo '<H3>'.'Server functions'.'</H3>';
                var_dump($this->soapclient->__getFunctions());
                echo '<H3>'.'Server types'.'</H3>';
                var_dump($this->soapclient->__getTypes());
            }


            } catch (SoapFault $fault) {
                            //trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
                if(defined('DEBUG_LOG')){
                        echo '<H3>'.'Fault!'.'</H3>';
                        var_dump($fault);
                }
            }
    }
    private function buildSoapHeader(){
        $soapheader = new CreditHistorySoapHeaderClass();
        $header =  new SoapHeader($soapheader->namespace,$soapheader->name,$soapheader,false);
        $this->soapclient->__setSoapHeaders(array($header));
    }

    public function callSearchFrontOffice($identification,$identificationType){
        $this->buildSoapHeader();

        try{
                $keyValue = new SearchFrontOfficeKeyValueClass();
                $keyValue->typeExportCode='Entity.Type.Individual';
                $keyValue->identification = $identification;
                $keyValue->identificationType = $identificationType;

                $parameters = new SearchFrontOfficeParametersClass();
                $parameters->keyValue = $keyValue;
                $params = new SoapVar($parameters, SOAP_ENC_OBJECT);

                $response = $this->soapclient->SearchFrontOffice(array('parameters'=>$params));
                //RETURNS FUCKING ANYXML TYPE - convert it to object
                        return simplexml_load_string($response->SearchFrontOfficeResult->any);

        } catch (SoapFault $fault) {
                        //trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
                        if(defined('DEBUG_LOG'))
                        {
                                echo '<H3>'.'Fault!'.'</H3>';
                                var_dump($fault);
                        }

                        if(defined('DEBUG_LOG'))
                        {
                                echo '<H3>'.'Fault! Last Request'.'</H3>';
                                var_dump($this->soapclient->__getLastRequest());
                                echo '<H3>'.'Fault! Last Response'.'</H3>';
                                var_dump($this->soapclient->__getLastResponse());
                        }

        }
    }
    public function callGetReport($ciid){
        $this->buildSoapHeader();
        try{
				$keyValue = new GetReportKeyValueClass();
				$keyValue->ciid=$ciid;
				$keyValue->creditinfoId = $ciid;
				$keyValue->remoteIp = '';
                                $keyValue->reportVersion = '2';
                                $keyValue->cipScore = '200101';
			
				$parameters = new GetReportParametersClass($keyValue);
//                                $doc = new GetReportDocClass();
//                                $doc->doc = $parameters;
//				$parameters->doc = $keyValue;
//                                $parameters->doc['keyValue']->ciid=$ciid;
//                                $parameters->reportId = 200017;
//                                var_dump($parameters);
//				$params = new SoapVar($parameters, SOAP_ENC_OBJECT);
                                $params = new SoapVar($parameters, SOAP_ENC_OBJECT);
		        $response = $this->soapclient->GetReport(array('reportId' => 200017,'doc'=>$params));
		        //RETURNS FUCKING ANYXML TYPE - convert it to object
				return simplexml_load_string($response->GetReportResult->any);
	
		} catch (SoapFault $fault) {
				//trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
				if(defined('DEBUG_LOG'))
				{
					echo '<H3>'.'Fault!'.'</H3>';
					var_dump($fault);
				}
				
				if(defined('DEBUG_LOG'))
				{
					echo '<H3>'.'Fault! Last Request'.'</H3>';
					var_dump($this->soapclient->__getLastRequest());
					echo '<H3>'.'Fault! Last Response'.'</H3>';
					var_dump($this->soapclient->__getLastResponse());
				}

		}
    }
}

$client = new CreditHistorySoapClass();
$response = $client->callSearchFrontOffice('3036119463','130');
$ciid = (string)$response->Result->FrontOffice->CigEntities->CigEntityBusinessObjectList->CigEntityBusinessObject->CreditinfoId;
$response = $client->callGetReport($ciid);

$bki_report = new MbkiReport($response);
                     var_dump(unserialize(serialize($bki_report)));
//                     var_dump($bki_report->contractTerminatedSummaryInformations);
//                     ReportMBKIController::actionShowReportJq($bki_report);
//echo '<H3>'.'Response var_dump'.'</H3>';
//var_dump($response);
//echo '<H3>'.'Response print_r'.'</H3>';
//var_dump((string)$response->Result->FrontOffice->CigEntities->CigEntityBusinessObjectList->CigEntityBusinessObject->CreditinfoId);
//print_r($response);

?>