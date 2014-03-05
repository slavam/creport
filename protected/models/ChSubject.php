<?php

/**
 * This is the model class for table "ch_subjects".
 *
 * The followings are the available columns in table 'ch_subjects':
 * @property integer $id
 * @property string $surname_ru
 * @property string $firstname_ru
 * @property string $middlename_ru
 * @property string $surname_ua
 * @property string $firstname_ua
 * @property string $middlename_ua
 * @property string $birth_date
 * @property integer $gender_id
 * @property string $taxpayer_number
 * @property boolean $is_resident
 * @property integer $nationality_id
 * @property integer $citizenship_id
 * @property integer $education_id
 * @property integer $marital_status_id
 * @property string $photo_base64
 * @property string $created_at
 * @property integer $report_id
 */
class ChSubject extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ChSubject the static model class
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
		return 'ch_subjects';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('gender_id, nationality_id, citizenship_id, education_id, marital_status_id, report_id', 'numerical', 'integerOnly'=>true),
			array('taxpayer_number', 'length', 'max'=>10),
			array('surname_ru, firstname_ru, middlename_ru, surname_ua, firstname_ua, middlename_ua, birth_date, is_resident, photo_base64, created_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, surname_ru, firstname_ru, middlename_ru, surname_ua, firstname_ua, middlename_ua, birth_date, gender_id, taxpayer_number, is_resident, nationality_id, citizenship_id, education_id, marital_status_id, photo_base64, created_at, report_id', 'safe', 'on'=>'search'),
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
                    'gender' => array(self::BELONGS_TO, 'Gender', 'gender_id'),
                    'nationality' => array(self::BELONGS_TO, 'Nationality', 'nationality_id'),
                    'citizenship' => array(self::BELONGS_TO, 'Citizenship', 'citizenship_id'),
                    'education' => array(self::BELONGS_TO, 'Education', 'education_id'),
                    'marital_status' => array(self::BELONGS_TO, 'MaritalStatus', 'marital_status_id'),
                    'report' => array(self::BELONGS_TO, 'Report', 'report_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'surname_ru' => 'Surname Ru',
			'firstname_ru' => 'Firstname Ru',
			'middlename_ru' => 'Middlename Ru',
			'surname_ua' => 'Surname Ua',
			'firstname_ua' => 'Firstname Ua',
			'middlename_ua' => 'Middlename Ua',
			'birth_date' => 'Birth Date',
			'gender_id' => 'Gender',
			'taxpayer_number' => 'Taxpayer Number',
			'is_resident' => 'Is Resident',
			'nationality_id' => 'Nationality',
			'citizenship_id' => 'Citizenship',
			'education_id' => 'Education',
			'marital_status_id' => 'Marital Status',
			'photo_base64' => 'Photo Base64',
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
		$criteria->compare('surname_ru',$this->surname_ru,true);
		$criteria->compare('firstname_ru',$this->firstname_ru,true);
		$criteria->compare('middlename_ru',$this->middlename_ru,true);
		$criteria->compare('surname_ua',$this->surname_ua,true);
		$criteria->compare('firstname_ua',$this->firstname_ua,true);
		$criteria->compare('middlename_ua',$this->middlename_ua,true);
		$criteria->compare('birth_date',$this->birth_date,true);
		$criteria->compare('gender_id',$this->gender_id);
		$criteria->compare('taxpayer_number',$this->taxpayer_number,true);
		$criteria->compare('is_resident',$this->is_resident);
		$criteria->compare('nationality_id',$this->nationality_id);
		$criteria->compare('citizenship_id',$this->citizenship_id);
		$criteria->compare('education_id',$this->education_id);
		$criteria->compare('marital_status_id',$this->marital_status_id);
		$criteria->compare('photo_base64',$this->photo_base64,true);
		$criteria->compare('created_at',$this->created_at,true);
                $criteria->compare('report_id',$this->report_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}