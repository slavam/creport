<?php

/**
 * This is the model class for table "id_documents".
 *
 * The followings are the available columns in table 'id_documents':
 * @property integer $id
 * @property integer $document_type_id
 * @property integer $ch_subject_id
 * @property string $created_at
 * @property string $number
 * @property string $issued_by
 * @property string $issue_date
 * @property integer $report_id
 */
class IdDocument extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return IdDocument the static model class
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
		return 'id_documents';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('document_type_id, ch_subject_id, report_id', 'numerical', 'integerOnly'=>true),
			array('created_at, number, issued_by, issue_date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, document_type_id, ch_subject_id, created_at, number, issued_by, issue_date, report_id', 'safe', 'on'=>'search'),
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
			'document_type_id' => 'Document Type',
			'ch_subject_id' => 'Ch Subject',
			'created_at' => 'Created At',
			'number' => 'Number',
			'issued_by' => 'Issued By',
			'issue_date' => 'Issue Date',
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
		$criteria->compare('document_type_id',$this->document_type_id);
		$criteria->compare('ch_subject_id',$this->ch_subject_id);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('number',$this->number,true);
		$criteria->compare('issued_by',$this->issued_by,true);
		$criteria->compare('issue_date',$this->issue_date,true);
		$criteria->compare('report_id',$this->report_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}