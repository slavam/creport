<h3>
<?php if($result>'') echo $result; ?>
</h3>
<?php $form=$this->beginWidget('CActiveForm', array(
'id'=>'topic-form',
'enableAjaxValidation'=>false,
'htmlOptions' => array('enctype' => 'multipart/form-data','multiple'=>'multiple'), // ADD THIS
 )); ?>

<div class="row">
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