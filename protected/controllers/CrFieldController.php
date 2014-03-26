<?php

class CrFieldController extends Controller
{
    public function actionIndexJq()
    {
            $this->render('indexJq');
    }

    public function actionGetDictionaries(){
        $dictionaries = ['Бюро кредитных историй', 'Поля кредитных отчетов'];
        $responce['rows']=array();
        foreach ($dictionaries as $i=>$d) {
            $responce['rows'][$i]['id'] = $i+1;
            $responce['rows'][$i]['cell'] = array(
                $i,
                $d
            );
        }
        echo CJSON::encode($responce);            
    }

    public function actionGetFields(){
        $fields = CrField::model()->findAll(array('order'=>'name'));
        $responce['rows']=array();
        foreach ($fields as $i=>$f) {
            $responce['rows'][$i]['id'] = $i+1;
            $responce['rows'][$i]['cell'] = array(
                $f->id,
                $f->name
            );
        }
        echo CJSON::encode($responce);            
    }
    public function actionCreateField(){
        if(isset($_POST['name'])){
            $model=new CrField;
            $model->name=$_POST['name'];
            if($model->save()){
                Yii::app()->end();
                $this->redirect(array('indexJq'));
            }            
        }
        $this->render('createField');
    }
        
    public function loadModel($id)
    {
            $model=  CrField::model()->findByPk($id);
            if($model===null)
                    throw new CHttpException(404,'The requested page does not exist.');
            return $model;
    }
    
    public function actionDelete(){
       if(Yii::app()->request->isAjaxRequest)
           {
               if ($this->loadModel($_GET['field_id'])->delete())
                   echo CJSON::encdode (array('status'=>'deleted','message'=>'deleted'));
               else
                   echo CJSON::encode(array('status'=>'error','message'=>'error while delete record!'));
               Yii::app()->end();
           } else
               throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
    }
   
    public function actionLoadXML(){
        
    if (file_exists('test_mbki.xml')) {
//        ini_set('mbstring.substitute_character', "none");
            $xml = simplexml_load_file('test_mbki.xml');
//            $xml = utf8_encode($xml);
//            $id = $xml->r->trace['ReqID'];
//            $phone = $xml->{'r'}->trace['phone'];
//            $xml->asXML('xml_as_string.xml');
//            print_r(query_attribute($xml->r, "key", "5")->LST['DSer']);
//            print_r(query_attribute($xml->r, "key", "8")->LSTHS['DB']);
//            echo (string)$xml->asXML();
            print_r((string)$xml->Report->Subject->Surname);
//            foreach ($xml->xpath('//LSTHS') as $character) {
//                foreach ($character->attributes() as $key => $value) {
//
////                    print_r ((string)$key.' = '.mb_convert_encoding($value, 'UTF-8').'<br>');
//                    print_r ($key.' = '.$value.'<br>');
//                }
//                print_r('<br>');
//            }
        } else {
            exit('Не удалось открыть файл test.xml.');
        }
    }
//    private function query_attribute($xmlNode, $attr_name, $attr_value) {
//            foreach($xmlNode as $node) { 
//              switch($node[$attr_name]) {
//                case $attr_value:
//                  return $node;
//              }
//            }
//          }
//    private function is_report_in_db($xml){
//        $inn_ubki = $this->query_attribute($xml->r, "key", "5")->LST['OKPO'];
//        if (isset($inn_ubki))
//            $sql = "select * from xml_reports where tax_payer_number=".$inn_ubki." and chb_report_id='".$xml->r->trace['ReqID']."'";
//        else 
//            $sql = "select * from xml_reports where tax_payer_number=".$xml->Report->Subject->TaxpayerNumber." and chb_report_id='".$xml->Report->Subject->CreditinfoId."'";
//        $report = XmlReport::model()->findAllBySql($sql);
//        return count($report)>0? true:false;
//    }

//    public function actionMultipleupload(){
//        $model= new XmlReport;
//        if (isset($_POST['my_button'])){
//            foreach ($_FILES['image_name']['tmp_name'] as $key => $value) {
//                $xml = simplexml_load_file($value);
//                if (!$this->is_report_in_db($xml)) {
//                    $id = $xml->r->trace['ReqID'];
//                    $model->attributes = array();
//                    $model->created_at = date("Y-m-d H:i:s", time());
//                    $model->xml_report = (string)$xml->asXML();
//                    if (isset($id)){ // UBKI
//                        $model->tax_payer_number = $xml->r[1]->LST['OKPO'];
//                        $model->bureau_id = 2;
//                        $model->chb_report_id = $id;
//                    } else { // mbki
//                        $model->tax_payer_number = $xml->Report->Subject->TaxpayerNumber;
//                        $model->bureau_id = 3;
//                        $model->chb_report_id = $xml->Report->Subject->CreditinfoId;
//                    }
//                    $model->save();                    
//                }
//            }
//        }
////        var_dump("out side if");
//        $this->render('multipleupload',array('model'=>$model));
//    }
    public function actionMySoapClient(){
        $this->render('mySoapClient');
    }

