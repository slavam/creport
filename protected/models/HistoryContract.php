<?php

/**
 * This is the model class for table "history_contracts".
 *
 * The followings are the available columns in table 'history_contracts':
 * @property integer $id
 * @property integer $contract_id
 * @property integer $year
 * @property integer $month
 * @property integer $factor_id
 * @property integer $factor_value_id
 * @property string $value
 * string payment_date
 */
class HistoryContract extends CActiveRecord
{
    public $delay_count;
    public $sum_delay_count;
    public $outstanding_amount;
    public $max_delay;
    /**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return HistoryContract the static model class
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
		return 'history_contracts';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('contract_id, year, month, factor_id, factor_value_id', 'numerical', 'integerOnly'=>true),
			array('value', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, contract_id, year, month, factor_id, factor_value_id, value, payment_date', 'safe', 'on'=>'search'),
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
			'contract_id' => 'Contract',
			'year' => 'Year',
			'month' => 'Month',
			'factor_id' => 'Factor',
			'factor_value_id' => 'Factor Value',
			'value' => 'Value',
                        'payment_date' => 'Payment Date',
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
		$criteria->compare('contract_id',$this->contract_id);
		$criteria->compare('year',$this->year);
		$criteria->compare('month',$this->month);
		$criteria->compare('factor_id',$this->factor_id);
		$criteria->compare('factor_value_id',$this->factor_value_id);
		$criteria->compare('value',$this->value,true);
                $criteria->compare('payment_date',$this->payment_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
        public function getExceedingDurationCount($report_id, $low, $high){ // #1.15
            // границы интервала ВХОДЯТ, т.е. <= , >=
            $sql = "
                select count(*) as delay_count from history_contracts hc
                join contracts c on c.id = hc.contract_id and c.report_id=".$report_id."
                where factor_id =8 and CAST(coalesce(value, '0') AS integer)  between ".$low." and ".$high ;
            $c = $this->model()->findBySql($sql);
            return $c->delay_count;
        }
        public function getSumOfDelayCount($report_id, $low, $high){ // #1.15
            $sql = "
                select count(*) as sum_delay_count from history_contracts hc
                join contracts c on c.id = hc.contract_id and c.report_id=".$report_id."
                where factor_id =10 and -CAST(coalesce(value, '0') AS real)  between ".$low." and ".$high ; // sum is negative
            $c = $this->model()->findBySql($sql);
            return $c->sum_delay_count;
        }
        public function getLastDelayBySumDays($report_id, $sum){
            // возвращает количество дней до последней просрочки более заданной суммы
            $sql = "
                select * from history_contracts hc
                join contracts c on c.id = hc.contract_id and c.report_id=".$report_id."
                where factor_id =10 
                and -CAST(coalesce(value, '0') AS real) > ".$sum."
                order by year desc, month desc limit 1";
            $c = $this->model()->findBySql($sql);
            $l_d = new DateTime($c->year.'-'.$c->month.'-01');
            $curr_date = new DateTime("now");
            return $curr_date->diff($l_d)->days;
        }
        public function getSizePositiveHistory($report_id){ // размер (в днях) позитивной кредитной истории после последней просрочки
            $sql = "
                select hc1.year as year, hc1.month as month from contracts c 
                join history_contracts hc1 on c.id=hc1.contract_id and hc1.factor_id=8 and CAST(coalesce(hc1.value, '0') AS integer) > 90
                join history_contracts hc2 on c.id=hc2.contract_id and hc2.factor_id=10 and -CAST(coalesce(hc2.value, '0') AS real) > 50
                where report_id =".$report_id."
                order by hc1.year desc, hc1.month desc limit 1";
            $c = $this->model()->findBySql($sql);
            $last_delay_date = new DateTime($c->year.'-'.$c->month.'-01');
            $sql2="
                select hc.year as year, hc.month as month from history_contracts hc
                join contracts c on c.id = hc.contract_id and c.report_id=".$report_id."
                order by year desc, month desc limit 1 
                ";
            $c2 = $this->model()->findBySql($sql2);
            $last_payment_date = new DateTime($c2->year.'-'.$c2->month.'-01');
            return $last_payment_date->diff($last_delay_date)->days;
        }
        public function getMaxAmountOfDelay($report_id){  // max сумма просрочки
            $sql="
                select -min(CAST(coalesce(value, '0') AS real)) as max_delay from history_contracts hc
                join contracts c on c.id=hc.contract_id and c.report_id=".$report_id."
                where factor_id in (10) and value !='0' and year>=(date_part('year', now())-3)
                ";
            $c = $this->model()->findBySql($sql);
            return $c->max_delay;
        }
        public function getArrears($report_id){ // сумма общей задолженности
            $sql ="
                select sum(outstanding_amount) as outstanding_amount from contracts where report_id=".$report_id;
            $c = $this->model()->findBySql($sql);
            return -$c->outstanding_amount;
        }
        public function isGrowthDelayCritical($report_id){
            $sql = "
                select hc.* from history_contracts hc
                join contracts c on c.id=hc.contract_id and c.report_id=".$report_id."
                where factor_id = 10 and value !='0' and year>=(date_part('year', now())-3)
                order by c.id, hc.id";
            $cs = $this->model()->findAllBySql($sql);
            $c_id=0;
            $v=0;
            $curr_date = new DateTime('now');
            foreach ($cs as $c) {
                if($c_id!=$c->contract_id){
                    $c_id = $c->contract_id;
                    $v=-$c->value;
                } else{
                    $d=new DateTime($c->year.'-'.$c->month.'-01');
                    if($d->diff($curr_date)->days > 365){
                        if ($v+$c->value>100)
                            return true;
                    }else{
                        if ($v+$c->value>50)
                            return true;
                    }
                    $v =-$c->value;
                }
            }
            return false;
        }
        public function isGrowthArrearsByContractCritical($contract_id){
            $sql="
                select * from history_contracts 
                where factor_id = 10 and value !='0' and payment_date>(CURRENT_TIMESTAMP - INTERVAL '37 months') and contract_id =".$contract_id."
                order by payment_date";
            $hcs = $this->model()->findAllBySql($sql);
            if(count($hcs)<1)                
                return false;
            $c_id=0;
            $v=0;
            $curr_date = new DateTime('now');
            foreach ($hcs as $c) {
                if($c_id!=$c->contract_id){
                    $c_id = $c->contract_id;
                    $v=-$c->value;
                } else{
                    $d=new DateTime($c->payment_date);
                    if($d->diff($curr_date)->days > 365){
                        if ((-$c->value-$v)>100)
                            return true;
                    }else{
                        if ((-$c->value-$v)>50)
                            return true;
                    }
                    $v =-$c->value;
                }
            }
            return false;
        }
        public function getMaxAmountOfDelayByContract($contract_id){  // max сумма просрочки
            $sql="
                select -min(CAST(coalesce(value, '0') AS real)) as max_delay from history_contracts 
                where factor_id = 10 and value !='0' and payment_date>(CURRENT_TIMESTAMP - INTERVAL '37 months') and contract_id =".$contract_id;
            $c = $this->model()->findBySql($sql);
            return isset($c)? $c->max_delay:0;
        }
        public function getLastDelayDateByContract($contract_id){
            $sql="
                select max(payment_date) as payment_date from history_contracts 
                where factor_id = 10 and value !='0' and payment_date>(CURRENT_TIMESTAMP - INTERVAL '37 months') and contract_id =".$contract_id;
            $c = $this->model()->findBySql($sql);
            return isset($c)? $c->payment_date:null;
        }
        public function getExceedingDurationCountByContract($contract_id, $low, $high){ // #1.15
            // границы интервала ВХОДЯТ, т.е. <= , >=
            $sql = "
                select count(*) as delay_count from history_contracts hc                
                where factor_id=8 and contract_id=".$contract_id." and CAST(coalesce(value, '0') AS integer)  between ".$low." and ".$high."
                and payment_date>(CURRENT_TIMESTAMP - INTERVAL '37 months')" ;
            $c = $this->model()->findBySql($sql);
            return isset($c)? $c->delay_count:0;
        }
        public function getLastDelayByContract($contract_id){
            $sql="
                select CAST(coalesce(value, '0') AS real) as value from history_contracts 
                where factor_id = 10 and payment_date>(CURRENT_TIMESTAMP - INTERVAL '37 months') and contract_id =".$contract_id."         
                order by payment_date desc limit 1";
            $c = $this->model()->findBySql($sql);
            return isset($c)? $c->value:null;
        }
        public function isHistoryByContract($contract_id, $days, $isUnsecuredCredit){ // есть ли история по кредиту? 
            if($isUnsecuredCredit)
                if($days>365*3)
                    $sql="select count(*) as id from history_contracts where payment_date >(CURRENT_TIMESTAMP - INTERVAL '61 months') and factor_id=5 and contract_id=".$contract_id;
                else
                    $sql="select count(*) as id from history_contracts where payment_date >(CURRENT_TIMESTAMP - INTERVAL '37 months') and factor_id=5 and contract_id=".$contract_id;
            else 
                $sql="select count(*) as id from history_contracts where factor_id=5 and contract_id=".$contract_id;
            
            $c = $this->model()->findBySql($sql);
            return $c->id>3 ? true:false;
        }
        public function getFirstOverdueSum($contract_id, $days, $isUnsecuredCredit){ // с какой суммы возникла история
            if($isUnsecuredCredit)
                if($days>365*3)
                    $sql="select * from history_contracts 
                        where payment_date >(CURRENT_TIMESTAMP - INTERVAL '61 months') and factor_id=10 and value != '0' and contract_id=".$contract_id.
                        " order by payment_date limit 1";
                else
                    $sql="select * from history_contracts 
                        where payment_date >(CURRENT_TIMESTAMP - INTERVAL '37 months') and factor_id=10 and value != '0' and contract_id=".$contract_id.
                        " order by payment_date limit 1";
            else 
                $sql="select * from history_contracts where factor_id=10 and value != '0' and contract_id=".$contract_id." order by payment_date limit 1";
            
            $c = $this->model()->findBySql($sql);
            return abs($c->value);
        }
        public function getMaxOverdue($contract_id, $days, $isUnsecuredCredit){ // максимальная просрочка за всю историю
            if($isUnsecuredCredit)
                if($days>365*3)
                    $sql="select * from history_contracts 
                        where payment_date >(CURRENT_TIMESTAMP - INTERVAL '61 months') and factor_id=10 and value != '0' and contract_id=".$contract_id.
                        " order by CAST(coalesce(value, '0') AS real) limit 1";
                else
                    $sql="select * from history_contracts 
                        where payment_date >(CURRENT_TIMESTAMP - INTERVAL '37 months') and factor_id=10 and value != '0' and contract_id=".$contract_id.
                        " order by CAST(coalesce(value, '0') AS real) limit 1";
            else 
                $sql="select * from history_contracts where factor_id=10 and value != '0' and contract_id=".$contract_id." order by CAST(coalesce(value, '0') AS real) limit 1";
            
            $c = $this->model()->findBySql($sql);
            return abs($c->value);
        }
}