<?php

/**
 * This is the model class for table "contracts".
 *
 * The followings are the available columns in table 'contracts':
 * @property integer $id
 * @property integer $contract_type_id
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
 */
class Contract extends CActiveRecord
{
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
			array('contract_type_id, ch_subject_id, currency_id, role_id, report_id', 'numerical', 'integerOnly'=>true),
			array('is_onen, code, application_date, credit_start_date, credit_end_date, total_amount, outstanding_amount, monthly_instalment_amount, overdue_amount, created_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, contract_type_id, ch_subject_id, is_onen, code, currency_id, role_id, application_date, credit_start_date, credit_end_date, total_amount, outstanding_amount, monthly_instalment_amount, overdue_amount, created_at, report_id', 'safe', 'on'=>'search'),
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
			'contract_type_id' => 'Contract Type',
			'ch_subject_id' => 'Ch Subject',
			'is_onen' => 'Is Onen',
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
		$criteria->compare('contract_type_id',$this->contract_type_id);
		$criteria->compare('ch_subject_id',$this->ch_subject_id);
		$criteria->compare('is_onen',$this->is_onen);
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

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}