<?php

/**
 * This is the model class for table "contracts".
 *
 * The followings are the available columns in table 'contracts':
 * @property integer $id
 * @property integer $contract_type_code
 * @property integer $ch_subject_id
 * @property boolean $is_onen
 * @property string $code
 * @property integer $currency_id
 * @property integer $role_id
 * @property string $application_date
 * @property string $credit_start_date
 * @property string $credit_end_date
 * @property string $total_amount
 * @property string $outstanding_amount
 * @property string $monthly_instalment_amount
 * @property string $overdue_amount
 * @property string $created_at
 * @property integer $report_id
 * @property integer $credit_type_id
 */
class Contract extends CActiveRecord
{
    public $date;
    public $flag;
    public $money;
    /**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Contract the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'contracts';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('contract_type_code, ch_subject_id, currency_id, role_id, report_id, credit_type_id', 'numerical', 'integerOnly'=>true),
			array('is_open, code, application_date, credit_start_date, credit_end_date, total_amount, outstanding_amount, monthly_instalment_amount, overdue_amount, created_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, contract_type_code, ch_subject_id, is_open, code, currency_id, role_id, application_date, credit_start_date, credit_end_date, total_amount, outstanding_amount, monthly_instalment_amount, overdue_amount, created_at, report_id, credit_type_id', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'contract_type_code' => 'Contract Type Code',
			'ch_subject_id' => 'Ch Subject',
			'is_open' => 'Is Open',
			'code' => 'Code',
			'currency_id' => 'Currency',
			'role_id' => 'Role',
			'application_date' => 'Application Date',
			'credit_start_date' => 'Credit Start Date',
			'credit_end_date' => 'Credit End Date',
			'total_amount' => 'Total Amount',
			'outstanding_amount' => 'Outstanding Amount',
			'monthly_instalment_amount' => 'Monthly Instalment Amount',
			'overdue_amount' => 'Overdue Amount',
			'created_at' => 'Created At',
			'report_id' => 'Report',
                        'credit_type_id' => 'Credit type',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('contract_type_code',$this->contract_type_code);
		$criteria->compare('ch_subject_id',$this->ch_subject_id);
		$criteria->compare('is_open',$this->is_open);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('currency_id',$this->currency_id);
		$criteria->compare('role_id',$this->role_id);
		$criteria->compare('application_date',$this->application_date,true);
		$criteria->compare('credit_start_date',$this->credit_start_date,true);
		$criteria->compare('credit_end_date',$this->credit_end_date,true);
		$criteria->compare('total_amount',$this->total_amount,true);
		$criteria->compare('outstanding_amount',$this->outstanding_amount,true);
		$criteria->compare('monthly_instalment_amount',$this->monthly_instalment_amount,true);
		$criteria->compare('overdue_amount',$this->overdue_amount,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('report_id',$this->report_id);
                $criteria->compare('credit_type_id',$this->credit_type_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
        public function getContractsByReport($report_id){
            $sql ="select * from contracts  where report_id =".$report_id;
            $c = $this->model()->findAllBySql($sql);
            return count($c)>0? $c:null;
        }
        public function getOpenContractsByReport($report_id){
            $sql ="select * from contracts  where report_id =".$report_id." and is_open";
            $c = $this->model()->findAllBySql($sql);
            return count($c)>0? $c:null;
        }
        public function getLoanBurdenData($report_id){
            $sql = "
                (select credit_start_date as date, 'S' as flag, total_amount as money from contracts where total_amount>0 and report_id=".$report_id."
                union
                select credit_end_date as date, 'E' as flag, total_amount as money from contracts where total_amount>0 and  report_id=".$report_id." )
                order by date";
            $c = $this->model()->findAllBySql($sql);
            return count($c)>0? $c:null;
        }
        public function getLongCreditHistory($report_id){
            $sql = "select * from contracts where report_id =".$report_id." order by credit_start_date limit 1"; // get min start date
            $c = $this->model()->findBySql($sql);
            $min_start_date = $c->credit_start_date;
            //??????????
        }
        public function getLastPaymentDate($report_id, $bureau_id){
            if($bureau_id==2){ //ubki
            // по требованию ВМА от 20140704
//                $sql = "select h_c.payment_date as application_date from history_contracts h_c
//                            join contracts c on c.id=h_c.contract_id and report_id=".$report_id."
//                            where factor_id=5 and value = 'Y' 
//                            order by payment_date desc limit 1";
                $sql = "select h_c.payment_date as application_date from history_contracts h_c
                            join contracts c on c.id=h_c.contract_id and report_id=".$report_id."
                            where factor_id=6 and (CAST(coalesce(value, '0') AS real))!=0 
                            order by payment_date desc limit 1";
                $c = $this->model()->findBySql($sql);
                return count($c)>0? $c->application_date : '';
            }else{
                $sql="select h_c.payment_date as application_date from history_contracts h_c
                        join contracts c on c.id=h_c.contract_id and report_id=".$report_id."
                        where contract_id>0 and factor_id=2 and (CAST(coalesce(value, '0') AS real))=0
                        order by payment_date desc limit 1";
                $c = $this->model()->findBySql($sql);
                return count($c)>0? $c->application_date : '';
            }
        }
        public function isUnsecuredCredit($contract_type_code, $bureau_id){ // беззалоговый?
            if(isset($this->credit_type_id))
                return $this->credit_type_id==1? true:false;
            if($bureau_id == 2){ //ubki
                if(($contract_type_code==1) or ($contract_type_code==3) or ($contract_type_code==4))
                    return false;
                else 
                    return true;
            }else{ // mbki
                if($contract_type_code==1)
                    return false;
                else 
                    return true;
            }
            return true;
        }
        public function getMonthlyPayment($contract, $bureau_id, $days, $isUnsecuredCredit){
            if(($bureau_id == 2 and $contract->contract_type_code == 5) or ($bureau_id == 3 and $contract->contract_type_code == 4))
                $res = $contract->total_amount*0.07;  // C C
            else {
                if(isset($contract->credit_end_date))
                    $e_d = new DateTime($contract->credit_end_date); 
                else {
                    $e_d = new DateTime(HistoryContract::model()->getLastPaymentDate($contract->id, $days, $isUnsecuredCredit));
                }
                $s_d = new DateTime($contract->credit_start_date);
                $days_of_credit = $s_d->diff($e_d)->days;
                $m_p = 30*$contract->total_amount/$days_of_credit;
                $res = ($m_p>$contract->monthly_instalment_amount)? $m_p:$contract->monthly_instalment_amount;
            }
            if($res==0)
                return ($days<366)? 500:1000;
            else
                return $res;
        }
}