<h2>Введите параметры кредитного отчета для анализа</h2>

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
        <div class="row">
		<?php echo "Сумма кредита, запрошенная клиентом: "; ?>
		<?php echo $form->textField($model,'requested_amount'); ?>
		<?php echo $form->error($model,'inn'); ?>
	</div>
        <div class="row">
		<?php echo "Срок пользования кредитом (месяцы): "; ?>
		<?php echo $form->textField($model,''); ?>
		<?php echo $form->error($model,'inn'); ?>
	</div>
        <div class="row">
		<?php echo "Тип кредита: "; ?>
		<?php echo $form->textField($model,''); ?>
		<?php echo $form->error($model,'inn'); ?>
	</div>
    <br>
    <div class="row buttons">
        <?php echo CHtml::submitButton('Продолжить',array('name'=>'my_button')); ?>
    </div>
<?php $this->endWidget(); ?>
</div><!-- form -->
