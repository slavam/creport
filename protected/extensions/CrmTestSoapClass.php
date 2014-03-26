<?php
//header("Content-Type: text/html; charset=utf-8");

//define('DEBUG_LOG',1);

class class_request{
    public $webResponses;
}

class class_webResponses{
	public $wr;
}

class class_attachments{
    public $attach;
}

class class_attach {
    public $name;
    public $exten;
    public $file;
}

class class_wr{
//    public $activityUID;
    public $emailSenderName;
    public $description;
    public $type;
    public $mailBody;
    public $attachments;
//    public $personID;
    public $lastName;
    public $firstName;
    public $middleName;
    public $phone;
    public $interestProduct;
    public $interestFANumber;
    function __construct($model){
        $this->emailSenderName = $model->emailSenderName;
        $this->description = $model->description;
//        $this->type = $model->type;
        $this->mailBody = $model->mailBody;
        $this->lastName = $model->lastName;
        $this->firstName=$model->firstName;
        $this->middleName=$model->middleName;
        $this->phone=$model->phone;
        $this->interestProduct=$model->interestProduct;
        $this->interestFANumber=$model->interestFANumber;
                
    }
}

class CrmTestSoapClass
{
////    private $soap;
    private $wsdl = '01_10_03.wsdl';

    function __construct(){
        try {
            $this->soapclient = new SoapClient($this->wsdl,
                array(
                        'soap_version' => SOAP_1_1,
                        'trace' => true,
                        'encoding' => 'UTF-8',
                        'features' => SOAP_SINGLE_ELEMENT_ARRAYS
                )
            );
            if(defined('DEBUG_LOG')){
/*
                echo '<H3>'.'Server functions'.'</H3>';
//                var_dump($this->soapclient->__getFunctions());
                echo '<H3>'.'Server types'.'</H3>';
//                var_dump($this->soapclient->__getTypes());
*/
            }


            } catch (SoapFault $fault) {
                            //trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
                if(defined('DEBUG_LOG')){
                        echo '<H3>'.'Fault!'.'</H3>';
                        var_dump($fault);
                }
            }
    }

	private function AddWSSUsernameToken($username, $password)
	{
	    $wssNamespace = "http://schemas.xmlsoap.org/ws/2002/07/secext";
	    $username = new SoapVar($username, XSD_STRING, null, null, 'Username', $wssNamespace);
	    $password = new SoapVar($password, XSD_STRING, null, null, 'Password', $wssNamespace);
	    $usernameToken = new SoapVar(array($username, $password), SOAP_ENC_OBJECT, null, null, 'UsernameToken', $wssNamespace);
	    $usernameToken = new SoapVar(array($usernameToken), SOAP_ENC_OBJECT, null, null, null, $wssNamespace);
	    $wssUsernameTokenHeader = new SoapHeader($wssNamespace, 'Security', $usernameToken);
	    $this->soapclient->__setSoapHeaders($wssUsernameTokenHeader); 
	}

    public function callWebService($model, $attachs){
        $this->AddWSSUsernameToken('IDB_ADMIN', 'idb_admin');

        try{
				$req = new class_request();
				$req->webResponses = new class_webResponses();
				$req->webResponses->wr = new class_wr($model);
//                                var_dump($req->webResponses->wr);
				//!!! Обязательный ппараметр - надо в тест-кейсах указать это!
				$req->webResponses->wr->type='Email - Inbound';

                $req->webResponses->wr->attachments = new class_attachments();
//                var_dump($attachs);
                if (isset($attachs))
                foreach ($attachs as $a) {
                    $attach = new class_attach();
                    $attach->name = $a['name'];
                    $attach->exten = $a['exten'];
                    $attach->file = $a['file'];
                    $req->webResponses->wr->attachments->attach[] = $attach;
                }
//                var_dump($req->webResponses->wr->attachments->attach);

//                $attach = new class_attach();
//                $attach->name = 'File1';
//                $attach->exten = 'jpg';
//                $attach->file = '/9j/4AAQSkZJRgABAgAAZABkAAD/7AhnMYQf/2Q==';
//                $attach1 = new class_attach();
//                $attach1->name = 'File2';
//                $attach1->exten = 'jpeg';
//                $attach1->file = '/9j/4AAQSkZJRgABAgAAZABkAAD/7AhnMYQf/2Q==';
//
//                $req->webResponses->wr->attachments->attach[] = $attach;
//                $req->webResponses->wr->attachments->attach[] = $attach1;
                
                $req->webResponses->wr->attachments = new SoapVar($req->webResponses->wr->attachments, SOAP_ENC_OBJECT,'attachments','http://siebel.com/');

                $params = new SoapVar($req, SOAP_ENC_OBJECT);

//                var_dump($params);

                $response = $this->soapclient->VBR_spcAction_spcWR_spcUpsert($params);

                if (is_soap_fault($response)) {
				    trigger_error("SOAP Fault: (faultcode: {$response->faultcode}, faultstring: {$response->faultstring})", E_USER_ERROR);
				}
  
                                echo '<H3>'.'CORRECT! Last Request'.'</H3>';
                                echo "REQUEST:\n" . htmlentities(str_ireplace('><', ">\n<", $this->soapclient->__getLastRequest())) . "\n";
                                echo '<H3>'.'CORRECT! Last Response'.'</H3>';
                                echo "REQUEST:\n" . htmlentities(str_ireplace('><', ">\n<", $this->soapclient->__getLastResponse())) . "\n";
				
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
                                echo "REQUEST:\n" . htmlentities(str_ireplace('><', ">\n<", $this->soapclient->__getLastRequest())) . "\n";
                                echo '<H3>'.'Fault! Last Response'.'</H3>';
                                echo "REQUEST:\n" . htmlentities(str_ireplace('><', ">\n<", $this->soapclient->__getLastResponse())) . "\n";
                        }

        }
    }
}

//$ct = new CrmTestSoapClass();
//$ct->callWebService();

?>
