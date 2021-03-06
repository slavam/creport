<?php

class InnForm extends CFormModel
{
	public $inn;
        public $ubkiLogin = "";
        public $ubkiPassword = ''; //vbr_test'; //"ntcnbhjdfybt";
        public $mbkiLogin = ''; //'test.vbr';
        public $mbkiPassword = '';//'Test@1234';

        /**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
                    array('inn', 'required'),
                    array('inn', 'numerical', 'integerOnly'=>true),
                    array('inn', 'length', 'is'=>10),
                    array('inn', 'validateIsReport', 'on'=>'my_test'),
		);
	}
        public function validateIsReport($attribute,$params)
        {       
            $r = XmlReport::model()->getLastReport($this->inn);
            if (count($r)>0)
                return true;
            else
                $this->addError($attribute, 'Для этого ИНН в базе нет данных');
        }
}
?>
