<?php

//require_once('log.php');
//require_once('wsse.php');
//require_once('creport.php');

class ReportController extends Controller
{
    private $log;
    public $hist_is_negative = 'История НЕГАТИВНАЯ';
    public $hist_is_positive = 'История ПОЗИТИВНАЯ';
    private function query_attribute($xmlNode, $attr_name, $attr_value) {
        foreach($xmlNode as $node) { 
            switch($node[$attr_name]) {
                case $attr_value:
                    return $node;
            }
        }
    }
    private function is_report_in_db($xml){
        if($this->query_attribute($xml->r, "key", "5")->LST['errcode']=='nocl')
            return false;
        $inn_ubki = $this->query_attribute($xml->r, "key", "5")->LST['OKPO'];
        if (isset($inn_ubki))
            $sql = "select * from xml_reports where tax_payer_number=".$inn_ubki." and chb_report_id='".$xml->r->trace['ReqID']."'";
        else 
            $sql = "select * from xml_reports where tax_payer_number=".$xml->Report->Subject->TaxpayerNumber." and chb_report_id='".$xml->Report->Subject->CreditinfoId."'";
        $report = XmlReport::model()->findAllBySql($sql);
        return count($report)>0? true:false;
    }
    public function actionMultipleupload(){
        $model= new XmlReport;
        $res='';
        if (isset($_POST['my_button'])){
//            var_dump($_FILES['image_name']['tmp_name'][0]);
            if(count($_FILES['image_name']['tmp_name'][0])>''){
//                var_dump($_FILES);
            foreach ($_FILES['image_name']['tmp_name'] as $key => $value) {
                $xml = simplexml_load_file($value);
                if($this->query_attribute($xml->r, "key", "5")->LST['errcode']=='nocl'){
                    $res.="В УБКИ данных о клиенте нет. ИНН:".substr($_FILES['image_name']['name'][0],0,10).'<br>';
                    $report = new Report();
                    $report->report_type_id = 3; // reportNotAvailable
                    $report->bureau_id = 2;
                    $report->created_at = date("Y-m-d H:i:s", time());
                    $report->code_from_bureau = ''; 
                    $report->issue_date = null;
                    $report->taxpayer_number = substr($_FILES['image_name']['name'][0],0,10);
                    $report->save();
                    $model->created_at = date("Y-m-d H:i:s", time());
                    $model->xml_report = (string)$xml->asXML();
                    $model->tax_payer_number = substr($_FILES['image_name']['name'][0],0,10);
                    $model->bureau_id = 2;
                    $model->chb_report_id = '';
                    $model->save(); 
                }else{
                    if (!$this->is_report_in_db($xml)) {
                        $id = $xml->r->trace['ReqID'];
                        $model->attributes = array();
                        $model->created_at = date("Y-m-d H:i:s", time());
                        $model->xml_report = (string)$xml->asXML();
                        if (isset($id)){ // UBKI
                            $model->tax_payer_number = $xml->r[1]->LST['OKPO'];
                            $model->bureau_id = 2;
                            $model->chb_report_id = $id;
                        } else { // mbki
                            $model->tax_payer_number = $xml->Report->Subject->TaxpayerNumber;
                            $model->bureau_id = 3;
                            $model->chb_report_id = $xml->Report->Subject->CreditinfoId;
                        }
                        $model->save();                    
                        $res .= "Загружен отчет для ИНН:".$model->tax_payer_number.'<br>';
                        $this->actionSaveCreditReport($xml);
                    }else{
                        $res .= "Для ИНН:".substr($_FILES['image_name']['name'][0],0,10).' в базе уже есть этот же отчет. Файл не загружен.<br>';
                    }
                }
            }
        }
        }
        $this->render('multipleupload',array('model'=>$model, 'result'=>$res));
    }
    
    public function actionInn()
    {
        $model=new InnForm;
        $model->scenario='my_test';
        if(isset($_POST['InnForm'])){
            $model->attributes=$_POST['InnForm'];
            if($model->validate())
                $this->redirect(array('getReportByINN','inn'=>$model->inn)); //$_POST['inn']));
        }
        $this->render('inn',array('model'=>$model));
    }
    
    private function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
           $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';

