<h2>Введите ИНН клиента для поиска кредитного отчета</h2>
<div class="form">
<?php echo CHtml::beginForm();?>
    <br>
    <div class="row">
	<?php echo "ИНН клиента: "; ?>
        <?php echo CHtml::textField('inn', $inn, array('size'=> 20,'maxlength'=>10)); ?>
    </div>
    <br>
    <div class="row buttons">
        <?php echo CHtml::submitButton('Отобрать',array('name'=>'my_button')); ?>
    </div>
<?php echo CHtml::endForm();?>
</div><!-- form -->