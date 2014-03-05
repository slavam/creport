<?php

/**
 * This is the model class for table "inquiries".
 *
 * The followings are the available columns in table 'inquiries':
 * @property integer $id
 * @property integer $report_id
 * @property string $inquiry_date
 * @property string $inquiry_id
 * @property string $inquiry_type
 * @property integer $ch_subject_id
 * @property string $created_at
 */
class Inquirie extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Inquirie the static model class
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
		return 'inquiries';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('report_id, ch_subject_id', 'numerical', 'integerOnly'=>true),
			array('inquiry_date, inquiry_id, inquiry_type, created_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, report_id, inquiry_date, inquiry_id, inquiry_type, ch_subject_id, created_at', 'safe', 'on'=>'search'),
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
			'report_id' => 'Report',
			'inquiry_date' => 'Inquiry Date',
			'inquiry_id' => 'Inquiry',
			'inquiry_type' => 'Inquiry Type',
			'ch_subject_id' => 'Ch Subject',
			'created_at' => 'Created At',
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
		$criteria->compare('report_id',$this->report_id);
		$criteria->compare('inquiry_date',$this->inquiry_date,true);
		$criteria->compare('inquiry_id',$this->inquiry_id,true);
		$criteria->compare('inquiry_type',$this->inquiry_type,true);
		$criteria->compare('ch_subject_id',$this->ch_subject_id);
		$criteria->compare('created_at',$this->created_at,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}