    public function actionCurlUbki(){
        $this->render('curlUbki');
    }
    public function actionUpdateHistoryContract(){
//        $sql = "select * from history_contracts where payment_date is null"; // limit 10";
        $hcs = HistoryContract::model()->findAllBySql($sql);
        foreach ($hcs as $hc) {
            $hc->payment_date = $hc->year.'-'.($hc->month<10? '0'.$hc->month:$hc->month).'-01';
            $hc->save();
        }
        $this->render('indexJq');
    }

    public function actionCrmTestForm(){
        $model=new CrmForm;
        if(isset($_POST['CrmForm'])){
            $model->attributes=$_POST['CrmForm'];
            if($model->validate()){
//            $model['activityUID']=$_POST['CrmForm']['activityUID'];
                $model['emailSenderName']=$_POST['CrmForm']['emailSenderName'];
                $model['description']=$_POST['CrmForm']['description'];
                $model['type']=$_POST['CrmForm']['type'];
                $model['mailBody']=$_POST['CrmForm']['mailBody'];
    //            $model['attachments']=$_POST['CrmForm']['attachments'];
    //            $model['personID']=$_POST['CrmForm']['personID'];
                $model['lastName']=$_POST['CrmForm']['lastName'];
                $model['firstName']=$_POST['CrmForm']['firstName'];
                $model['middleName']=$_POST['CrmForm']['middleName'];
                $model['phone']=$_POST['CrmForm']['phone'];
                $model['interestProduct']=$_POST['CrmForm']['interestProduct'];
                $model['interestFANumber']=$_POST['CrmForm']['interestFANumber'];
//                var_dump($_FILES);
                $attachs = null;
//                if (count($_FILES['image_name']['tmp_name'])>0)
                foreach ($_FILES['image_name']['tmp_name'] as $key => $value) 
                    if($value>''){
                    $type = pathinfo($value, PATHINFO_EXTENSION);
                    $data = file_get_contents($value);
                    $base64 = base64_encode($data);
//                    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    $attachs[]= array(
                        'name' => $_FILES['image_name']['name'][$key],
                        'exten' => $_FILES['image_name']['type'][$key],
                        'file' => $base64,
                    );
                }
//                    var_dump($attachs);
//                $xml = simplexml_load_file($value);
//                if (!$this->is_report_in_db($xml)) {
//                    $id = $xml->r->trace['ReqID'];
//                    $model->attributes = array();
//                    $model->created_at = date("Y-m-d H:i:s", time());
//                    $model->xml_report = (string)$xml->asXML();
//                    if (isset($id)){ // UBKI
//                        $model->tax_payer_number = $xml->r[1]->LST['OKPO'];
//                        $model->bureau_id = 2;
//                        $model->chb_report_id = $id;
//                    } else { // mbki
//                        $model->tax_payer_number = $xml->Report->Subject->TaxpayerNumber;
//                        $model->bureau_id = 3;
//                        $model->chb_report_id = $xml->Report->Subject->CreditinfoId;
//                    }
//                    $model->save();                    
//                }
//                var_dump($attachs);
//                var_dump($model);
//            if($model->validate())
//            $client = new CrmTestSoapClass();
//            $attachs = array();
//            $attachs[0] = array(
//                    'name' => 'File1',
//                    'exten'=>'jpg',
//                    'file'=>'/9j/4AAQSkZJRgABAgAAZABkAAD/7AhnMYQf/2Q==',
//            );
//            $attachs[1] = array(
//                    'name' => 'File2',
//                    'exten'=>'txt',
//                    'file'=>'Text',
//            );
//            var_dump($model->attributes);
//            var_dump($attachs);
//            $response = $client->callWebService($model, $attachs);
//            var_dump($response);
            $ct = new CrmTestSoapClass();
            $ct->callWebService($model,$attachs);
            }
//        $ciid = (string)$response1->Result->FrontOffice->CigEntities->CigEntityBusinessObjectList->CigEntityBusinessObject->CreditinfoId;
//        $response = $client->callGetReport($ciid);
//        $this->createRawXmlRecord($response); // save raw xml
//        return $response;
//                $this->redirect(array('getReportByINN','inn'=>$model->inn)); //$_POST['inn']));
        }
        $this->render('crmTestForm',array('model'=>$model));
    }
    
