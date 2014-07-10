<?php
class NativeQuerieController extends Controller{
    public function actionIndexJq(){
        $this->render('indexJq');
    }
    public function actionGetQueries(){
        $queries = NativeQuerie::model()->findAll(array('order' => 'user_id ASC, created_at DESC'));
        $responce['rows']=array();
        foreach ($queries as $i=>$q) {
            $responce['rows'][$i]['id'] = $i+1;
            $responce['rows'][$i]['cell'] = array(
                $q->id,
                isset($q->user_id)? $q->user->login:'Гость-'.$q->author,
                $q->taxpayer_number,
//                $q->result,
                $q->action->name,
                $q->created_at
                );
        }
        echo CJSON::encode($responce);
    }
    public function actionGetReportsByInn(){
        $responce['rows']=array();
        if($_GET['inn']>''){
            $reports = Report::model()->getReportsByInn($_GET['inn']);
            foreach ($reports as $i=>$r) {
                $responce['rows'][$i]['id'] = $i+1;
                $responce['rows'][$i]['cell'] = array(
                    $r->id,
                    $r->code_from_bureau,
                    $r->bureau_id==2? 'УБКИ':'МБКИ',
                    $r->issue_date,
                    $r->created_at
                    );
            }
        }
        echo CJSON::encode($responce);
    }
//    public function actionShowReportByStamp(){
//        $last_report_by_bureau = XmlReport::model()->getLastReportByBureau($_GET['inn'],$_GET['bureau_id']);
//        switch ($last_xml_report->bureau_id) {
//            case 2: // UBKI
//                $bki_report = new UbkiReport($xml);
//                $this->actionShowUbkiReportJq($bki_report, $last_xml_report->created_at);
//            break;
//            case 3: // MBKI
////                     var_dump($xml->Report->Subject); //->xpath('//SummaryInformation'));
//                $bki_report = new MbkiReport($xml);
//                $this->actionShowReportJq($bki_report, $last_xml_report->created_at);
//            break;
//        }        
//    }
}
?>
