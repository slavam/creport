<h2>Введите данные для тестовой формы</h2>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'crm-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
    'htmlOptions' => array('enctype' => 'multipart/form-data','multiple'=>'multiple'), // ADD THIS
)); ?>
    <br>
        <div class="row">
            <b>Адрес отправителя: </b>
            <br>
		<?php echo $form->textField($model,'emailSenderName'); ?>
		<?php echo $form->error($model,'emailSenderName'); ?>
	</div>
        <div class="row">
            <b>Тема обращения: </b>
            <br>
		<?php echo $form->textField($model,'description'); ?>
		<?php echo $form->error($model,'description'); ?>
	</div>
        <div class="row">
            <b>Текст обращения: </b>
            <br>
            <?php echo $form->textField($model,'mailBody'); ?>
	</div>
        <div class="row">
            <b>Тип обращения: </b>
            <br>
            <?php echo $form->textField($model,'type'); ?>
	</div>
        
        <div class="row">
            <b>Фамилия: </b>
            <br>
            <?php echo $form->textField($model,'lastName'); ?>
	</div>
        <div class="row">
            <b>Имя: </b>
            <br>
            <?php echo $form->textField($model,'firstName'); ?>
	</div>
        <div class="row">
            <b>Отчество: </b>
            <br>
            <?php echo $form->textField($model,'middleName'); ?>
	</div>
        <div class="row">
            <b>Контактный номер телефона: </b>
            <br>
            <?php echo $form->textField($model,'phone'); ?>
	</div>
        <div class="row">
            <b>Интересующий продукт: </b>
            <br>
            <?php echo $form->textField($model,'interestProduct'); ?>
	</div>
        <div class="row">
            <b>Номер договора: </b>
            <br>
            <?php echo $form->textField($model,'interestFANumber'); ?>
	</div>
    <br>

<div class="row">
    <div class="row">
            <b>Вложения: </b>
            <br>
	</div>
<?php
    $this->widget('CMultiFileUpload', array(
            'name' => 'image_name',
            'model'=> $model,
            'id'=>'imagepath',
            'accept' => 'xml', // useful for verifying files
            'duplicate' => 'Duplicate file!', // useful, i think
            'denied' => 'Invalid file type', // useful, i think
        ));
?>
<div class="row buttons">
    <?php echo CHtml::submitButton('Сохранить',array('name'=>'my_button')); ?>
</div>
</div>

 <?php $this->endWidget(); ?>    
    
</div><!-- form -->
