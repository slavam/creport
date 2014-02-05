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
    private function query_attribute($xmlNode, $attr_name, $attr_value) {
            foreach($xmlNode as $node) { 
              switch($node[$attr_name]) {
                case $attr_value:
                  return $node;
              }
            }
          }
    private function is_report_in_db($xml){
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
        if (isset($_POST['my_button'])){
            foreach ($_FILES['image_name']['tmp_name'] as $key => $value) {
                $xml = simplexml_load_file($value);
                if (!$this->is_report_in_db($xml)) {
                    $id = $xml->r->trace['ReqID'];
                    $model->attributes = array();
                    if (isset($id)){ // UBKI
                        $model->tax_payer_number = $xml->r[1]->LST['OKPO'];
    //                    query_attribute($xml->r, "key", "5")->LST['DSer']
                        $model->xml_report = (string)$xml->asXML();
                        $model->bureau_id = 2;
                        $model->created_at = date("Y-m-d H:i:s", time());
                        $model->chb_report_id = $id;
                        $model->save();
    //                    var_dump($model);
                    } else { // mbki
                        $model->tax_payer_number = $xml->Report->Subject->TaxpayerNumber;
                        $model->xml_report = (string)$xml->asXML();
                        $model->bureau_id = 3;
                        $model->created_at = date("Y-m-d H:i:s", time());
                        $model->chb_report_id = $xml->Report->Subject->CreditinfoId;
                        $model->save();                    
                    }
                }
            }
//            $images = CUploadedFile::getInstancesByName('image_name');
        }

//        var_dump("out side if");
        $this->render('multipleupload',array('model'=>$model));
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
	*/
}