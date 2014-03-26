<h2>Введите параметры для анализа кредитного отчета</h2>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'analyze-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>
    <br>
        <div class="row">
            <b>ИНН клиента: </b>
            <br>
		<?php echo $form->textField($model,'inn'); ?>
		<?php echo $form->error($model,'inn'); ?>
	</div>
        <div class="row">
            <b>Сумма кредита, запрошенная клиентом: </b>
            <br>
		<?php echo $form->textField($model,'requested_amount'); ?>
		<?php echo $form->error($model,'requested_amount'); ?>
	</div>
        <div class="row">
            <b>Срок пользования кредитом (месяцы): </b>
            <br>
		<?php echo $form->textField($model,'number_months'); ?>
		<?php echo $form->error($model,'number_months'); ?>
	</div>
        <div class="row">
            <b>Тип кредита: </b>
            <br>
            <?php echo $form->dropDownList($model, 'credit_type_id', CHtml::listData(CreditType::model()->findAll(), 'id', 'name')); ?>
	</div>
    <br>
    <div class="row buttons">
        <?php echo CHtml::submitButton('Продолжить',array('name'=>'my_button')); ?>
    </div>
<?php $this->endWidget(); ?>
</div><!-- form -->
