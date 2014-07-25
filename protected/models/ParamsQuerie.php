<?php
class ParamsQuerie extends CFormModel{
    public $start_date;
    public $stop_date;
    public function rules()
	{
		return array(
                    array('start_date, stop_date', 'required'),
                    array('start_date, stop_date', 'date', 'format'=>'dd.MM.yyyy'),
                    array('start_date, stop_date', 'safe')
                    );
        }
}
?>
