<?php
//class Attach
//{
//    public $name;
//    public $exten;
//    public $file;
//}

class CrmForm extends CFormModel
{
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

        /**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
                    array('type', 'required'),
//                    array('inn, number_months, credit_type_id', 'numerical', 'integerOnly'=>true),
//                    array('requested_amount','numerical'),
//                    array('inn', 'validateIsReport'), //, 'on'=>'my_test'),
                    array('emailSenderName, description, type, mailBody, lastName, firstName, middleName, phone, interestProduct, interestFANumber', 'safe', 'on'=>'search'),
                    
		);
	}
//        public function validateIsReport($attribute,$params)
//        {       
//            $r = Report::model()->getLastReportByInn($this->inn);
//            if (isset($r))
//                 return true;
//            else
//                $this->addError($attribute, 'Для этого ИНН в системе нет данных. Выполните запрос в БКИ и повторите анализ');
//        }
        
}
?>
