<h2>Введите ИНН клиента для просмотра кредитного отчета</h2>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'inn-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>
    <br>
        <div class="row">
		<?php echo "ИНН клиента: "; ?>
		<?php echo $form->textField($model,'inn'); ?>
		<?php echo $form->error($model,'inn'); ?>
	</div>
    <br>
    <div class="row buttons">
        <?php echo CHtml::submitButton('Показать',array('name'=>'my_button')); ?>
    </div>
<?php $this->endWidget(); ?>
</div><!-- form -->
