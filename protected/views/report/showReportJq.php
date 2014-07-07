<?php
$cs = Yii::app()->clientScript;
 
$cs->registerCssFile(Yii::app()->request->baseUrl.'/jqgrid/themes/ui.jqgrid.css');
$cs->registerCssFile(Yii::app()->request->baseUrl.'/jqgrid/themes/redmond/jquery-ui-custom.css');
 
$cs->registerScriptFile(Yii::app()->request->baseUrl.'/jqgrid/js/jquery.js');
$cs->registerScriptFile(Yii::app()->request->baseUrl.'/jqgrid/js/i18n/grid.locale-ru.js');
$cs->registerScriptFile(Yii::app()->request->baseUrl.'/jqgrid/js/jquery.jqGrid.min.js');
//$cs->registerScriptFile(Yii::app()->request->baseUrl.'/jqgrid/js/jquery.jqGrid.js');
$cs->registerScriptFile(Yii::app()->request->baseUrl.'/jqgrid/js/jquery-ui-custom.min.js');
$cs->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.form.js');

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<style type="text/css">
    th.ui-th-column div {
            /* see http://stackoverflow.com/a/7256972/315935 for details */
            word-wrap: break-word;      /* IE 5.5+ and CSS3 */
            white-space: -moz-pre-wrap; /* Mozilla, since 1999 */
            white-space: -pre-wrap;     /* Opera 4-6 */
            white-space: -o-pre-wrap;   /* Opera 7 */
            white-space: pre-wrap;      /* CSS3 */
            overflow: hidden;
            height: auto !important;
            vertical-align: middle;
        }
        .ui-jqgrid tr.jqgrow td {
            white-space: normal !important;
            height: auto;
            vertical-align: middle;
            padding-top: 2px;
            padding-bottom: 2px;
        }
        .ui-jqgrid .ui-jqgrid-htable th.ui-th-column {
            padding-top: 2px;
            padding-bottom: 2px;
        }
        .ui-jqgrid .frozen-bdiv, .ui-jqgrid .frozen-div {
            overflow: hidden;
        }
        .my-table, .my-table td, .my-table th {
            border: 1px #aaa solid;
        }
</style>
<script type="text/javascript">
   $.jgrid.no_legacy_api = true;
   $.jgrid.useJSON = true;
</script>

<h2>Международное Бюро Кредитных Историй</h2>
<h3>Отчет сохранен в базе <?echo $date; ?></h3>
<? 
if(!Yii::app()->user->isGuest){
    echo '<h3><a href='.Yii::app()->createUrl("report/showAnalyzeResult").'?inn='.$inn.'&type=1>Анализ (Кредит без залога) </a><br>'; 
    echo '<a href='.Yii::app()->createUrl("report/showAnalyzeResult").'?inn='.$inn.'&type=3>Анализ (Кредит без залога без справки о доходах) </a><br>'; 
    echo '<a href='.Yii::app()->createUrl("report/showAnalyzeResult").'?inn='.$inn.'&type=2>Анализ (Кредит залоговый) </a></h3><br>'; 
}
?> 

<?php
echo '<h3>ID МБКИ: '.$report->mbkiId.'</h3>';
echo '<h3>Личная информация</h3>';
?>
<table class="table_edit">
    <tr>
    	<td>Фамилия:</td>
    	<td><?php echo $report->surname; ?></td>
    	<td>Классификация:</td>
    	<td><?php echo $report->classificationName; ?></td>
    <tr>
    	<td>Имя:</td>
    	<td><?php echo $report->name; ?></td>
    	<td>Дата рождения:</td>
    	<td><?php echo $report->dateOfBirth; ?></td>
    <tr>
    	<td>Отчество:</td>
    	<td><?php echo $report->fathersName; ?></td>
    	<td>Пол:</td>
    	<td><?php echo $report->genderName; ?></td>
    <tr>
    	<td>Фамилия при рождении:</td>
    	<td><?php echo $report->birthName; ?></td>
    	<td>Резидент?</td>
    	<td><?php echo $report->residencyName; ?></td>
    <tr>
    	<td>Идентификационный номер:</td>
    	<td><?php echo $report->taxpayerNumber; ?></td>
    	<td>Гражданство:</td>
    	<td><?php echo $report->nationality; ?></td>
    <tr>
    	<td>Гражданский паспорт:</td>
    	<td><?php echo $report->passport; ?></td>
    	<td>Образование:</td>
    	<td><?php echo $report->educationName; ?></td>
