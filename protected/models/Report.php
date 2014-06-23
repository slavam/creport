<?php

/**
 * This is the model class for table "reports".
 *
 * The followings are the available columns in table 'reports':
 * @property integer $id
 * @property integer $bureau_id
 * @property string $created_at
 * @property string $code_from_bureau
 * @property string $issue_date
 * @property string $taxpayer_number
 * @property integer $report_type_id
 */
class Report extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Report the static model class
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
		return 'reports';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('bureau_id, report_type_id', 'numerical', 'integerOnly'=>true),
			array('created_at, code_from_bureau, issue_date, taxpayer_number', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, bureau_id, report_type_id, created_at, code_from_bureau, issue_date, taxpayer_number', 'safe', 'on'=>'search'),
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
                    'bureau' => array(self::BELONGS_TO, 'Bureau', 'bureau_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'bureau_id' => 'Bureau',
			'created_at' => 'Created At',
			'code_from_bureau' => 'Code From Bureau',
			'issue_date' => 'Issue Date',
                        'taxpayer_number' => 'Taxpayer Number',
                        'report_type_id' => 'Report Type'
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
		$criteria->compare('bureau_id',$this->bureau_id);
                $criteria->compare('report_type_id',$this->report_type_id);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('code_from_bureau',$this->code_from_bureau,true);
		$criteria->compare('issue_date',$this->issue_date,true);
                $criteria->compare('taxpayer_number',$this->taxpayer_number,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
        public function getLastReportInBureauByInn($inn, $bureau_id){
            $sql = "select * from reports where taxpayer_number = '".$inn."' and bureau_id=".$bureau_id." order by issue_date desc limit 1";
            $res = $this->model()->findAllBySql($sql);
            return count($res)>0? $res:null;
        }
        public function getLastReportByInn($inn){
            $sql = "select * from reports where taxpayer_number = '".$inn."' and report_type_id !=3 order by issue_date desc, created_at desc limit 1";
            $res = $this->model()->findAllBySql($sql);
            return count($res)>0? $res[0]:null;
        }
        public function getLastFreshReportByInn($inn){
            $sql = "select * from reports where taxpayer_number = '".$inn."' and issue_date >(CURRENT_TIMESTAMP - INTERVAL '30 days') and report_type_id !=3 order by issue_date desc, created_at desc limit 1";
            $res = $this->model()->findAllBySql($sql);
            return count($res)>0? $res[0]:null;
        }
        public function getReportsByInn($inn){
            $sql = "select * from reports where taxpayer_number = '".$inn."' order by created_at desc, issue_date desc";
            $res = $this->model()->findAllBySql($sql);
            return count($res)>0? $res:null;
        }
}