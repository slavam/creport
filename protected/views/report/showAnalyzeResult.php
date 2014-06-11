<h2>Результаты анализа кредитного отчета СКИ с ИНН <? 
echo '<a href='.Yii::app()->createUrl("report/getReportByINN").'?inn='.$inn.'>'.$inn.' </a>'.($type==1?' (Кредит без залога)':' (Кредит под залог)'); ?> 
</h2>

<?php echo $log; ?>
<br>
<br>
<h2>Вердикт: <? echo $verdict ?></h2>