</table>
<table id="list"></table> 

<script type="text/javascript">
$(function() {
    var grid=$("#list");
    grid.jqGrid( {
        datatype: 'local',
        width : '1000',
        shrinktofit:false,
        height : 'auto',
        mtype : 'GET',
        colNames : ['Тип адреса','Улица','Город','Индекс','Район','Область','Страна'],
        colModel : [
            {name:'type', width:80},
            {name:'street', width:200},
            {name:'city', width:80},
            {name:'zip', width:40},
            {name:'region', width:80},
            {name:'area', width:100},
            {name:'country', width:100}
        ],
        caption : 'Адреса',
        rowNum : 300000,
        pgbuttons: false,     // disable page control like next, back button
        pgtext: null,  
    	loadError: function(xhr, status, error) {alert('status: '+status+" error: "+error)}
    }); 
    var data = <? echo CJavaScript::encode($report->addresses); ?>;
//    alert(data[0])
//    console.log(data);
    for(var i = 0;i < data.length; i++){
        $('#list').jqGrid('addRowData',i+1,data[i]);
    }    
});
</script>
<br>
<table id="co_list"></table> 

<script type="text/javascript">
$(function() {
    var grid=$("#co_list");
    grid.jqGrid( {
        datatype : 'local',
        width : '500',
        height : 'auto',
        mtype : 'GET',
        colNames : ['Тип контакта','Номер'],
        colModel : [
            {name:'name', width:100},
            {name:'value', width:100},
        ],
        caption : 'Контакты',
        rowNum : 300000,
        pgbuttons: false,     // disable page control like next, back button
        pgtext: null,  
    	loadError: function(xhr, status, error) {alert('status: '+status+" error: "+error)}
    }); 
    var data = <? echo CJavaScript::encode($report->contacts); ?>;
//    alert(data[0])
//    console.log(data);
    for(var i = 0;i < data.length; i++){
        $('#co_list').jqGrid('addRowData',i+1,data[i]);
    }    
    
});
</script>

<br>
<table id="id_list"></table> 

<script type="text/javascript">
$(function() {
    var grid=$("#id_list");
    grid.jqGrid( {
        datatype : 'local',
        width : '1000',
        height : 'auto',
        mtype : 'GET',
        colNames : ['Тип идентификатора','Номер документа','Дата выдачи','Кем выдан','Дата регистрации','Дата окончания действия'],
        colModel : [
            {name:'idDocName', width:100},
            {name:'docNumber', width:80},
            {name:'issuedDate', width:80},
            {name:'issuedBy', width:100},
            {name:'reg_date', width:80},
            {name:'stop_date', width:80}
        ],
        caption : 'Идентификационные документы',
        rowNum : 300000,
        pgbuttons: false,     // disable page control like next, back button
        pgtext: null,  
    	loadError: function(xhr, status, error) {alert('status: '+status+" error: "+error)}
    }); 
    var data = <? echo CJavaScript::encode($report->identifications); ?>;
//    alert(data[0])
//    console.log(data);
    for(var i = 0;i < data.length; i++){
        $('#id_list').jqGrid('addRowData',i+1,data[i]);
    }    
    
});
</script>
<br>
<table id="rel_list"></table> 

