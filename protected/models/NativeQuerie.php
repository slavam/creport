<?php

/**
 * This is the model class for table "native_queries".
 *
 * The followings are the available columns in table 'native_queries':
 * @property integer $id
 * @property string $created_at
 * @property string $taxpayer_number
 * @property integer $bureau_id
 * @property string $author
 * @property string $result
 * @property string $request
 */
class NativeQuerie extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return NativeQuerie the static model class
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
		return 'native_queries';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('bureau_id', 'numerical', 'integerOnly'=>true),
			array('created_at, taxpayer_number, author, result, request', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, created_at, taxpayer_number, bureau_id, author, result, request', 'safe', 'on'=>'search'),
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
			'created_at' => 'Created At',
			'taxpayer_number' => 'Taxpayer Number',
			'bureau_id' => 'Bureau',
			'author' => 'Author',
			'result' => 'Result',
			'request' => 'Request',
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
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('taxpayer_number',$this->taxpayer_number,true);
		$criteria->compare('bureau_id',$this->bureau_id);
		$criteria->compare('author',$this->author,true);
		$criteria->compare('result',$this->result,true);
		$criteria->compare('request',$this->request,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}