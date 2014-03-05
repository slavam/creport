<?php

class ReportMBKIController extends Controller
{
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
    
//    public function actionGetINN(){
//        if (isset($_POST['my_button'])){
//            $this->redirect(array('getReportByINN','inn'=>$_POST['inn']));
//        }
//        $this->render('getINN');
//    }
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

    public function createNativeQueryRecord($xml_report, $description){
        $query = new NativeQuerie();
        $query->author = $this->get_client_ip();
        $query->created_at = date("Y-m-d H:i:s", time());
        $query->taxpayer_number = $xml_report->tax_payer_number;
        $query->bureau_id = $xml_report->bureau_id;
        $query->result = $description;
        $query->save();
    }

    public function actionGetReportByINN(){
        $last_xml_report = XmlReport::model()->getLastReport($_GET['inn']);
        $this->createNativeQueryRecord($last_xml_report, 'Отчет выбран из таблицы xml_reports');
        
        if (isset($last_xml_report)){
             $xml = new SimpleXMLElement($last_xml_report->xml_report);
//             var_dump(unserialize('77'));
             switch ($last_xml_report->bureau_id) {
                 case 2: // UBKI
//                     if ($this->actionSaveCreditReport($xml))
//                         var_dump ("Сохранено");
                     $bki_report = new UbkiReport($xml);
//                     var_dump(unserialize(serialize($bki_report)));
                     $this->actionShowUbkiReportJq($bki_report);
                 break;
                 case 3: // MBKI
//                     if ($this->actionSaveCreditReport($xml))
//                         var_dump ("Сохранено");
//                     var_dump($xml->Report->Subject); //->xpath('//SummaryInformation'));
                     $bki_report = new MbkiReport($xml);
//                     var_dump(unserialize(serialize($bki_report)));
                     $this->actionShowReportJq($bki_report);
                 break;
             }
//             $lastname = $this->query_attribute($xml->r, "key", "5")->LST['uaLName'];
        } else {
             $bki_report = '';
             $this->redirect(array('inn'));
        }
     }
     