        return $ipaddress; 
    }

    public function createNativeQueryRecord($tax_payer_number, $bureau_id, $description){
        $query = new NativeQuerie();
        $query->author = $this->get_client_ip();
        $query->created_at = date("Y-m-d H:i:s", time());
        $query->taxpayer_number = $tax_payer_number;
        $query->bureau_id = $bureau_id;
        $query->result = $description;
        $query->user_id = Yii::app()->user->id;
        $query->save();
    }

    public function actionGetReportByINN(){
        $last_xml_report = XmlReport::model()->getLastReport($_GET['inn']);
        $this->createNativeQueryRecord($last_xml_report->tax_payer_number, $last_xml_report->bureau_id, 'Отчет выбран из таблицы xml_reports');
        
        if (isset($last_xml_report)){
             $xml = new SimpleXMLElement($last_xml_report->xml_report);
             if($this->query_attribute($xml->r, "key", "5")->LST['errcode']=='nocl'){
//                 $this->redirect(array(''))
                $this->render('showUbkiNotHaveClient', array('inn'=>$_GET['inn']));
             } else
             switch ($last_xml_report->bureau_id) {
                 case 2: // UBKI
                     $bki_report = new UbkiReport($xml);
                     $this->actionShowUbkiReportJq($bki_report, $last_xml_report->created_at);
                 break;
                 case 3: // MBKI
//                     var_dump($xml->Report->Subject); //->xpath('//SummaryInformation'));
                     $bki_report = new MbkiReport($xml);
                     $this->actionShowReportJq($bki_report, $last_xml_report->created_at);
                 break;
             }
        } else {
             $bki_report = '';
             $this->redirect(array('inn'));
        }
     }
     
     public function actionShowReportJq($bki_report, $created_at){
         $this->render('showReportJq',array('inn'=>$_GET['inn'], 'report'=>$bki_report, 'date'=>$created_at));
     }
     public function actionShowUbkiReportJq($bki_report, $created_at){
         $this->render('showUbkiReportJq',array('inn'=>$_GET['inn'], 'report'=>$bki_report, 'date'=>$created_at));
     }
     public function actionSaveCreditReport($xml){
         if(isset($xml)){
             $id = $xml->r->trace['ReqID'];
             $current_time = date("Y-m-d H:i:s", time());
             if (isset($id)){ // UBKI
                 $this->createNativeQueryRecord($credit_report->okpo, 2, 'Отчет получен из УБКИ');
                 $credit_report = new UbkiReport($xml);
                 $report = new Report();
                 $report->bureau_id = 2;
                 $report->created_at = $current_time;
                 $report->code_from_bureau = $xml->r->trace['ReqID'];
                 $report->issue_date = $credit_report->clDate>''? $credit_report->clDate:null;
                 $report->taxpayer_number = $credit_report->okpo;
                 $report->report_type_id = 1;
                if ($report->save()){
                    $ch_subject = new ChSubject();
                    $ch_subject->report_id = $report->id;
                    $ch_subject->created_at= $current_time;
                    $ch_subject->surname_ru = $credit_report->ruLName;
                    $ch_subject->firstname_ru =$credit_report->ruFName;
                    $ch_subject->middlename_ru = $credit_report->ruMName;
                    $ch_subject->surname_ua = $credit_report->uaLName;
                    $ch_subject->firstname_ua = $credit_report->uaFName;
                    $ch_subject->middlename_ua = $credit_report->uaMName;
                    $ch_subject->birth_date=$credit_report->db;
                    $ch_subject->gender_id = $credit_report->sex=='M'? 1 :($credit_report->gender=='F'? 2: null); 
                    $ch_subject->taxpayer_number =$credit_report->okpo;
//                    $ch_subject->is_resident = $credit_report->residency=='1'? true:false;
//                    $ch_subject->nationality_id = 
//                    $ch_subject->citizenship_id
//                    $ch_subject->education_id
                    $ch_subject->marital_status_id = $credit_report->familySt=='Y'? 2:null;
                    $ch_subject->photo_base64 = $credit_report->photo;
                    if ($ch_subject->save()){
                        if($credit_report->wokpo>'' or $credit_report->clWName>'' or $credit_report->clWDate>''){
                            $work = new Workplace();
                            $work->report_id = $report->id;
                            $work->ch_subject_id = $ch_subject->id;
                            $work->created_at=$current_time;
                            $work->name=$credit_report->clWName;
                            $work->start_date=$credit_report->clWDate>''?$credit_report->clWDate:null;
                            $work->code=$credit_report->wokpo;
                            $work->save();
                        }
                        foreach ($credit_report->auth_hist as $r) 
                            if($r['wokpo']>'' or $r['clWName']>'' or $r['clWDate']>''){
                                $work = new Workplace();
                                $work->report_id = $report->id;
                                $work->ch_subject_id = $ch_subject->id;
                                $work->created_at=$current_time;
                                $work->name=$r['clWName'];
                                $work->start_date=$r['clWDate']>''?$r['clWDate']:null;
                                $work->code=$r['wokpo'];
                                $work->save();
                            }       
                        if($credit_report->address1>''){
                            $address = new Address();
                            $address->report_id = $report->id;
                            $address->ch_subject_id = $ch_subject->id;
                            $address->created_at=$current_time;
                            $address->address_type_id = 1;
                            $address->addr1=$credit_report->address1;
                            $address->save();
                        }
                        if($credit_report->address2>''){
                            $address = new Address();
                            $address->report_id = $report->id;
                            $address->ch_subject_id = $ch_subject->id;
                            $address->created_at=$current_time;
                            $address->address_type_id = 3;
                            $address->addr1=$credit_report->address2;
                            $address->save();
                        }
                        if($credit_report->address3>''){
                            $address = new Address();
                            $address->report_id = $report->id;
                            $address->ch_subject_id = $ch_subject->id;
                            $address->created_at=$current_time;
                            $address->address_type_id = 2;
                            $address->addr1=$credit_report->address3;
                            $address->save();
                        }
                        foreach ($credit_report->contact_hist as $a) {
                            $address = new Address();
                            $address->report_id = $report->id;
                            $address->ch_subject_id = $ch_subject->id;
                            $address->created_at=$current_time;
                            $address->address_type_id = $a['type']=='1'? 1:($a['type']=='2'? 3:2);
                            $address->addr1=$a['address'];
                            $address->save();
                        }
                        foreach ($credit_report->contacts as $c) {
                            $contact = new Contact();
                            $contact->report_id = $report->id;
                            $contact->ch_subject_id = $ch_subject->id;
                            $contact->created_at=$current_time;
                            $contact->contact_type_id=$c['type']=='1'? 2:($c['tyoe']=='2'? 3:($c['type']=='3'? 1:null));
                            $contact->value=$c['number'];
                            $contact->save();
                        }
                        foreach ($credit_report->doc_hist as $i) {
                            $id_doc = new IdDocument();
                            $id_doc->report_id = $report->id;
                            $id_doc->ch_subject_id = $ch_subject->id;
                            $id_doc->created_at=$current_time;
                            $id_doc->document_type_id=2;
                            $id_doc->number=$i['dser'].' '.$i['dnum'];
                            $id_doc->issued_by=$i['dwho'];
                            $id_doc->issue_date=$i['dds']>''?  $i['dds']:null;
                            $id_doc->save();
                        }
                        foreach ($credit_report->credits as $ctr) {
                            $contract = new Contract();
                            $contract->ch_subject_id = $ch_subject->id;
                            $contract->report_id = $report->id;
                            $contract->created_at=$current_time;
                            $contract->contract_type_code = $ctr['creditType'];
                            $contract->is_open = $ctr['flClose']=='N'? true:false;
                            $contract->code=$ctr['reference'];
                            $contract->currency_id=$ctr['currencyCode']=='UAH'? 1:($ctr['currencyCode']=='USD'? 2: ($ctr['currencyCode']=='EUR'? 3:null));
                            $contract->role_id=1; //$ctr['subjectRoleCode']=='1'? 1:null;
//                            $contract->application_date = $ctr['dateOfApplication'];
                            $contract->credit_start_date =  $ctr['startDate']>''? $ctr['startDate']:null;
                            $contract->credit_end_date=  $ctr['stopDate'] == 'null'? null:$ctr['stopDate'];
                            $contract->total_amount=$ctr['amount'];
                            $contract->outstanding_amount=$ctr['amtCurr'];
//                            $contract->monthly_instalment_amount=$ctr['monthlyInstalmentAmountValue'];
                            $contract->overdue_amount=$ctr['amtExp'];
                            $contract->credit_type_id= (($ctr['creditType']=='01') or ($ctr['creditType']=='03') or ($ctr['creditType']=='04'))? 2:1;
//                            var_dump($ctr['crSetAmount']);
                            $contract->save();
                            foreach ($ctr['payments'] as $p) {
                                $this->saveUbkiHistoryContract($contract->id, $p, 5);
                                $this->saveUbkiHistoryContract($contract->id, $p, 6);
                                $this->saveUbkiHistoryContract($contract->id, $p, 7);
                                $this->saveUbkiHistoryContract($contract->id, $p, 8);
                                $this->saveUbkiHistoryContract($contract->id, $p, 9);
                                $this->saveUbkiHistoryContract($contract->id, $p, 10);
                                
//                                $hist = new HistoryContract();
//                                $hist->contract_id = $contract->id;
//                                $hist->month = $p['month'];
//                                $hist->year = $p['year'];
//                                $hist->value=$p['flPay'];
//                                $hist->factor_id=5;
//                                $hist->payment_date=$p['year'].'-'.$p['month'].'-01';
//                                $hist->save();
//                                
//                                $hist = new HistoryContract();
//                                $hist->contract_id = $contract->id;
//                                $hist->month = $p['month'];
//                                $hist->year = $p['year'];
//                                $hist->value=$p['amtCurr'];
//                                $hist->factor_id=6;
//                                $hist->payment_date=$p['year'].'-'.$p['month'].'-01';
//                                $hist->save();
//                                
//                                $hist = new HistoryContract();
//                                $hist->contract_id = $contract->id;
//                                $hist->month = $p['month'];
//                                $hist->year = $p['year'];
//                                $hist->value=$p['amtExp'];
//                                $hist->factor_id=10;
//                                $hist->payment_date=$p['year'].'-'.$p['month'].'-01';
//                                $hist->save();
//                                
//                                $hist = new HistoryContract();
//                                $hist->contract_id = $contract->id;
//                                $hist->month = $p['month'];
//                                $hist->year = $p['year'];
//                                $hist->value=$ctr['crSetAmount'];
//                                $hist->factor_id=7;
//                                $hist->payment_date=$p['year'].'-'.$p['month'].'-01';
//                                $hist->save();
//                                
//                                $hist = new HistoryContract();
//                                $hist->contract_id = $contract->id;
//                                $hist->month = $p['month'];
//                                $hist->year = $p['year'];
//                                $hist->value=$p['daysExp'];
//                                $hist->factor_id=8;
//                                $hist->payment_date=$p['year'].'-'.$p['month'].'-01';
//                                $hist->save();
//                                
//                                $hist = new HistoryContract();
//                                $hist->contract_id = $contract->id;
//                                $hist->month = $p['month'];
//                                $hist->year = $p['year'];
//                                $hist->value=$p['flUse'];
//                                $hist->factor_id=9;
//                                $hist->payment_date=$p['year'].'-'.$p['month'].'-01';
//                                $hist->save();
                            }
                        }
                        foreach ($credit_report->query_hist as $il){ 
//                            var_dump($il);
                            $inquiry = new Inquirie();
                            $inquiry->report_id = $report->id;
                            $inquiry->created_at = $current_time;
                            $inquiry->ch_subject_id=$ch_subject->id;
                            $inquiry->inquiry_date = $il['reqDateTime'];
                            $inquiry->inquiry_id = $il['reqID'];
                            $inquiry->inquiry_type =$il['reqType'];
                            $inquiry->save();
                        }
                    }
                }                 
             } else { //MBKI
                 $this->createNativeQueryRecord($credit_report->taxpayerNumber, 3, 'Отчет получен из МБКИ');
                 $credit_report = new MbkiReport($xml);
                 $report = new Report();
                 $report->bureau_id = 3;
                 $report->created_at = $current_time;
                 $report->code_from_bureau = $credit_report->mbkiId; // usageIdentity;
                 $report->issue_date = $credit_report->updated>''? $credit_report->updated:null; //issueDate;
                 $report->taxpayer_number = $credit_report->taxpayerNumber;
                 $report->report_type_id = 1;
//                 var_dump($report);
                if ($report->save()){
                    $ch_subject = new ChSubject();
                    $ch_subject->report_id = $report->id;
                    $ch_subject->surname_ru = $credit_report->surname;
                    $ch_subject->firstname_ru =$credit_report->name;
                    $ch_subject->middlename_ru = $credit_report->fathersName;
//                    $ch_subject->$surname_ua = $credit_report->
//                    $firstname_ua
//                    $middlename_ua
                    $ch_subject->birth_date=$credit_report->dateOfBirth>''? $credit_report->dateOfBirth:null;
                    $ch_subject->gender_id = $credit_report->gender=='2'? 2 :($credit_report->gender=='1'? 1: null); 
                    $ch_subject->taxpayer_number =$credit_report->taxpayerNumber;
                    $ch_subject->is_resident = $credit_report->residency=='1'? true:false;
//                    $ch_subject->nationality_id = 
//                    $ch_subject->citizenship_id
//                    $ch_subject->education_id
//                    $ch_subject->marital_status_id
//                    $ch_subject->photo_base64 = $credit_report->
                    $ch_subject->created_at= $current_time;
//                    
                    if ($ch_subject->save()){
//                        var_dump($credit_report->relations);
                        foreach ($credit_report->relations as $r) {
                            $work = new Workplace();
                            $work->report_id = $report->id;
                            $work->ch_subject_id = $ch_subject->id;
                            $work->created_at=$current_time;
                            $work->name=$r['companyName'];
                            $work->start_date=null; //$r['startDate'];
                            $work->address=$r['startDate'];
                            $work->code=$r['registrationNumber'];
                            $work->profession=$r['jobTitle'];
                            $work->save();
                        }       
//                        var_dump($credit_report->addresses);
                        foreach ($credit_report->addresses as $a) {
                            $address = new Address();
//                            $address->country_id = $a->country;
                            $address->report_id = $report->id;
                            $address->ch_subject_id = $ch_subject->id;
                            $address->created_at=$current_time;
                            $address->address_type_id = $a['id_type']=='1'? 1:($a['id_type']=='2'? 2:null);
                            $address->zip=$a['zip'];
//                            $address->code = KOATUU;
                            $address->addr1=$a['street'];
                            $address->save();
                        }
                        foreach ($credit_report->contacts as $c) {
                            $contact = new Contact();
                            $contact->report_id = $report->id;
                            $contact->ch_subject_id = $ch_subject->id;
                            $contact->created_at=$current_time;
                            $contact->contact_type_id=$c['code']=='1'? 2:($c['code']=='2'? 3:($c['code']=='3'? 1:null));
                            $contact->value=$c['value'];
                            $contact->save();
                        }
//                        var_dump($credit_report->identifications);
                        foreach ($credit_report->identifications as $i) {
                            $id_doc = new IdDocument();
                            $id_doc->report_id = $report->id;
                            $id_doc->ch_subject_id = $ch_subject->id;
                            $id_doc->created_at=$current_time;
                            $id_doc->document_type_id=$i['idType']=='2'? 1:($i['idType']=='4'? 2:null);
                            $id_doc->number=$i['docNumber'];
                            $id_doc->issued_by=$i['issuedBy'];
                            $id_doc->issue_date=$i['issuedDate']>''?  $i['issuedDate']:null;
                            $id_doc->save();
                        }
                        foreach ($credit_report->contracts as $ctr) {
                            $contract = new Contract();
                            $contract->ch_subject_id = $ch_subject->id;
                            $contract->report_id = $report->id;
                            $contract->created_at=$current_time;
                            $contract->contract_type_code = $ctr['importCode'];
                            $contract->is_open = $ctr['contractType']=='Existing'? true:false;
                            $contract->code=$ctr['codeOfContract'];
                            $contract->currency_id=$ctr['currencyCode']=='UAH'? 1:($ctr['currencyCode']=='USD'? 2: ($ctr['currencyCode']=='EUR'? 3:null));
                            $contract->role_id=$ctr['subjectRoleCode']=='1'? 1:null;
                            $contract->application_date = $ctr['dateOfApplication']>''? $ctr['dateOfApplication']:null;
                            $contract->credit_start_date =$ctr['creditStartDate']>''? $ctr['creditStartDate']:null;
                            $contract->credit_end_date=$ctr['contractEndDate']>''? $ctr['contractEndDate']:null;
                            $contract->total_amount=+$ctr['totalAmountValue'];
                            $contract->outstanding_amount= +$ctr['outstandingAmountValue'];
                            $contract->monthly_instalment_amount= +$ctr['monthlyInstalmentAmountValue'];
                            $contract->overdue_amount=+$ctr['overdueAmountValue'];
                            $contract->credit_type_id= (($ctr['exportCode']=='Contract.Type.Financial.Credit_by_installments')and(($ctr['purposeOfCreditCode']==7)or($ctr['purposeOfCreditCode']==9)or($ctr['purposeOfCreditCode']==10)))? 2:1;
                            try {
                                $contract->save();
                            } catch (Exception $exc) {
                                var_dump($ctr);
                                echo $exc->getTraceAsString();
                            }
                            
                            foreach ($ctr['months'] as $ms) 
                                foreach ($ms as $i => $m) {
                                if ($m>'' and $i!='description'){
                                    $slash_pos = stripos($m, '/');
                                    $month = substr($m, 0, $slash_pos);
                                    $year = '20'.substr($m, $slash_pos+1);
                                    if(count($ctr['hCTotalNumberOfOverdueInstalments'])>1){
                                        $this->saveMbkiHistoryContract($contract->id, $month, $year, $this->fineMbkiValue($ctr['hCTotalNumberOfOverdueInstalments'][$i]['value']), 1);
//                                        $hist = new HistoryContract();
//                                        $hist->month = $month;
//                                        $hist->year = $year;
//                                        $hist->value=$this->fineMbkiValue($ctr['hCTotalNumberOfOverdueInstalments'][$i]['value']); //str_replace(',', '.', str_replace(' ', '', $ctr['hCTotalNumberOfOverdueInstalments'][$i]['value']));
//                                        $hist->contract_id = $contract->id;
//                                        $hist->factor_id=1;
//                                        $hist->payment_date=$year.'-'.$month.'-01';
//                                        $hist->save();
                                    }
                                    if(count($ctr['hCTotalOverdueAmount'])>1){
                                        $this->saveMbkiHistoryContract($contract->id, $month, $year, $this->fineMbkiValue($ctr['hCTotalOverdueAmount'][$i]['value']), 2);
//                                        $hist = new HistoryContract();
//                                        $hist->month = $month;
//                                        $hist->year = $year;
//                                        $hist->value=$this->fineMbkiValue($ctr['hCTotalOverdueAmount'][$i]['value']); //str_replace(',', '.', str_replace(' ', '', $ctr['hCTotalOverdueAmount'][$i]['value']));
//                                        $hist->contract_id = $contract->id;
//                                        $hist->factor_id=2;
//                                        $hist->payment_date=$year.'-'.$month.'-01';
//                                        $hist->save();
                                    }
                                    if(count($ctr['hCResidualAmount'])>1){
                                        $this->saveMbkiHistoryContract($contract->id, $month, $year, $this->fineMbkiValue($ctr['hCResidualAmount'][$i]['value']), 3);
//                                        $hist = new HistoryContract();
//                                        $hist->month = $month;
//                                        $hist->year = $year;
//                                        $hist->value=$this->fineMbkiValue($ctr['hCResidualAmount'][$i]['value']); //str_replace(',', '.', str_replace(' ', '', $ctr['hCResidualAmount'][$i]['value']));
//                                        $hist->contract_id = $contract->id;
//                                        $hist->factor_id=3;
//                                        $hist->payment_date=$year.'-'.$month.'-01';
//                                        $hist->save();
                                    }
                                    if(count($ctr['hCCreditCardUsedInMonth'])>1){
                                        $this->saveMbkiHistoryContract($contract->id, $month, $year, $ctr['hCCreditCardUsedInMonth'][$i]['value'], 4);
//                                        $hist = new HistoryContract();
//                                        $hist->month = $month;
//                                        $hist->year = $year;
//                                        $hist->value=$ctr['hCCreditCardUsedInMonth'][$i]['value'];
//                                        $hist->contract_id = $contract->id;
//                                        $hist->factor_id=4;
//                                        $hist->payment_date=$year.'-'.$month.'-01';
//                                        $hist->save();
                                    }
                                }
                            }
                            if(isset($ctr['months24']))
                            foreach ($ctr['months24'] as $ms) 
                                if(isset ($ms))
                                foreach ($ms as $i => $m) {
                                if ($m>'' and $i!='description'){
                                    $slash_pos = stripos($m, '/');
                                    $month = substr($m, 0, $slash_pos);
                                    $year = '20'.substr($m, $slash_pos+1);
                                    if(count($ctr['hCTotalNumberOfOverdueInstalments24'])>1){
                                        $this->saveMbkiHistoryContract($contract->id, $month, $year, $this->fineMbkiValue($ctr['hCTotalNumberOfOverdueInstalments24'][$i]['value']), 1);
//                                        $hist = new HistoryContract();
//                                        $hist->month = $month;
//                                        $hist->year = $year;
//                                        $hist->value=$this->fineMbkiValue($ctr['hCTotalNumberOfOverdueInstalments24'][$i]['value']); //str_replace(',', '.', str_replace(' ', '', $ctr['hCTotalNumberOfOverdueInstalments24'][$i]['value']));
//                                        $hist->contract_id = $contract->id;
//                                        $hist->factor_id=1;
//                                        $hist->payment_date=$year.'-'.$month.'-01';
//                                        $hist->save();
                                    }
                                    if(count($ctr['hCTotalOverdueAmount24'])>1){
                                        $this->saveMbkiHistoryContract($contract->id, $month, $year, $this->fineMbkiValue($ctr['hCTotalOverdueAmount24'][$i]['value']), 2);
//                                        $hist = new HistoryContract();
//                                        $hist->month = $month;
//                                        $hist->year = $year;
//                                        $hist->value=$this->fineMbkiValue($ctr['hCTotalOverdueAmount24'][$i]['value']); //str_replace(',', '.', str_replace(' ', '', $ctr['hCTotalOverdueAmount24'][$i]['value']));
//                                        $hist->contract_id = $contract->id;
//                                        $hist->factor_id=2;
//                                        $hist->payment_date=$year.'-'.$month.'-01';
//                                        $hist->save();
                                    }
                                    if(count($ctr['hCResidualAmount24'])>1){
                                        $this->saveMbkiHistoryContract($contract->id, $month, $year, $this->fineMbkiValue($ctr['hCResidualAmount24'][$i]['value']), 3);
//                                        $hist = new HistoryContract();
//                                        $hist->month = $month;
//                                        $hist->year = $year;
//                                        $hist->value= $this->fineMbkiValue($ctr['hCResidualAmount24'][$i]['value']); // str_replace(',', '.', str_replace(' ', '', $ctr['hCResidualAmount24'][$i]['value']));
//                                        $hist->contract_id = $contract->id;
//                                        $hist->factor_id=3;
//                                        $hist->payment_date=$year.'-'.$month.'-01';
//                                        $hist->save();
                                    }
                                    if(count($ctr['hCCreditCardUsedInMonth24'])>1){
                                        $this->saveMbkiHistoryContract($contract->id, $month, $year, $ctr['hCCreditCardUsedInMonth24'][$i]['value'], 4);
//                                        $hist = new HistoryContract();
//                                        $hist->month = $month;
//                                        $hist->year = $year;
//                                        $hist->value=$ctr['hCCreditCardUsedInMonth24'][$i]['value'];
//                                        $hist->contract_id = $contract->id;
//                                        $hist->factor_id=4;
//                                        $hist->payment_date=$year.'-'.$month.'-01';
//                                        $hist->save();
                                    }
                                }
                            }
                        }
                        foreach ($credit_report->inquiryList as $il){ 
                            $inquiry = new Inquirie();
                            $inquiry->report_id = $report->id;
                            $inquiry->created_at = $current_time;
                            $inquiry->ch_subject_id=$ch_subject->id;
                            $inquiry->inquiry_date = $il['date'];
                            $inquiry->inquiry_id = $il['subscriber'];
                            $inquiry->inquiry_type =$il['subscriberType'];
                            $inquiry->save();
                        }
                    }
                 }
             }
             
         } else 
             return false;
     }
    private function fineMbkiValue($src){
        return $src=='-'? str_replace('-', '0', $src):str_replace(',', '.', str_replace(' ', '', $src));
    }

    private function saveMbkiHistoryContract($contract_id, $month, $year, $value, $factor_id){
        $hist = new HistoryContract();
        $hist->month = $month;
        $hist->year = $year;
        $hist->value=$value; 
        $hist->contract_id = $contract_id;
        $hist->factor_id=$factor_id; 
        $hist->payment_date=$year.'-'.$month.'-01';
        $hist->save();         
        return true;
     }

     private function saveUbkiHistoryContract($contract_id, $p, $factor_id){
        $fl_value = array('5'=>'flPay', 
            '6'=>'amtCurr', 
            '7'=>'crSetAmount', 
            '8'=>'daysExp',
            '9'=>'flUse',
            '10'=>'amtExp');
        $hist = new HistoryContract();
        $hist->contract_id = $contract_id;
        $hist->month = $p['month'];
        $hist->year = $p['year'];
        $hist->value=$p[$fl_value[$factor_id]]; 
        $hist->factor_id=$factor_id;
        $hist->payment_date=$p['year'].'-'.$p['month'].'-01';
        $hist->save();         
        return true;
     }

     public function actionHistoryIsAbsent($inn, $date){
         $this->render('historyIsAbsent',array('inn'=>$inn, 'date'=>$date));
     }
     
    public function actionInnForQuery(){
        $model=new InnForm;
        if(isset($_POST['InnForm'])){
            $model->attributes=$_POST['InnForm'];
            if($model->validate()){
                $native_report = Report::model()->getLastReportByInn($model->inn);
                if(isset($native_report)){
                    $curr_date = new DateTime("now");
                    $get_report_date = new DateTime($native_report->created_at); //issue_date);
                    if($curr_date->diff($get_report_date)->days<31){ // report is actual
                            $this->redirect(array('getReportByINN','inn'=>$model->inn)); //, 'source'=>$res)); // show report
                    }
                } 
                $from_bureau = $this->makeRequestToBureaus($model);
                switch ($from_bureau) {
                    case 0:
                    case 3:    
                        $this->actionShowBureauResponse($model->inn, $from_bureau); 
                        exit;
                    case 1:
                    case 2:
                        $this->redirect(array('getReportByINN','inn'=>$model->inn));
                        exit;
                }
            }
        }
        $this->render('innForQuery',array('model'=>$model));
    }
    public function actionShowBureauResponse($inn, $response){
        $this->render('showBureauResponse',array('inn'=>$inn, 'response'=>$response));
    }
    public function actionShowLastReportByBureau(){
        $last_report_by_bureau = XmlReport::model()->getLastReportByBureau($_GET['inn'],$_GET['bureau_id']);
        $this->showReportByBureau($last_report_by_bureau);
    }

    public function showReportByBureau($report){
        $this->createNativeQueryRecord($report->tax_payer_number, $report->bureau_id, 'Отчет выбран из таблицы xml_reports');
        try{
            $xml = new SimpleXMLElement($report->xml_report);
        } catch (Exception $e) {
            trigger_error(sprintf(
            'SimpleXMLElement failed with error #%d: %s',
            $e->getCode(), $e->getMessage()),
            E_USER_ERROR);
        }
        if($this->query_attribute($xml->r, "key", "5")->LST['errcode']=='nocl')
            $this->render('showUbkiNotHaveClient', array('inn'=>$_GET['inn']));
        else
            switch ($report->bureau_id) {
                case 2: // UBKI
                    $bki_report = new UbkiReport($xml);
                    $this->actionShowUbkiReportJq($bki_report, $report->created_at);
                break;
                case 3: // MBKI
                    $bki_report = new MbkiReport($xml);
                    $this->actionShowReportJq($bki_report, $report->created_at);
                break;
            }
    }

     private function makeRequestToBureaus($query){
//        $native_report = Report::model()->getLastReportByInn($query->inn);
//        if(isset($native_report)){
//            $curr_date = new DateTime("now");
//            $get_report_date = new DateTime($native_report->created_at); //issue_date);
//            if($curr_date->diff($get_report_date)->days<31) // report is actual
//                if($native_report->report_type_id!=3){   // report is real
//                    $this->createNativeQueryRecord($query->inn, $native_report->bureau_id, 'Отчет выбран из таблицы reports');
//                    $res = "from creport";
//                    $this->redirect(array('getReportByINN','inn'=>$query->inn, 'source'=>$res)); // show report
//                }
//        }
//                 send request into mbki // mwm
//            $mbkiResponse=null;
        $mbkiResponse = $this->getDataFromMbki($query); //inn);
        $ret_value=0;
        if(isset($mbkiResponse))
            if(isset($mbkiResponse->Root->reportNotAvailable)){ // mbki history is absent
                $report = new Report();
                $report->report_type_id = 3; // reportNotAvailable
                $report->bureau_id = 3;
                $report->created_at = date("Y-m-d H:i:s", time());
                $report->code_from_bureau = $mbkiResponse->Report->SubjectInfo->CreditinfoId; 
                $report->issue_date = $mbkiResponse->Report['issued'];
                $report->taxpayer_number = $mbkiResponse->Report->SubjectCode;
                $report->save();
            }else {
                $this->actionSaveCreditReport($mbkiResponse); // save report into DB
                $ret_value = 2;
            }

        // send request into ubki
        // ONLY TESTING MODE // mwm
//        $ubkiResponse = null;
        $ubkiResponse = $this->getDataFromUbki($query); // request // ONLY TESTING MODE
        // ONLY TESTING MODE
        if(isset($ubkiResponse)){
            $this->actionSaveCreditReport($ubkiResponse); // save report into DB
            $ret_value = $ret_value==2?3:1;
        }
        return $ret_value;
//        $mbki_report_date = isset($mbkiResponse)? new DateTime($mbkiResponse->Report['issued']):null;
//        $ubki_report_date = isset($ubkiResponse)? new DateTime($ubkiResponse->r[1]->LST['CLDATE']):null;
//        if ($mbki_report_date > $ubki_report_date){
//            $res = "from bureau";
//            if(isset($mbkiResponse->Root->reportNotAvailable))
//                $this->redirect(array('historyIsAbsent','inn'=>$mbkiResponse->Report->SubjectCode,'date'=>$mbkiResponse->Report['issued']));
//            else
//                $this->redirect(array('getReportByINN','inn'=>$query->inn, 'source'=>$res)); 
//        }else 
//            $this->redirect(array('getReportByINN','inn'=>$query->inn, 'source'=>$res));         
    }

    private function getDataFromUbki($query){
//        $login = "v.morhachov";
//        $passw = "ntcnbhjdfybt";
        $typerequest = 'ALL';
        try {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'https://www.ubki2.com.ua/api/xmlrequest.php?login='.$query->ubkiLogin.'&passw='.$query->ubkiPassword.'&typerequest='.$typerequest.'&inn='.$query->inn.'&lnameua=&fnameua=&mnameua=&lnameru=&fnameru=&mnameru=&bdate=coding=&pser=&pnom=');
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl, CURLOPT_CAINFO, getcwd()."/cacert.pem"); 
            $out = curl_exec($curl);

            if(!$out){
                throw new Exception(curl_error($curl), curl_errno($curl));
                curl_close($curl);
//                return null;
            } else {
                $xml = new SimpleXMLElement($out);
                $this->createRawXmlRecord($xml);
                curl_close($curl);
                return $xml;
            }
        } catch(Exception $e) {
            trigger_error(sprintf(
            'Curl failed with error #%d: %s',
            $e->getCode(), $e->getMessage()),
            E_USER_ERROR);
        }
    }
    private function getDataFromMbki($query){
        $client = new CreditHistorySoapClass($query->mbkiLogin, $query->mbkiPassword);
        $response1 = $client->callSearchFrontOffice($query->inn,'130');
        $ciid = (string)$response1->Result->FrontOffice->CigEntities->CigEntityBusinessObjectList->CigEntityBusinessObject->CreditinfoId;
        $response = $client->callGetReport($ciid);
        $this->createRawXmlRecord($response); // save raw xml
        return $response;
    }

    public function createRawXmlRecord($raw_xml){
        $model= new XmlReport;
        $model->attributes = array();
        $model->created_at = date("Y-m-d H:i:s", time());
        $model->xml_report = (string)$raw_xml->asXML();
        if (isset($raw_xml->r->trace['ReqID'])){ // UBKI
            $model->tax_payer_number = $raw_xml->r[1]->LST['OKPO'];
            $model->bureau_id = 2;
            $model->chb_report_id = $raw_xml->r->trace['ReqID'];
        } else { // mbki
            $model->bureau_id = 3;
            $model->tax_payer_number = $raw_xml->Report->Subject->TaxpayerNumber>''? $raw_xml->Report->Subject->TaxpayerNumber:$raw_xml->Report->SubjectCode;
            $model->chb_report_id = $raw_xml->Report->Subject->CreditinfoId>''? $raw_xml->Report->Subject->CreditinfoId : $raw_xml->Report->SubjectInfo->CreditinfoId;
        }
        $model->save();                    
    }
    public function actionGetParamForAnalyze(){
        $model=new AnalyzeForm;
        if(isset($_POST['AnalyzeForm'])){
            $model->attributes=$_POST['AnalyzeForm'];
            if($model->validate()){
                $this->redirect(array('showAnalyzeResult','inn'=>$model->inn,'type'=>$model->credit_type_id));
//            }else{ // если отчет у нас не найден, то делаем запросы в БКИ
//                $query = new InnForm;
//                $query->inn = $model->inn;
//                $this->makeRequestToBureaus($query);
            }
        }
        $this->render('getParamForAnalyze',array('model'=>$model));
     }
     
    public function actionCrmService(){
        $soap = new DOMDocument();
        $soap->loadXML(file_get_contents("php://input"));
//        loggerClass::write('++++++++++++> : '.serialize($soap->saveXML()),2);
//        loggerClass::write('++++++++++++> : '.(string)$_REQUEST,2);
        ini_set("soap.wsdl_cache_enabled", "0"); // 0 - disable while debug, ENABLE in production
        //create new SOAP server
        $server = new SoapServer("creport.wsdl",
                                array(
                                        'soap_version' => SOAP_1_1,
                                        'encoding'=>'UTF-8',
                                        'cache_wsdl' => WSDL_CACHE_NONE
                                    )
                                );

        $auth = new dummyAuthenticationClass();

        $s = new WSSESoapServer($soap, $auth);
        try {
            if ($s->process()) {
                $server->setClass("creportSoapServerClass");
                $server->handle($s->saveXML());
                exit;
            }
        } catch (Exception $e) {
            /* Any exception handling */
            loggerClass::write('-> AnalyzeCrediHistory(): '.serialize($e),2);
        }

        //!!! can be overriden
        loggerClass::write("[!] Server fault: 0x80000001, Authentication failed!",1);
        $server->fault(0x80000001, "Authentication failed!");
    }
     
    public function actionShowAnalyzeResult(){
        $need_type = $_GET['type'];
        $log='';
        $res = $this->analyzeOnly($_GET['inn'], $need_type, $log);
        $this->render('showAnalyzeResult',array('inn'=>$_GET['inn'], 'type'=>$need_type, 'log'=>$log, 'verdict'=>$res)); //'model'=>$model));
    }
    
    public function analyzeOnly($inn, $claim_type, &$log){
        $header_report = Report::model()->getLastReportByInn($_GET['inn']);
        $curr_date = new DateTime('now');
        $lastPaymentDate = new DateTime(Contract::model()->getLastPaymentDate($header_report->id, $header_report->bureau_id));
        $fromLastPaymentDays = $lastPaymentDate->diff($curr_date)->days;
        $log = '<br>Последний кредитный отчет поступил из '.($header_report->bureau_id==2? 'УБКИ':'МБКИ').'. ReportID='.$header_report->id;
        $log .= '<br>=================== С момента последнего платежа прошло '.$fromLastPaymentDays.' дн.';
        $contracts = Contract::model()->getContractsByReport($header_report->id);
        $res = $this->hist_is_positive;
        if(count($contracts)>0)
        foreach ($contracts as $contract) { // перебор контрактов
            $isUnsecuredCredit = Contract::model()->isUnsecuredCredit($contract->contract_type_code, $header_report->bureau_id); // беззалоговый?
            if($this->isPositiveContractVar3($header_report->bureau_id, $contract->id, $fromLastPaymentDays, $isUnsecuredCredit)){ // позитив, т.к. история отсутствует
                $log .= '<br>Variant 3 POSITIVE. Нет истории. Contract=>'.$contract->id.' ('.$contract->code.')';
                continue;
            }else { // анализ истории
                $log .= '<br>Variant 3 Есть история. Control continue. Contract=>'.$contract->id.' ('.$contract->code.')';
                $log .= '<br>=================== Кредит '.($isUnsecuredCredit? 'беззалоговый':'под залог');
                
                if((($header_report->bureau_id==2) and (HistoryContract::model()->getMaxDelayDays($contract->id, $fromLastPaymentDays, $isUnsecuredCredit)==0)) or 
                        (($header_report->bureau_id==3) and (HistoryContract::model()->getMaxDelaySum($header_report->bureau_id, $contract->id, $fromLastPaymentDays, $isUnsecuredCredit)==0))){ // кредит без просрочек
                    $log .= '<br>Variant 0 POSITIVE. Кредит без просрочек (см. отчет). Contract=>'.$contract->id.' ('.$contract->code.')';
                    continue;
                } 
                $firstOverdue = HistoryContract::model()->getFirstOverdueSum($header_report->bureau_id, $contract->id, $fromLastPaymentDays, $isUnsecuredCredit);
                if(($firstOverdue<=50) and ($firstOverdue>0)){ // нужно проверить по последнему варианту
                    $log .= '<br>===================Variant 4. Contract=>'.$contract->id.' ('.$contract->code.')';
                    $log .= '<br>=================== Первая просрочка=>'.$firstOverdue;
                    if($this->isPositiveContractVar4($header_report->bureau_id, $contract->id, $fromLastPaymentDays, $isUnsecuredCredit, $log)){
                        $log .= '<br>Variant 4 POSITIVE. Contract=>'.$contract->id.' ('.$contract->code.')';
                        continue;
                    }else
                        $log .= '<br>Variant 4 NEGATIVE. Contract=>'.$contract->id.' ('.$contract->code.')';
                } 
                $monthlyPayment = Contract::model()->getMonthlyPayment($contract, $header_report->bureau_id, $fromLastPaymentDays, $isUnsecuredCredit);
                if(($firstOverdue<=$monthlyPayment) and ($firstOverdue>0) and
                        !(HistoryContract::model()->isGrowthArrearsByContractCritical($header_report->bureau_id, $contract->id, $fromLastPaymentDays, $isUnsecuredCredit))){ // нужно проверить по первому варианту
                    $log .= '<br>===================Variant 1. Contract=>'.$contract->id.' ('.$contract->code.')';
                    $log .= '<br>=================== Месячный платеж=>'.$monthlyPayment;
                    $log .= '<br>=================== Первая просрочка=>'.$firstOverdue;
                    $log .= '<br>=================== Допустимый прирост просрочки';
                    if($this->isPositiveContractVar1($header_report->bureau_id, $contract->id, $fromLastPaymentDays, $isUnsecuredCredit, $need_type) ){
                        $log .= '<br>=================== Статус кредита => Исполнен';
                        $log .= '<br>Variant 1 POSITIVE. Contract=>'.$contract->id.' ('.$contract->code.')';
                        continue;
                    }else{
                        $log .= '<br>=================== Статус кредита => Просрочен';
                        $log .= '<br>Variant 1 NEGATIVE. Contract=>'.$contract->id.' ('.$contract->code.')';
                    }
                }
                if($need_type == 1){ // заявка на беззалоговый
                    $lastDelayDate = HistoryContract::model()->getLastDelayDateByContract($header_report->bureau_id, $contract->id, $fromLastPaymentDays, $isUnsecuredCredit);
                    if(isset($lastDelayDate)){
                        $log .= '<br>===================Variant 2. Contract=>'.$contract->id.' ('.$contract->code.')';
                        $log .= '<br>=================== Кредит '.($isUnsecuredCredit? 'беззалоговый':'под залог');
                        $log .= '<br>=================== Дата последней просрочки=>'.$lastDelayDate;
                        if($this->isPositiveContractVar2($header_report->bureau_id, $contract, $lastDelayDate, $fromLastPaymentDays, $isUnsecuredCredit, $log))
                            $log .= '<br>Variant 2 POSITIVE. Contract=>'.$contract->id.' ('.$contract->code.')';
                        else{
                            $log .= '<br>Variant 2 NEGATIVE. Contract=>'.$contract->id.' ('.$contract->code.')';
                            $res = $this->hist_is_negative;
                            break;
                        }
                    } else
                        $log .= '<br>ERROR Which variant use?. Contract=>'.$contract->id.' ('.$contract->code.')';
                }else { // заявка на залоговый
                    $monthlyPayment = Contract::model()->getMonthlyPayment($contract, $header_report->bureau_id, $fromLastPaymentDays, $isUnsecuredCredit);
                    $maxOverdue = HistoryContract::model()->getMaxDelaySum($header_report->bureau_id, $contract->id, $fromLastPaymentDays, $isUnsecuredCredit);
                    
                    $l_d = new DateTime(HistoryContract::model()->getLastDelayDateByContract($header_report->bureau_id, $contract->id, $fromLastPaymentDays, $isUnsecuredCredit));
                    $curr_date = new DateTime('now');
                    $c_s_d = new DateTime($contract->credit_start_date);
                    $c_e_d = new DateTime($contract->credit_end_date);
                    $years_of_credit = (int)$c_s_d->diff($c_e_d)->days/365;
                    $kmax = HistoryContract::model()->getExceedingDurationCountByContract($header_report->bureau_id, $contract->id, 91, 1200, $fromLastPaymentDays, $isUnsecuredCredit);
                    $k90 = HistoryContract::model()->getExceedingDurationCountByContract($header_report->bureau_id, $contract->id, 61, 90, $fromLastPaymentDays, $isUnsecuredCredit);
                    // проверим вариант 2b
                    if (!$isUnsecuredCredit){  // залоговый
                        $log .= '<br>=================== Кредит под залог';
                        $log .= '<br>=================== Максимальная просрочка=>'.$maxOverdue;
                        $log .= '<br>=================== Месячный платеж=>'.$monthlyPayment;
                        if($maxOverdue>$monthlyPayment*1.5) {
                            $log .= '<br>=================== Максимальная просрочка превышает пороговое значение';
                            $log .= '<br>Variant 2b NEGATIVE. Contract=>'.$contract->id.' ('.$contract->code.')';
                            $res = $this->hist_is_negative;
                            break;                            
                        }elseif(HistoryContract::model()->getLastOverdueSum($header_report->bureau_id, $contract->id, $fromLastPaymentDays, $isUnsecuredCredit)!=0){ // просрочен
                            $log .= '<br>=================== Кредит просрочен';
                            $log .= '<br>Variant 2b NEGATIVE. Contract=>'.$contract->id.' ('.$contract->code.')';
                            $res = $this->hist_is_negative;
                            break;
                        }
                        $log .= '<br>=================== Кредит исполнен';
                        if($l_d->diff($curr_date)->days<=30*3){ // дата последней просрочки <= 3х месяцев
                            $log .= '<br>=================== Дата последней просрочки меньше 3х месяцев назад';
                            $log .= '<br>Variant 2b NEGATIVE. Contract=>'.$contract->id.' ('.$contract->code.')';
                            $res = $this->hist_is_negative;
                            break;
                        }
                        $log .= '<br>=================== Дата последней просрочки не меньше 3х месяцев назад';
                        $log .= '<br>=================== K90=>'.$k90;
                        $log .= '<br>=================== Kmax=>'.$kmax;
                        $log .= '<br>=================== Количество полных лет пользования кредитом=>'.$years_of_credit;
                        if (($kmax!=0) or ($k90>$years_of_credit)){
                            $log .= '<br>=================== Превышено значение Kmax или K90';
                            $log .= '<br>Variant 2b NEGATIVE. Contract=>'.$contract->id.' ('.$contract->code.')';
                            $res = $this->hist_is_negative;
                            break;
                        }else{
                            $log .= '<br>Variant 2b POSITIVE. Contract=>'.$contract->id.' ('.$contract->code.')';
                            continue;
                        }
                    }else {                   // проверим вариант 2a беззалоговый
                        $log .= '<br>=================== Кредит беззалоговый';
                        $log .= '<br>=================== Максимальная просрочка=>'.$maxOverdue;
                        $log .= '<br>=================== Месячный платеж=>'.$monthlyPayment;
                        if($maxOverdue>$monthlyPayment){ 
                            $log .= '<br>=================== Максимальная просрочка превышает пороговое значение';
                            $log .= '<br>Variant 2a NEGATIVE. Contract=>'.$contract->id.' ('.$contract->code.')';
                            $res = $this->hist_is_negative;
                            break;
                        }
                        if(HistoryContract::model()->getLastOverdueSum($header_report->bureau_id, $contract->id, $fromLastPaymentDays, $isUnsecuredCredit)>$monthlyPayment*1.25){ // исполнен
                            $log .= '<br>=================== Текущая просрочка превышает пороговое значение';
                            $log .= '<br>Variant 2a NEGATIVE. Contract=>'.$contract->id.' ('.$contract->code.')';
                            $res = $this->hist_is_negative;
                            break;
                        }
                        if(!HistoryContract::model()->is3LastPaymentsCorrect($contract->id, $fromLastPaymentDays, $isUnsecuredCredit)){  // последняя просрочка <=30 дней, не меньше 3 последних платежей оплачены с просрочкой <=30 дней
                            $log .= '<br>=================== Или последняя просрочка > 30 дней, или 3 последних платежа оплачены с просрочкой > 30 дней';
                            $log .= '<br>Variant 2a NEGATIVE. Contract=>'.$contract->id.' ('.$contract->code.')';
                            $res = $this->hist_is_negative;
                            break;
                        }
                        $log .= '<br>=================== K90=>'.$k90;
                        $log .= '<br>=================== Kmax=>'.$kmax;
                        if(($kmax>5) or ($k90>6)) {
                            $log .= '<br>=================== Превышено значение Kmax или K90';
                            $log .= '<br>Variant 2a NEGATIVE. Contract=>'.$contract->id.' ('.$contract->code.')';
                            $res = $this->hist_is_negative;
                            break;
                        }else{
                            $log .= '<br>Variant 2a POSITIVE. Contract=>'.$contract->id.' ('.$contract->code.')';
                            continue;
                        }
                    }
                    $log .= '<br>Variant 2 NEGATIVE. Contract=>'.$contract->id.' ('.$contract->code.')';
                    $res = $this->hist_is_negative;
                    break;
                }
            }
        }
        return $res;
    }

    private function isPositiveContractVar4($bureau_id, $contract_id, $days, $isUnsecuredCredit, &$log){
        $maxOverdue = HistoryContract::model()->getMaxOverdue($bureau_id, $contract_id, $days, $isUnsecuredCredit);
        $log .= '<br>=================== Максимальная просрочка=>'.$maxOverdue;
        return $maxOverdue<=100? true:false;
    }
    private function isPositiveContractVar2($bureau_id, $contract, $lastDelayDate, $fromLastPaymentDays, $isUnsecuredCredit, &$log){
        $monthlyPayment = Contract::model()->getMonthlyPayment($contract, $bureau_id, $fromLastPaymentDays, $isUnsecuredCredit);
        $log .= '<br>=================== Месячный платеж=>'.$monthlyPayment;
        $curr_date = new DateTime('now');
        $l_d = new DateTime($lastDelayDate);
        $c_s_d = new DateTime($contract->credit_start_date);
        $c_e_d = new DateTime($contract->credit_end_date);
        $years_of_credit = (int)$c_s_d->diff($c_e_d)->days/365;
        $k90 = HistoryContract::model()->getExceedingDurationCountByContract($bureau_id, $contract->id, 61, 90, $fromLastPaymentDays, $isUnsecuredCredit);
        $kmax = HistoryContract::model()->getExceedingDurationCountByContract($bureau_id, $contract->id, 91, 1200, $fromLastPaymentDays, $isUnsecuredCredit);
        $maxOverdue = HistoryContract::model()->getMaxOverdue($bureau_id, $contract->id, $fromLastPaymentDays, $isUnsecuredCredit);
        
        $log .= '<br>=================== Максимальная просрочка=>'.$maxOverdue;
        if($l_d->diff($curr_date)->days<365){
//            $treshold = ($monthlyPayment*2)<1000? 1000:$monthlyPayment*2;
            $treshold = ($monthlyPayment*2)<500? 500:$monthlyPayment*2;
            if (($maxOverdue<=$treshold) and
                    ($k90<=(3+$years_of_credit)) and
                    ($kmax<=$years_of_credit) and 
                    (HistoryContract::model()->getLastOverdueSum($bureau_id, $contract->id, $fromLastPaymentDays, $isUnsecuredCredit)<$monthlyPayment) and
                    (HistoryContract::model()->is3LastPaymentsCorrect($contract->id, $fromLastPaymentDays, $isUnsecuredCredit)))
                return true;
        }else{
            $treshold = ($monthlyPayment*2)<1000? 1000:$monthlyPayment*2;
            if (($maxOverdue<=$treshold) and
                    ($k90<=(5+$years_of_credit)) and
                    ($kmax<=$years_of_credit) and 
                    (HistoryContract::model()->getLastOverdueSum($bureau_id, $contract->id, $fromLastPaymentDays, $isUnsecuredCredit)==0))
                return true;
        }
        return false;
     }

     private function isPositiveContractVar1($bureau_id, $contract_id, $days, $isUnsecuredCredit, $contract_type){
         // максимальная сумма просроченной задолженности
        if (HistoryContract::model()->getMaxOverdue($bureau_id, $contract_id, $days, $isUnsecuredCredit)<=1000)
                if($contract_type==1) // если заявка на беззалоговый, то проверим статус
                    if (HistoryContract::model()->getLastOverdueSum($bureau_id, $contract_id, $days, $isUnsecuredCredit)==0) 
                        return true;
                    else 
                        return false;
                else 
                    return true;
        else
            return false;
     }

     private function isPositiveContractVar3($bureau_id, $contract_id, $days, $isUnsecuredCredit){
         $ret = HistoryContract::model()->isHistoryByContract($bureau_id, $contract_id, $days, $isUnsecuredCredit);
         return !$ret;
     }
