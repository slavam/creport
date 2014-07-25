<?php
class NativeQuerieController extends Controller{
    public function actionIndexJq(){
        $this->render('indexJq');
    }
    public function actionGetQueries(){
//        $queries = NativeQuerie::model()->findAll(array('order' => 'user_id ASC, created_at DESC'));
        $queries = NativeQuerie::model()->getQueries($_GET['start_date'], $_GET['stop_date']);
        $responce['rows']=array();
        foreach ($queries as $i=>$q) {
            $responce['rows'][$i]['id'] = $i+1;
            $responce['rows'][$i]['cell'] = array(
                $q->id,
                isset($q->user_id)? $q->user->login:'Гость-'.$q->author,
                $q->taxpayer_number,
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
    public function actionGetParams(){
        $model = new ParamsQuerie;
        if (isset($_POST['my_button'])){
            $model->attributes=$_POST['ParamsQuerie'];
            if($model->validate())
                $this->redirect(array('indexJq','start_date'=>$model->start_date, 'stop_date'=>$model->stop_date));
        }
        $this->render('getParams',array('model'=>$model));
    }
}
?>
