<?php

class ReportMBKIController extends Controller
{
//    public $creditTypes = array('Кредитный договор на другие потребительские цели',
//        'Обеспеченная ссуда',
//        'Необеспеченная ссуда',
//        '3',
//        '4',
//        'Кредитная карта',
//        'Товары в кредит');
    public function actionIndex()
    {
            $this->render('index');
    }

    public function actionGetINN(){
        if (isset($_POST['my_button'])){
            $this->redirect(array('getReportByINN','inn'=>$_POST['inn']));
        }
        $this->render('getINN');
    }
    public function actionGetReportByINN(){
        $last_xml_report = XmlReport::model()->getLastReport($_GET['inn']);
        if (isset($last_xml_report)){
             $xml = new SimpleXMLElement($last_xml_report->xml_report);
             switch ($last_xml_report->bureau_id) {
                 case 2: // UBKI
//                     var_dump($xml->r[8]->URATING); //->LST['ruLName']);
//                     $lastname = $this->query_attribute($xml->r, "key", "4");
//                     $lastname =$xml->r[7]->xpath('//CL_DEAL[@Reference="758367.1875777"]');
//                     var_dump($lastname);
//                     var_dump($xml->r[7]->xpath('//758367.1875777'));
                     $bki_report = new UbkiReport($xml);
//                     var_dump($bki_report->rating);
                     $this->actionShowUbkiReportJq($bki_report);
                 break;
                 case 3: // MBKI
                     $bki_report = new MbkiReport($xml);
                     $this->actionShowReportJq($bki_report);
                 break;
             }
//             $lastname = $this->query_attribute($xml->r, "key", "5")->LST['uaLName'];
        } else 
             $bki_report = '';
//        $this->redirect(array('showReportJq','inn'=>$_GET['inn'])); //, 'report'=> $bki_report));
        
     }
     
     public function actionShowReportJq($bki_report){
         $this->render('showReportJq',array('inn'=>$_GET['inn'], 'report'=>$bki_report));
     }
     public function actionShowUbkiReportJq($bki_report){
         $this->render('showUbkiReportJq',array('inn'=>$_GET['inn'], 'report'=>$bki_report));
     }
     public function actionGetAddresses(){
        $responce['rows']=array();
        foreach ($_GET['addresses'] as $i=>$a) {
                $responce['rows'][$i]['id'] = $i+1;
                $responce['rows'][$i]['cell'] = array(
                    0,
                    $a['type'],
                    $a['street'],
                    $a['city'],
                    $a['zip'],
                    $a['region'],
                    $a['area'],
                    $a['country']
                    );
            }
            echo CJSON::encode($responce);
     }
     public function actionGetIdDocs(){
        $responce['rows']=array();
        foreach ($_GET['idDocs'] as $i=>$a) {
                $responce['rows'][$i]['id'] = $i;
                $responce['rows'][$i]['cell'] = array(
                    $a['idDocName'],
                    $a['docNumber'],
                    $a['issuedDate'],
                    $a['issuedBy'],
                    $a['region'],
                    '',
                    ''
                    );
            }
            echo CJSON::encode($responce);
     }
     public function actionGetRelations(){
        $responce['rows']=array();
        foreach ($_GET['relations'] as $i=>$r) {
                $responce['rows'][$i]['id'] = $i;
                $responce['rows'][$i]['cell'] = array(
                    $r['state'],
                    $r['providerCode'],
                    $r['jobName'],
                    $r['companyName'],
                    $r['registrationNumber'],
                    $r['startDate'],
                    $r['address'],
                    $r['subjectsPosition']
                    );
            }
            echo CJSON::encode($responce);
     }
     public function actionGetSummaryInformations(){
        $responce['rows']=array();
        foreach ($_GET['summaryInformations'] as $i=>$si) {
                $responce['rows'][$i]['id'] = $i;
                $responce['rows'][$i]['cell'] = array(
                    $si['contractType'],
                    $si['numberOfExistingContracts'],
                    $si['numberOfTerminatedContracts'],
                    $si['numberOfUnsolvedApplications'],
                    $si['numberOfRejectedApplications'],
                    $si['numberOfRevokedApplications'],
                    $si['totalValue'].' '.$si['totalCurrency'],
                    $si['value'].' '.$si['currency'],
                    $si['numberOfUnpaidInstalments']
                    );
            }
            echo CJSON::encode($responce);
     }
     public function actionGetAuthHistory(){
        $responce['rows']=array();
        $i=0;
//        var_dump($_GET['auth_hist']);
        foreach ($_GET['auth_hist'] as $ah) {
//            var_dump($ah);
                $responce['rows'][$i]['id'] = $i;
                $responce['rows'][$i]['cell'] = array(
                    '1', //$ah['clDate'],
                    '1', //$ah['ruLName'].' '.$ah['ruFName'].' '.$ah['ruMName'].' (рус)',
                    '1', //$ah['familySt']=='Y'? 'женат/замужем':'',
                    '1', //$ah['db'],
                    '1', //$ah['sex']=='M'? 'мужской':'женский',
                    '1', //$ah['wokpo']
                    );
                $i++;
            }
            echo CJSON::encode($responce);
     }
    private function query_attribute($xmlNode, $attr_name, $attr_value) {
        foreach($xmlNode as $node) { 
          switch($node[$attr_name]) {
            case $attr_value:
              return $node;
          }
        }
    }
     
    // Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

         * 	*/
}