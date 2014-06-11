<h1>По запросу для ИНН <?  echo $inn; ?> </h1>
<?php
if($response==0)
    echo '<h2>В обоих бюро информация отсутствует</h2>';
if($response==3){
    echo '<h2>Из УБКИ получен отчет. <a href='.Yii::app()->createUrl('report/showLastReportByBureau').'?bureau_id=2&inn='.$inn.'>Просмотреть</a></h2>';
    echo '<h2>Из МБКИ получен отчет. <a href='.Yii::app()->createUrl('report/showLastReportByBureau').'?bureau_id=3&inn='.$inn.'>Просмотреть</a></h2>';
}
?>