/*     
     public function makeAutoAnalyze($inn, $claim_type){
         $header_report = getLastFreshReportByInn($inn); // ищем "свежий" отчет (не старше 30 дней)
         $log = '';
         if(!isset($header_report)) // если "свежего" отчета нет
             $this->makeRequestToBureaus($inn); // делаем запрос в БКИ
         $res = $this->analyzeOnly($inn, $claim_type, $log); // делаем анализ
         return $res; // возвращаем результат история положительная (true)/ отрицательная (false)
     }
*/

//     public function analyze($header_report){
//         $contracts = Contract::model()->getContractsByReport($header_report->id);
//         foreach ($contracts as $contract) {
//             if($this->isPositiveContractVar1($contract->id) )
//             ;
//         }
//         var_dump ($header_report->attributes);
//         $monthlyPayment = $this->getMonthlyPayment($header_report->id, $header_report->bureau_id);
//         echo 'Последний кредитный отчет поступил из '.($header_report->bureau_id==2? 'УБКИ':'МБКИ');
//         echo '<br>ИНН: '.$header_report->taxpayer_number;
//         echo '<br>Ежемесячные платежи (затраты по истории) = '.$monthlyPayment;
//         echo '<br>Размер кредитной истории (максимальная кредитная нагрузка) = '.$this->getMaxLoanBurden($header_report->id);
//         $low = 61;
//         $high = 90;
//         echo '<br>Просрочки платежа от '.$low.' до '.$high.' дней = '.HistoryContract::model()->getExceedingDurationCount($header_report->id, $low, $high);
//         $low = 51;
//         $high = 1000;
//         echo '<br>Количество просрочек на сумму от '.$low.' до '.$high.' = '.HistoryContract::model()->getSumOfDelayCount($header_report->id, $low, $high);
//         $sum=100;
//         echo '<br>Количество дней с момента последней просрочки на сумму более '.$sum.' = '.HistoryContract::model()->getLastDelayBySumDays($header_report->id, $sum);
//         echo '<br>Размер позитивной истории в днях после последней просрочки ='.HistoryContract::model()->getSizePositiveHistory($header_report->id);
//         echo '<br>Рост задолженности превышает 100(50)грн.=>'.HistoryContract::model()->isGrowthDelayCritical($header_report->id);
//         echo '<br>Max просрочка = '.HistoryContract::model()->getMaxAmountOfDelay($header_report->id);
//     }   
     
