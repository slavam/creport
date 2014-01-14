<?php

/**
 * This is the model class for table "xml_reports".
 *
 * The followings are the available columns in table 'xml_reports':
 * @property integer $id
 * @property string $tax_payer_number
 * @property string $xml_report
 * @property integer $bureau_id
 * @property string $created_at
 * @property string $chb_report_id
 */
class XmlReport extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return XmlReport the static model class
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
		return 'xml_reports';
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
			array('tax_payer_number, xml_report, created_at, chb_report_id', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, tax_payer_number, xml_report, bureau_id, created_at, chb_report_id', 'safe', 'on'=>'search'),
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
			'tax_payer_number' => 'Tax Payer Number',
			'xml_report' => 'Xml Report',
			'bureau_id' => 'Bureau',
			'created_at' => 'Created At',
			'chb_report_id' => 'Chb Report',
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
		$criteria->compare('tax_payer_number',$this->tax_payer_number,true);
		$criteria->compare('xml_report',$this->xml_report,true);
		$criteria->compare('bureau_id',$this->bureau_id);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('chb_report_id',$this->chb_report_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
        public function getLastReport($inn){
            $sql ='
                select * from xml_reports 
                where tax_payer_number='.$inn.' order by created_at desc limit 1';
            return $this->findBySql($sql);
        }
}