<script type="text/javascript">
$(function() {
    var grid=$("#rel_list");
    grid.jqGrid( {
        datatype : 'local',
        width : '1000',
        height : 'auto',
        mtype : 'GET',
        colNames : ['Состояние','Источник','Должность','Название','Номер','Дата начала', 'Адрес', 'Занятость'],
        colModel : [
            {name:'state', width:80},
            {name:'providerCode', width:80},
            {name:'jobTitle', width:80},
            {name:'companyName',index:'issue_by', width:100},
            {name:'reg_number', width:80},
            {name:'start_date', width:80},
            {name:'address', width:80},
            {name:'subjectPosition', width:80}
        ],
        caption : 'Тип деятельности клиента',
        rowNum : 300000,
        pgbuttons: false,     // disable page control like next, back button
        pgtext: null,  
    	loadError: function(xhr, status, error) {alert('status: '+status+" error: "+error)}
    }); //, cloneToTop:true});
    var data = <? echo CJavaScript::encode($report->relations); ?>;
//    alert(data[0])
//    console.log(data);
    for(var i = 0;i < data.length; i++){
        $('#rel_list').jqGrid('addRowData',i+1,data[i]);
    }    
    
});
</script>
<br>
<h3>Общая информация о контрактах</h3>
<?php 
if ($report->negativeInfoType == 'ALL')
echo "Количество пользователей МБКИ, которые сообщили о негативном статусе договоров за последние 12 месяцев: ".$report->numberOfUsersReportingNegativeStatus; ?>
<br>
<br>
<h3>Итоговая информация про все виды договоров:</h3>
<table class="table_edit">
    <tr>
    	<td>
            Количество действующих договоров:
        </td>
    	<td>
            <?php echo $report->numberOfExistingContracts; ?>
        </td>
    	<td>
            Общая непогашенная сумма:
        </td>
    	<td>
            <?php echo $report->totalOutstandingDebtValue.' '.$report->totalOutstandingDebtCurrency; ?>
        </td>
    <tr>
    	<td>
            Количество завершенных договоров:
        </td>
    	<td>
            <?php echo $report->numberOfTerminatedContracts; ?>
        </td>
    	<td>
            Общая сумма просроченных выплат:
        </td>
    	<td>
            <?php echo $report->value.' '.$report->currency; ?>
        </td>
    <tr>
    	<td>
            Количество необработанных заявлений:
        </td>
    	<td>
            <?php echo $report->numberOfUnsolvedApplications; ?>
        </td>
    	<td>
            Общее количество просроченных выплат:
        </td>
    	<td>
            <?php echo $report->numberOfUnpaidInstalments; ?>
        </td>
    <tr>
    	<td>
            Количество отказанных заявлений:
        </td>
    	<td>
            <?php echo $report->numberOfRejectedApplications; ?>
        </td>
    	<td>
            Количество аннулированных заявлений:
        </td>
    	<td>
            <?php echo $report->numberOfRevokedApplications; ?>
        </td>
</table>
<br>
<?
if (count($report->summaryInformations)>0){
?>
<table id='si_list'></table> 

<script type='text/javascript'>
$(function() {
    var grid=$('#si_list');
    grid.jqGrid( {
        datatype : 'local',
        width : '1000',
        height : 'auto',
        mtype : 'GET',
        colNames : ['Тип','Действующих договоров','Завершенных договоров','Необработанных заявлений','Отказанных заявлений','Аннулированных заявлений', 'Непогашенная сумма', 'Сумма просроченных выплат','Количество просроченных выплат'],
        colModel : [
            {name:'contractType', width:80},
            {name:'numberOfExistingContracts', width:80},
            {name:'numberOfTerminatedContracts', width:80},
            {name:'numberOfUnsolvedApplications', width:100},
            {name:'numberOfRejectedApplications', width:80},
            {name:'numberOfRevokedApplications', width:80},
            {name:'totalValue', width:80},
            {name:'value', width:80},
            {name:'numberOfUnpaidInstalments', width:80}
        ],
        caption : 'По типам договоров',
        rowNum : 300000,
        pgbuttons: false,     // disable page control like next, back button
        pgtext: null,  
    	loadError: function(xhr, status, error) {alert('status: '+status+" error: "+error)}
    }); //, cloneToTop:true});
    var data = <? echo CJavaScript::encode($report->summaryInformations); ?>;
//    alert(data[0])
//    console.log(data);
    for(var i = 0;i < data.length; i++){
        data[i]['totalValue']=data[i]['totalValue']+' '+data[i]['totalCurrency'];
        data[i]['value']=data[i]['value']+' '+data[i]['currency'];
        $('#si_list').jqGrid('addRowData',i+1,data[i]);
    }    
    
});
</script>
<?    
}    
?>
<br>