//     private function getMonthlyPayment($report_id, $bureau_id){
//         $res=0;
//         $contracts = Contract::model()->getContractsByReport($report_id);
//         foreach ($contracts as $key => $contract) {
//            if(($bureau_id == 2 and $contract->contract_type_code == 5) or ($bureau_id == 3 and $contract->contract_type_code == 4))
//                 $res += $contract->total_amount*0.07;  // C C
//            else {
//                $e_d = isset($contract->credit_end_date)?(new DateTime($contract->credit_end_date)):(new DateTime('2100-01-01'));
//                $s_d = new DateTime($contract->credit_start_date);
//                $ms = $e_d->diff($s_d)->days;
//                $m_p = 30*$contract->total_amount/$ms;
//                $res += ($m_p>$contract->monthly_instalment_amount)? $m_p:$contract->monthly_instalment_amount;
//            }
//         }
//         return $res;
//     }
//     private function getMaxLoanBurden($report_id){
//         $res=0;
//         $d = Contract::model()->getLoanBurdenData($report_id);
//         $s=0;
//         foreach ($d as $v) {
//             $s = ($v->flag == 'S')? ($s+$v->money):($s-$v->money);
//             if ($s>$res)
//                 $res=$s;
//         }
//         return $res;
//     }
//     private function isExceedingDuration(){
//         
//     }
//     private function getTotalDebt(){
//         
//     }
//     private function getMin36Date(){
//         $curr_date = new DateTime('now');
//         return $curr_date->sub(date_interval_create_from_date_string('3 years')); ;
//     }
//     private function getMin60Date(){
//         $curr_date = new DateTime('now');
//         return $curr_date->sub(date_interval_create_from_date_string('5 years')); ;
//     }
//     private function getMin12Date(){
//         $curr_date = new DateTime('now');
//         return $curr_date->sub(date_interval_create_from_date_string('12 months')); ;
//     }
//     private function isHistoryByContract($contract_id){
//         if (HistoryContract::model()->isGrowthArrearsByContractCritical($contract_id))
//             return false;
//     }
}
