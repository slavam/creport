<?php

class AnalyzeForm extends CFormModel
{
	public $inn;
        public $requested_amount;
        public $number_months;
        public $credit_type_id;

        /**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
                    array('inn', 'required'),
                    array('inn, number_months, credit_type_id', 'numerical', 'integerOnly'=>true),
                    array('requested_amount','numerical'),
                    array('inn', 'validateIsReport'), //, 'on'=>'my_test'),
		);
	}
        public function validateIsReport($attribute,$params)
        {       
            $r = Report::model()->getLastReportByInn($this->inn);
            if (isset($r))
                 return true;
            else
                $this->addError($attribute, 'Для этого ИНН в системе нет данных для анализа. Выполните запрос в БКИ и повторите анализ');
        }
        
}
?>