//    public function CrmObjectArrayTest(){
//        $soapclient = new SoapClient($this->wsdl, array( 'soap_version' => SOAP_1_1, 'trace' => true, 'encoding' => 'UTF-8', "features" => SOAP_SINGLE_ELEMENT_ARRAYS ) ); 
//        if(defined('DEBUG_LOG')){ 
//             echo ''.'Server functions'.''; 
//          var_dump($this->soapclient->__getFunctions()); 
//             echo '         */'.'Server types'.''; 
// var_dump($this->soapclient->__getTypes()); 
//  } } catch (SoapFault $fault) { //trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR); if(defined('DEBUG_LOG')){ echo '
//'.'Fault!'.'
//'; var_dump($fault); } } } private function AddWSSUsernameToken($username, $password) { $wssNamespace = "http://schemas.xmlsoap.org/ws/2002/07/secext"; $username = new SoapVar($username, XSD_STRING, null, null, 'Username', $wssNamespace); $password = new SoapVar($password, XSD_STRING, null, null, 'Password', $wssNamespace); $usernameToken = new SoapVar(array($username, $password), SOAP_ENC_OBJECT, null, null, 'UsernameToken', $wssNamespace); $usernameToken = new SoapVar(array($usernameToken), SOAP_ENC_OBJECT, null, null, null, $wssNamespace); $wssUsernameTokenHeader = new SoapHeader($wssNamespace, 'Security', $usernameToken); $this->soapclient->__setSoapHeaders($wssUsernameTokenHeader); } public function callWebService(){ $this->AddWSSUsernameToken('IDB_ADMIN', 'idb_admin'); try{ $req = new class_request(); $req->webResponses = new class_webResponses(); $req->webResponses->wr = new class_wr(); //!!! Обязательный ппараметр - надо в тест-кейсах указать это! $req->webResponses->wr->type='Email - Inbound'; $req->webResponses->wr->attachments = new class_attachments(); $attach = new class_attach(); $attach->name = 'File1'; $attach->exten = 'jpg'; $attach->file = '/9j/4AAQSkZJRgABAgAAZABkAAD/7AhnMYQf/2Q=='; $attach1 = new class_attach(); $attach1->name = 'File2'; $attach1->exten = 'jpeg'; $attach1->file = '/9j/4AAQSkZJRgABAgAAZABkAAD/7AhnMYQf/2Q=='; $req->webResponses->wr->attachments->attach[] = $attach; $req->webResponses->wr->attachments->attach[] = $attach1; $req->webResponses->wr->attachments = new SoapVar($req->webResponses->wr->attachments, SOAP_ENC_OBJECT,'attachments','http://siebel.com/'); $params = new SoapVar($req, SOAP_ENC_OBJECT); var_dump($params); $response = $this->soapclient->VBR_spcAction_spcWR_spcUpsert($params); if (is_soap_fault($response)) { trigger_error("SOAP Fault: (faultcode: {$response->faultcode}, faultstring: {$response->faultstring})", E_USER_ERROR); } echo '
//'.'CORRECT! Last Request'.'
//'; echo "REQUEST:\n" . htmlentities(str_ireplace('><', ">\n<", $this->soapclient->__getLastRequest())) . "\n"; echo '
//'.'CORRECT! Last Response'.'
//'; echo "REQUEST:\n" . htmlentities(str_ireplace('><', ">\n<", $this->soapclient->__getLastResponse())) . "\n"; //RETURNS FUCKING ANYXML TYPE - convert it to object return simplexml_load_string($response->SearchFrontOfficeResult->any); } catch (SoapFault $fault) { //trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR); if(defined('DEBUG_LOG')) { echo '
//'.'Fault!'.'
//'; var_dump($fault); } if(defined('DEBUG_LOG')) { echo '
//'.'Fault! Last Request'.'
//'; echo "REQUEST:\n" . htmlentities(str_ireplace('><', ">\n<", $this->soapclient->__getLastRequest())) . "\n"; echo '
//'.'Fault! Last Response'.'
//'; echo "REQUEST:\n" . htmlentities(str_ireplace('><', ">\n<", $this->soapclient->__getLastResponse())) . "\n"; } } } } $ct = new CrmTestSoapClass(); 
//                 $ct->callWebService();          
//    }
}