<?
$ct=$report->contracts[0]['contractType'];
switch ($ct) {
    case 'Existing':
        echo '<h3>Детальная информация о действующих контрактах</h3>';
        break;
    case 'Terminated':
        echo '<h3>Детальная информация о завершенных контрактах</h3>';
        break;
    case 'Rejected':
        echo '<h3>Детальная информация об отказанных контрактах</h3>';
        break;
}

foreach ($report->contracts as $c) {
    if ($c['contractType']!=$ct){
        $ct=$c['contractType'];
        if((string)$c['contractType']=='Terminated')
            echo '<h3>Детальная информация о завершенных контрактах</h3>';
        else 
            echo '<h3>Детальная информация об отказанных контрактах</h3>';
    }
    echo $c['exportCode'];
    echo '<br>Номер договора: '.$c['codeOfContract'];
    echo '<br>Комментарий субъекта: ';
?>
<br>
<br>
<h4>Основная информация:</h4>
<table>
    <tr>
    	<td>
            Дата заявки на кредит:
        </td>
    	<td>
            <?php echo $c['dateOfApplication']; ?>
        </td>
    	<td>
            Цель финансирования:
        </td>
    	<td>
            <?php echo $c['purposeOfCredit']; ?>
        </td>        
    </tr>
    <tr>
    	<td>
            
        </td>
    	<td>
            <?php echo ''; ?>
        </td>
    	<td>
            Фаза договора:
        </td>
    	<td>
            <?php echo $c['contractPhase']; ?>
        </td>        
    </tr>
    <tr>
    	<td>
            Дата заключения договора:
        </td>
    	<td>
            <?php echo $c['creditStartDate']; ?>
        </td>
    	<td>
            Негативный статус договора:
        </td>
    	<td>
            <?php echo $c['contractStatusValue']; ?>
        </td>        
    </tr>
    <tr>
    	<td>
            Дата начала договора:
        </td>
    	<td>
            <?php echo $c['creditStartDate']; ?>
        </td>
    	<td>
            Роль субъекта:
        </td>
    	<td>
            <?php echo $c['subjectRole']; ?>
        </td>        
    </tr>
    <tr>
    	<td>
            Плановая дата окончания договора:
        </td>
    	<td>
            <?php echo $c['contractEndDate']; ?>
        </td>
    	<td>
            Кредитор:
        </td>
    	<td>
            <?php echo $c['creditor']; ?>
        </td>        
    </tr>
        <tr>
    	<td>
            Порядок исполнения договора:
        </td>
    	<td>
            <?php echo ''; ?>
        </td>
    	<td>
            Дата последнего обновления:
        </td>
    	<td>
            <?php echo $c['accountingDate']; ?>
        </td>        
    </tr>
</table>
<h4>Субъекты договора:</h4>
<?
echo 'Номер документа: '.$c['identificationValue'];
echo '<br>Роль субъекта: '.$c['subjectRole'].'<br>';
?>
<br>
<h4>Обеспечение:</h4>
<h4>Детали:</h4>
<table>
    <tr>
    	<td>Общая сумма:</td>
    	<td><?php echo $c['totalAmountValue'].' '.$c['totalAmountCurrency']; ?></td>
    	<td>Процентная ставка:</td>
    	<td><?php echo $c['interesRate']; ?></td>        
    </tr>
    <tr>
    	<td>Сумма периодического платежа:</td>
    	<td>
            <?php echo $c['monthlyInstalmentAmountValue'].' '.$c['monthlyInstalmentAmountCurrency']; ?>
        </td>
    	<td>
            Периодичность платежей:
        </td>
    	<td>
            <?php echo $c['pereodicityOfPayments']; ?>
        </td>        
    </tr>
    <tr>
    	<td>
            Общее запланированное количество платежей:
        </td>
    	<td>
            <?php echo $c['numberOfInstalments']; ?>
        </td>
    	<td>
            Способ платежа:
        </td>
    	<td>
            <?php echo $c['methodOfPayments']; ?>
        </td>        
    </tr>
    <tr>
    	<td>
            Количество платежей, которые осталось оплатить:
        </td>
    	<td>
            <?php echo $c['numberOfOutstandingInstalments']; ?>
        </td>
    	<td>
            Дата завершения оплаты <br>процентов за пользование кредитом:
        </td>
    	<td>
            <?php echo $c['methodOfPayments']; ?>
        </td>        
    </tr>
    <tr>
    	<td>
            Сумма платежей, которые осталось оплатить:
        </td>
    	<td>
            <?php echo $c['outstandingAmountValue'].' '.$c['outstandingAmountCurrency']; ?>
        </td>
    	<td>
            Количество дней просрочки оплаты процентов:
        </td>
    	<td>
            <?php echo ''; ?>
        </td>        
    </tr>
    <tr>
    	<td>
            Количество неоплаченных платежей:
        </td>
    	<td>
            <?php echo $c['numberOfOverdueInstalments']; ?>
        </td>
    	<td>
            Количество неоплаченных платежей по процентам:
        </td>
    	<td>
            <?php echo $c['numberOfInstalmentsNotPaidAccordingToInterestRate']; ?>
        </td>        
    </tr>
    <tr>
    	<td>
            Неоплаченная просроченная сумма платежей:
        </td>
    	<td>
            <?php 
            if(+$c['overdueAmountValue']!=0)
                echo '<font color="#ff0000"><b>'.$c['overdueAmountValue'].' '.$c['overdueAmountCurrency'].'</b>';
            else
                echo $c['overdueAmountValue'].' '.$c['overdueAmountCurrency']; ?>
        </td>
    	<td>
            Неоплаченная просроченная сумма<br>
            проценнтов, в соответствии с<br>
            установленным графиком платежей:<br>
        </td>
    	<td>
            <?php 
            if(+$c['dueInterestAmountValue']!=0)
                echo '<font color="#ff0000"><b>'.$c['dueInterestAmountValue'].' '.$c['dueInterestAmountCurrency'].'</b>';
            else
                echo $c['dueInterestAmountValue'].' '.$c['dueInterestAmountCurrency']; ?>
        </td>        
    </tr>
</table>
<h4>Ежегодный исторический календарь платежей:</h4>
<table class ="my-table">
    <tr>
        <?  foreach ($c['months'] as $ms) 
                foreach ($ms as $m) 
            {?>
            <th><?echo $m;?></th>
        <?}?>
    </tr>
    <?if($c['hCResidualAmount']['description']>''){?>
    <tr>
        <?foreach ($c['hCResidualAmount'] as $m) 
            if(isset($m['value'])){?>
                <td><?echo $m['value'];?></td>
            <?} else {?>
                <td><?echo $m;?></td>
            <?}?>
    </tr>
    <?}?>
    <?if($c['hCTotalNumberOfOverdueInstalments']['description']>''){?>
    <tr>
        <td><?echo $c['hCTotalNumberOfOverdueInstalments']['description']; ?></td>
        <?foreach ($c['hCTotalNumberOfOverdueInstalments'] as $i=>$m) 
            if($i!='description')
                if(+$m['value']!=0)
                    echo '<td><font color="#ff0000"><b>'.$m['value'].'</b></td>';
                else
                    echo '<td>'.$m['value'].'</td>';
        ?>
    </tr>
    <?}?>
    <?if($c['hCCreditCardUsedInMonth']['description']>''){?>
    <tr>
        <td><?echo $c['hCCreditCardUsedInMonth']['description']; ?></td>
        <td><?echo $c['hCCreditCardUsedInMonth']['month1']['value']; ?></td>
        <td><?echo $c['hCCreditCardUsedInMonth']['month2']['value']; ?></td>
        <td><?echo $c['hCCreditCardUsedInMonth']['month3']['value']; ?></td>
        <td><?echo $c['hCCreditCardUsedInMonth']['month4']['value']; ?></td>
        <td><?echo $c['hCCreditCardUsedInMonth']['month5']['value']; ?></td>
        <td><?echo $c['hCCreditCardUsedInMonth']['month6']['value']; ?></td>
        <td><?echo $c['hCCreditCardUsedInMonth']['month7']['value']; ?></td>
        <td><?echo $c['hCCreditCardUsedInMonth']['month8']['value']; ?></td>
        <td><?echo $c['hCCreditCardUsedInMonth']['month9']['value']; ?></td>
        <td><?echo $c['hCCreditCardUsedInMonth']['month10']['value']; ?></td>
        <td><?echo $c['hCCreditCardUsedInMonth']['month11']['value']; ?></td>
        <td><?echo $c['hCCreditCardUsedInMonth']['month12']['value']; ?></td>
    </tr>
    <?}?>
    <tr>
        <td><?echo $c['hCTotalOverdueAmount']['description']; ?></td>
        <?
        if(isset($c['hCTotalOverdueAmount']))
        foreach ($c['hCTotalOverdueAmount'] as $i=>$m) 
            if($i!='description')
                if(+$m['value']!=0)
                    echo '<td><font color="#ff0000"><b>'.$m['value'].'</b></td>';
                else
                    echo '<td>'.$m['value'].'</td>';
        ?>
    </tr>
</table>
<?if(isset($c['months24'][0]))
    {
?>
<table class ="my-table">
    <tr>
        <?  foreach ($c['months24'] as $ms) 
            if(isset ($ms))
                foreach ($ms as $m) 
            {?>
            <th><?echo $m;?></th>
        <?}?>
    </tr>
    <?if($c['hCTotalNumberOfOverdueInstalments24']['description']>''){?>
    <tr>
        <td><?echo $c['hCTotalNumberOfOverdueInstalments24']['description']; ?></td>
        <?foreach ($c['hCTotalNumberOfOverdueInstalments24'] as $i=>$m) 
            if($i!='description')
                if(+$m['value']!=0)
                    echo '<td><font color="#ff0000"><b>'.$m['value'].'</b></td>';
                else
                    echo '<td>'.$m['value'].'</td>';
        ?>
    </tr>
<?}?>
    <tr >
        <td><?echo $c['hCResidualAmount24']['description']; ?></td>
        <?
        if(isset($c['hCResidualAmount24']))
        foreach ($c['hCResidualAmount24'] as $i=>$m) 
            if($i!='description')
                echo '<td>'.$m['value'].'</td>';
        ?>
    </tr>
    <tr >
        <td><?echo $c['hCCreditCardUsedInMonth24']['description']; ?></td>
        <?
        if(isset($c['hCCreditCardUsedInMonth24']))
        foreach ($c['hCCreditCardUsedInMonth24'] as $i=>$m) 
            if($i!='description')
                echo '<td>'.$m['value'].'</td>';
        ?>
    </tr>
    <tr >
        <td><?echo $c['hCTotalOverdueAmount24']['description']; ?></td>
        <?
        if(isset($c['hCTotalOverdueAmount24']))
        foreach ($c['hCTotalOverdueAmount24'] as $i=>$m) 
            if($i!='description')
                if(+$m['value']!=0)
                    echo '<td><font color="#ff0000"><b>'.$m['value'].'</b></td>';
                else
                    echo '<td>'.$m['value'].'</td>';
        ?>
    </tr>
    
</table>
<?}?>
<?    
    //}
}
?>

<br>
<h3>Запросы по субъекту за последние 12 месяцев</h3>
<table>
<tr>
    <th>Дата запроса</th>
    <th>Пользователь</th>
    <th>Тип пользователя</th>
<?
foreach ($report->inquiryList as $il) {
    echo '<tr><td>'.$il['date'].'</td>';
    echo '<td>'.$il['subscriber'].'</td>';
    echo '<td>'.$il['subscriberType'].'</td></tr>';
}
?>
</table>

<h4>ИТОГО: <? echo $report->summarySubscriberType.' '.$report->summarySubscriberCount;?></h4>
<h4>Количество запросов за последние 12 месяцев: <? echo $report->numberOfInquiers;?></h4>
<table style="width: 500px;">
<?
foreach ($report->inquiers as $i) {
    echo '<tr><td>'.$i['quarter'].'-й квартал '.$i['year'].'</td>';
    echo '<td>'.$i['value'].'</td></tr>';
}
?>
</table>
<h3>Конец кредитного отчета</h3>