     public function actionShowReportJq($bki_report){
         $this->render('showReportJq',array('inn'=>$_GET['inn'], 'report'=>$bki_report));
     }
     public function actionShowUbkiReportJq($bki_report){
         $this->render('showUbkiReportJq',array('inn'=>$_GET['inn'], 'report'=>$bki_report));
     }
     public function actionSaveCreditReport($xml){
         if(isset($xml)){
             $id = $xml->r->trace['ReqID'];
             $current_time = date("Y-m-d H:i:s", time());
             if (isset($id)){ // UBKI
                 $credit_report = new UbkiReport($xml);
                 $report = new Report();
                 $report->bureau_id = 2;
                 $report->created_at = $current_time;
                 $report->code_from_bureau = $xml->r->trace['ReqID'];
                 $report->issue_date = $credit_report->clDate;
                 $report->taxpayer_number = $credit_report->okpo;
                 $report->report_type_id = 2;
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
                            $contract->contract_type_id = $ctr['creditType'];
                            $contract->is_onen = $ctr['flClose']=='N'? true:false;
                            $contract->code=$ctr['reference'];
                            $contract->currency_id=$ctr['currencyCode']=='UAH'? 1:($ctr['currencyCode']=='USD'? 2: ($ctr['currencyCode']=='EUR'? 3:null));
                            $contract->role_id=1; //$ctr['subjectRoleCode']=='1'? 1:null;
//                            $contract->application_date = $ctr['dateOfApplication'];
                            $contract->credit_start_date =  $ctr['startDate']>''? $ctr['startDate']:null;
                            $contract->credit_end_date=  $ctr['stopDate'] == 'null'? null:$ctr['stopDate'];
                            $contract->total_amount=$ctr['amount'];
                            $contract->outstanding_amount=$ctr['AmtCurr'];
//                            $contract->monthly_instalment_amount=$ctr['monthlyInstalmentAmountValue'];
                            $contract->overdue_amount=$ctr['amtExp'];
//                            var_dump($ctr['crSetAmount']);
                            $contract->save();
                            foreach ($ctr['payments'] as $p) {
                                $hist = new HistoryContract();
                                $hist->contract_id = $contract->id;
                                $hist->month = $p['month'];
                                $hist->year = $p['year'];
                                $hist->value=$p['flPay'];
                                $hist->factor_id=5;
                                $hist->save();
                                
                                $hist = new HistoryContract();
                                $hist->contract_id = $contract->id;
                                $hist->month = $p['month'];
                                $hist->year = $p['year'];
                                $hist->value=$p['amtCurr'];
                                $hist->factor_id=6;
                                $hist->save();
                                
                                $hist = new HistoryContract();
                                $hist->contract_id = $contract->id;
                                $hist->month = $p['month'];
                                $hist->year = $p['year'];
                                $hist->value=$p['amtExp'];
                                $hist->factor_id=10;
                                $hist->save();
                                
                                $hist = new HistoryContract();
                                $hist->contract_id = $contract->id;
                                $hist->month = $p['month'];
                                $hist->year = $p['year'];
                                $hist->value=$ctr['crSetAmount'];
                                $hist->factor_id=7;
                                $hist->save();
                                
                                $hist = new HistoryContract();
                                $hist->contract_id = $contract->id;
                                $hist->month = $p['month'];
                                $hist->year = $p['year'];
                                $hist->value=$p['daysExp'];
                                $hist->factor_id=8;
                                $hist->save();
                                
                                $hist = new HistoryContract();
                                $hist->contract_id = $contract->id;
                                $hist->month = $p['month'];
                                $hist->year = $p['year'];
                                $hist->value=$p['flUse'];
                                $hist->factor_id=9;
                                $hist->save();
                            }
                        }
                        foreach ($credit_report->query_hist as $il){ 
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
                 $credit_report = new MbkiReport($xml);
                 $report = new Report();
                 $report->bureau_id = 3;
                 $report->created_at = $current_time;
                 $report->code_from_bureau = $credit_report->mbkiId; // usageIdentity;
                 $report->issue_date = $credit_report->updated; //issueDate;
                 $report->taxpayer_number = $credit_report->taxpayerNumber;
                 $report->report_type_id = 1;
                if ($report->save()){
                    $ch_subject = new ChSubject();
                    $ch_subject->report_id = $report->id;
                    $ch_subject->surname_ru = $credit_report->surname;
                    $ch_subject->firstname_ru =$credit_report->name;
                    $ch_subject->middlename_ru = $credit_report->fathersName;
//                    $ch_subject->$surname_ua = $credit_report->
//                    $firstname_ua
//                    $middlename_ua
                    $ch_subject->birth_date=$credit_report->dateOfBirth;
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
                            $contract->contract_type_id = $ctr['importCode'];
                            $contract->is_onen = $ctr['contractType']=='Existing'? true:false;
                            $contract->code=$ctr['codeOfContract'];
                            $contract->currency_id=$ctr['currencyCode']=='UAH'? 1:($ctr['currencyCode']=='USD'? 2: ($ctr['currencyCode']=='EUR'? 3:null));
                            $contract->role_id=$ctr['subjectRoleCode']=='1'? 1:null;
                            $contract->application_date = $ctr['dateOfApplication']>''? $ctr['dateOfApplication']:null;
                            $contract->credit_start_date =$ctr['creditStartDate']>''? $ctr['creditStartDate']:null;
                            $contract->credit_end_date=$ctr['contractEndDate']>''? $ctr['contractEndDate']:null;
                            $contract->total_amount=$ctr['totalAmountValue'];
                            $contract->outstanding_amount= +$ctr['outstandingAmountValue'];
                            $contract->monthly_instalment_amount= +$ctr['monthlyInstalmentAmountValue'];
                            $contract->overdue_amount=$ctr['overdueAmountValue'];
//                            var_dump($ctr);
                            $contract->save();
                            foreach ($ctr['months'] as $ms) 
                                foreach ($ms as $i => $m) {
                                if ($m>'' and $i!='description'){
                                    $slash_pos = stripos($m, '/');
                                    $month = substr($m, 0, $slash_pos);
                                    $year = '20'.substr($m, $slash_pos+1);
                                    if(count($ctr['hCTotalNumberOfOverdueInstalments'])>1){
                                        $hist = new HistoryContract();
                                        $hist->month = $month;
                                        $hist->year = $year;
                                        $hist->value=$ctr['hCTotalNumberOfOverdueInstalments'][$i]['value'];
                                        $hist->contract_id = $contract->id;
                                        $hist->factor_id=1;
                                        $hist->save();
                                    }
                                    if(count($ctr['hCTotalOverdueAmount'])>1){
                                        $hist = new HistoryContract();
                                        $hist->month = $month;
                                        $hist->year = $year;
                                        $hist->value=$ctr['hCTotalOverdueAmount'][$i]['value'];
                                        $hist->contract_id = $contract->id;
                                        $hist->factor_id=2;
                                        $hist->save();
                                    }
                                    if(count($ctr['hCResidualAmount'])>1){
                                        $hist = new HistoryContract();
                                        $hist->month = $month;
                                        $hist->year = $year;
                                        $hist->value=$ctr['hCResidualAmount'][$i]['value'];
                                        $hist->contract_id = $contract->id;
                                        $hist->factor_id=3;
                                        $hist->save();
                                    }
                                    if(count($ctr['hCCreditCardUsedInMonth'])>1){
                                        $hist = new HistoryContract();
                                        $hist->month = $month;
                                        $hist->year = $year;
                                        $hist->value=$ctr['hCCreditCardUsedInMonth'][$i]['value'];
                                        $hist->contract_id = $contract->id;
                                        $hist->factor_id=4;
                                        $hist->save();
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
                                        $hist = new HistoryContract();
                                        $hist->month = $month;
                                        $hist->year = $year;
                                        $hist->value=$ctr['hCTotalNumberOfOverdueInstalments24'][$i]['value'];
                                        $hist->contract_id = $contract->id;
                                        $hist->factor_id=1;
                                        $hist->save();
                                    }
                                    if(count($ctr['hCTotalOverdueAmount24'])>1){
                                        $hist = new HistoryContract();
                                        $hist->month = $month;
                                        $hist->year = $year;
                                        $hist->value=$ctr['hCTotalOverdueAmount24'][$i]['value'];
                                        $hist->contract_id = $contract->id;
                                        $hist->factor_id=2;
                                        $hist->save();
                                    }
                                    if(count($ctr['hCResidualAmount24'])>1){
                                        $hist = new HistoryContract();
                                        $hist->month = $month;
                                        $hist->year = $year;
                                        $hist->value=$ctr['hCResidualAmount24'][$i]['value'];
                                        $hist->contract_id = $contract->id;
                                        $hist->factor_id=3;
                                        $hist->save();
                                    }
                                    if(count($ctr['hCCreditCardUsedInMonth24'])>1){
                                        $hist = new HistoryContract();
                                        $hist->month = $month;
                                        $hist->year = $year;
                                        $hist->value=$ctr['hCCreditCardUsedInMonth24'][$i]['value'];
                                        $hist->contract_id = $contract->id;
                                        $hist->factor_id=4;
                                        $hist->save();
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
     public function actionGetParamForAnalyze(){
        $model=new AnalyzeForm;
        if(isset($_POST['InnForm'])){
            $model->attributes=$_POST['InnForm'];
            if($model->validate())
                $this->redirect(array('analyze','inn'=>$model->inn)); //$_POST['inn']));
        }
        $this->render('getParamForAnalyze',array('model'=>$model));
     }
     public function actionAnalyze(){
         $inn = $_GET['inn'];
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
                    $get_report_date = new DateTime($native_report[0]->created_at); //issue_date);
                    if($curr_date->diff($get_report_date)->days<31) // report is actual
                        if($native_report->report_type_id!=3)   // report is real
                            $this->redirect(array('getReportByINN','inn'=>$model->inn)); // show report
                }
//                 send request into mbki
                $mbkiResponse = $this->getDataFromMbki($model->inn);
                if(isset($mbkiResponse->Root->reportNotAvailable)){ // mbki history is absent
                    $report = new Report();
                    $report->report_type_id = 3; // reportNotAvailable
                    $report->bureau_id = 3;
                    $report->created_at = date("Y-m-d H:i:s", time());
                    $report->code_from_bureau = $mbkiResponse->Report->SubjectInfo->CreditinfoId; 
                    $report->issue_date = $mbkiResponse->Report['issued'];
                    $report->taxpayer_number = $mbkiResponse->Report->SubjectCode;
                    $report->save();
                }else
                    $this->actionSaveCreditReport($mbkiResponse); // save report into DB

                // send request into ubki
                $ubkiResponse = $this->getDataFromUbki($model->inn); // request
                $this->actionSaveCreditReport($ubkiResponse); // save report into DB
                
                $mbki_report_date = isset($mbkiResponse)? new DateTime($mbkiResponse->Report['issued']):null;
                $ubki_report_date = isset($ubkiResponse)? new DateTime($ubkiResponse->r[1]->LST['CLDATE']):null;
                if ($mbki_report_date > $ubki_report_date)
                    if(isset($mbkiResponse->Root->reportNotAvailable))
                        $this->redirect(array('historyIsAbsent','inn'=>$mbkiResponse->Report->SubjectCode,'date'=>$mbkiResponse->Report['issued']));
                    else
                        $this->redirect(array('getReportByINN','inn'=>$model->inn)); 
                else 
                    $this->redirect(array('getReportByINN','inn'=>$model->inn)); 
            }
        }
        $this->render('innForQuery',array('model'=>$model));
    }
    
    private function getDataFromUbki($inn){
        $login = "v.morhachov";
        $passw = "ntcnbhjdfybt";
        $typerequest = 'ALL';
        if( $curl = curl_init() ) {
            curl_setopt($curl, CURLOPT_URL, 'https://www.ubki2.com.ua/api/xmlrequest.php?login='.$login.'&passw='.$passw.'&typerequest='.$typerequest.'&inn='.$inn.'&lnameua=&fnameua=&mnameua=&lnameru=&fnameru=&mnameru=&bdate=coding=&pser=&pnom=');
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
//            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,true);
            curl_setopt($curl, CURLOPT_CAINFO, getcwd()."/cacert.pem"); 
//                    curl_setopt($curl, CURLOPT_CAINFO, "D:\\Sites\\yii\\demos\\creport\\cacert.pem"); getcwd().
            $out = curl_exec($curl);
//    $result = curl_multi_getcontent ($curl);

            if(!$out){
                echo curl_error($curl);
                curl_close($curl);
                return null;
            } else {
                $xml = new SimpleXMLElement($out);
                $this->createRawXmlRecord($xml);
//            var_dump($xml);
                curl_close($curl);
                return $xml;
            }
        }
    }
    private function getDataFromMbki($inn){
        $client = new CreditHistorySoapClass();
        $response1 = $client->callSearchFrontOffice($inn,'130');
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
}