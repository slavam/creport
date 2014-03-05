<?php

/**
 * This is the model class for table "workplaces".
 *
 * The followings are the available columns in table 'workplaces':
 * @property integer $id
 * @property integer $ch_subject_id
 * @property string $name
 * @property string $code
 * @property string $profession
 * @property string $address
 * @property string $start_date
 * @property string $created_at
 * @property integer $report_id
 */
class Workplace extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Workplace the static model class
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
		return 'workplaces';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('ch_subject_id, report_id', 'numerical', 'integerOnly'=>true),
			array('name, code, profession, address, start_date, created_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, ch_subject_id, name, code, profession, address, start_date, created_at, report_id', 'safe', 'on'=>'search'),
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
                    'report' => array(self::BELONGS_TO, 'Report', 'report_id'),
                    'ch_subject' => array(self::BELONGS_TO, 'ChSubject', 'ch_subject_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'ch_subject_id' => 'Ch Subject',
			'name' => 'Name',
			'code' => 'Code',
			'profession' => 'Profession',
			'address' => 'Address',
			'start_date' => 'Start Date',
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
		$criteria->compare('ch_subject_id',$this->ch_subject_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('profession',$this->profession,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('start_date',$this->start_date,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('report_id',$this->report_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}