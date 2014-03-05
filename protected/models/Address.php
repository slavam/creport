<?php

/**
 * This is the model class for table "addresses".
 *
 * The followings are the available columns in table 'addresses':
 * @property integer $id
 * @property integer $country_id
 * @property integer $address_type_id
 * @property string $zip
 * @property string $code
 * @property string $addr1
 * @property integer $ch_subject_id
 * @property double $latitude
 * @property double $longitude
 * @property string $created_at
 * @property integer $report_id
 */
class Address extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Address the static model class
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
		return 'addresses';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('country_id, address_type_id, ch_subject_id, report_id', 'numerical', 'integerOnly'=>true),
			array('latitude, longitude', 'numerical'),
			array('zip, code, addr1, created_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, country_id, address_type_id, zip, code, addr1, ch_subject_id, latitude, longitude, created_at, report_id', 'safe', 'on'=>'search'),
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
			'country_id' => 'Country',
			'address_type_id' => 'Address Type',
			'zip' => 'Zip',
			'code' => 'Code',
			'addr1' => 'Addr1',
			'ch_subject_id' => 'Ch Subject',
			'latitude' => 'Latitude',
			'longitude' => 'Longitude',
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
		$criteria->compare('country_id',$this->country_id);
		$criteria->compare('address_type_id',$this->address_type_id);
		$criteria->compare('zip',$this->zip,true);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('addr1',$this->addr1,true);
		$criteria->compare('ch_subject_id',$this->ch_subject_id);
		$criteria->compare('latitude',$this->latitude);
		$criteria->compare('longitude',$this->longitude);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('report_id',$this->report_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}