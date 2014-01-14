<?php
$cs = Yii::app()->clientScript;
 
$cs->registerCssFile(Yii::app()->request->baseUrl.'/jqgrid/themes/ui.jqgrid.css');
$cs->registerCssFile(Yii::app()->request->baseUrl.'/jqgrid/themes/redmond/jquery-ui-custom.css');
 
$cs->registerScriptFile(Yii::app()->request->baseUrl.'/jqgrid/js/jquery.js');
$cs->registerScriptFile(Yii::app()->request->baseUrl.'/jqgrid/js/i18n/grid.locale-ru.js');
$cs->registerScriptFile(Yii::app()->request->baseUrl.'/jqgrid/js/jquery.jqGrid.min.js');
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

<?php
echo '<h3>ID МБКИ: '.$report->mbkiId.'</h3>';
echo '<h3>Личная информация</h3>';
?>
<table class="table_edit">
    <tr>
    	<td>
            Фамилия:
        </td>
    	<td>
            <?php echo $report->surname; ?>
        </td>
    	<td>
            Классификация:
        </td>
    	<td>
            <?php echo $report->classification; ?>
        </td>
    <tr>
    	<td>
            Имя:
        </td>
    	<td>
            <?php echo $report->name; ?>
        </td>
    	<td>
            Дата рождения:
        </td>
    	<td>
            <?php echo $report->dateOfBirth; ?>
        </td>
    <tr>
    	<td>
            Отчество:
        </td>
    	<td>
            <?php echo $report->fathersName; ?>
        </td>
    	<td>
            Пол:
        </td>
    	<td>
            <?php echo $report->gender; ?>
        </td>
    <tr>
    	<td>
            Фамилия при рождении:
        </td>
    	<td>
            <?php echo $report->birthName; ?>
        </td>
    	<td>
            Резидент?:
        </td>
    	<td>
            <?php echo $report->residency; ?>
        </td>
    <tr>
    	<td>
            Идентификационный номер:
        </td>
    	<td>
            <?php echo $report->taxpayerNumber; ?>
        </td>
    	<td>
            Гражданство:
        </td>
    	<td>
            <?php echo $report->nationality; ?>
        </td>
    <tr>
    	<td>
            Гражданский паспорт:
        </td>
    	<td>
            <?php echo $report->passport; ?>
        </td>
    	<td>
            Образование:
        </td>
    	<td>
            <?php echo $report->education; ?>
        </td>
</table>
<table id="list"></table> 
<div id="pager"></div> 

<script type="text/javascript">
//function myFmatter (cellvalue, options, rowObject)
//{
////    alert(JSON.stringify(rowObject))
//    return cellvalue;
//}    
$(function() {
//    var lastSel = 0;
    var grid=$("#list");
//    var pager_selector = "#pager";
    grid.jqGrid( {
        url: "<?php  echo Yii::app()->createUrl('reportMBKI/getAddresses',
                array('addresses'=>$report->addresses))?>",
        datatype : 'json',
        width : '1000',
        height : 'auto',
        mtype : 'GET',
        groupingView: {
            groupField: ['block_name'],
            groupColumnShow: [false],
            groupText: ['<b> Блок: {0}</b>'],
            groupSummary: [true]
        },
        colNames : ['ID','Тип адреса','Улица','Город','Индекс','Район','Область','Страна'],
        colModel : [
            {name:'id',index:'id', width:20, hidden:true},
            {name:'block_id',index:'id', width:80},
            {name:'factor_id',index:'factor_id', width:200},
            {name:'block_name',index:'block_name', width:100},
            {name:'block_weight', width:40},
            {name:'factor_name', width:100},
            {name:'factor_weight', width:100},
            {name:'rate', width:100}
        ],
        caption : 'Адреса',
        rowNum : 300000,
        pgbuttons: false,     // disable page control like next, back button
        pgtext: null,  
        pager: '#pager',
        gridComplete: function () {
            if(grid.getGridParam("reccount")===0){
                alert("Эта матрица еще не рассчитана");
                window.location.href = "<?echo Yii::app()->createUrl('/performance/getViewParams');?>";
            }
        },
    	loadError: function(xhr, status, error) {alert('status: '+status+" error: "+error)}
    }).navGrid('#pager',{search:false, view:false, del:false, add:false, edit:false, refresh:false},
    {}, // default settings for edit
    {}, // default settings for add
    {}, // delete
    {}, // search options
    {}
    ); //, cloneToTop:true});
    
});
</script>
<br>
<table id="id_list"></table> 

<script type="text/javascript">
$(function() {
    var grid=$("#id_list");
    grid.jqGrid( {
        url: "<?php  echo Yii::app()->createUrl('reportMBKI/getIdDocs',
                array('idDocs'=>$report->identifications))?>",
        datatype : 'json',
        width : '1000',
        height : 'auto',
        mtype : 'GET',
//        groupingView: {
//            groupField: ['block_name'],
//            groupColumnShow: [false],
//            groupText: ['<b> Блок: {0}</b>'],
//            groupSummary: [true]
//        },
        colNames : ['Тип идентификатора','Номер документа','Дата выдачи','Кем выдан','Дата регистрации','Дата окончания действия'],
        colModel : [
            {name:'type',index:'type', width:100},
            {name:'number',index:'number', width:80},
            {name:'date_issue',index:'date_issue', width:80},
            {name:'issue_by',index:'issue_by', width:100},
            {name:'reg_date', width:80},
            {name:'stop_date', width:80}
        ],
        caption : 'Идентификационные документы',
        rowNum : 300000,
        pgbuttons: false,     // disable page control like next, back button
        pgtext: null,  
//        pager: '#pager',
        gridComplete: function () {
            if(grid.getGridParam("reccount")===0){
                alert("Эта матрица еще не рассчитана");
                window.location.href = "<?echo Yii::app()->createUrl('/performance/getViewParams');?>";
            }
        },
    	loadError: function(xhr, status, error) {alert('status: '+status+" error: "+error)}
    }).navGrid('#pager',{search:false, view:false, del:false, add:false, edit:false, refresh:false},
    {}, // default settings for edit
    {}, // default settings for add
    {}, // delete
    {}, // search options
    {}
    ); //, cloneToTop:true});
    
});
</script>
<br>
<table id="rel_list"></table> 

<script type="text/javascript">
$(function() {
    var grid=$("#rel_list");
    grid.jqGrid( {
        url: "<?php  echo Yii::app()->createUrl('reportMBKI/getRelations',
                array('relations'=>$report->relations))?>",
        datatype : 'json',
        width : '1000',
        height : 'auto',
        mtype : 'GET',
        colNames : ['Состояние','Источник','Должность','Название','Номер','Дата начала', 'Адрес', 'Занятость'],
        colModel : [
            {name:'state',index:'type', width:80},
            {name:'provider',index:'number', width:80},
            {name:'jobTitle',index:'date_issue', width:80},
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
//        gridComplete: function () {
//            if(grid.getGridParam("reccount")===0){
//                alert("Эта матрица еще не рассчитана");
//                window.location.href = "<?echo Yii::app()->createUrl('/performance/getViewParams');?>";
//            }
//        },
    	loadError: function(xhr, status, error) {alert('status: '+status+" error: "+error)}
    }).navGrid('#pager',{search:false, view:false, del:false, add:false, edit:false, refresh:false},
    {}, // default settings for edit
    {}, // default settings for add
    {}, // delete
    {}, // search options
    {}
    ); //, cloneToTop:true});
    
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
            <?php echo $report->totalOutstandingDebt; ?>
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
        url: "<?php  echo Yii::app()->createUrl('reportMBKI/getSummaryInformations',
                array('summaryInformations'=>$report->summaryInformations))?>",
        datatype : 'json',
        width : '1000',
        height : 'auto',
        mtype : 'GET',
        colNames : ['Тип','Действующих договоров','Завершенных договоров','Необработанных заявлений','Отказанных заявлений','Аннулированных заявлений', 'Непогашенная сумма', 'Сумма просроченных выплат','Количество просроченных выплат'],
        colModel : [
            {name:'type',index:'type', width:80},
            {name:'f1', width:80},
            {name:'f2', width:80},
            {name:'f3', width:100},
            {name:'f4', width:80},
            {name:'f5', width:80},
            {name:'f6', width:80},
            {name:'f7', width:80},
            {name:'f8', width:80}
        ],
        caption : 'По типам договоров',
        rowNum : 300000,
        pgbuttons: false,     // disable page control like next, back button
        pgtext: null,  
//        gridComplete: function () {
//            if(grid.getGridParam("reccount")===0){
//                alert("Эта матрица еще не рассчитана");
//                window.location.href = "<?echo Yii::app()->createUrl('/performance/getViewParams');?>";
//            }
//        },
    	loadError: function(xhr, status, error) {alert('status: '+status+" error: "+error)}
    }).navGrid('#pager',{search:false, view:false, del:false, add:false, edit:false, refresh:false},
    {}, // default settings for edit
    {}, // default settings for add
    {}, // delete
    {}, // search options
    {}
    ); //, cloneToTop:true});
    
});
</script>
<?    
}    
?>
<br>
<h3>Детальная информация о действующих контрактах</h3>
<?
foreach ($report->contracts as $c) {
    if ($c['contractType']=='Existing'){
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
            <?php echo $c['overdueAmountValue'].' '.$c['overdueAmountCurrency']; ?>
        </td>
    	<td>
            Неоплаченная просроченная сумма<br>
            проценнтов, в соответствии с<br>
            установленным графиком платежей:<br>
        </td>
    	<td>
            <?php echo $c['dueInterestAmountValue'].' '.$c['dueInterestAmountCurrency']; ?>
        </td>        
    </tr>
</table>
<h4>Ежегодный исторический календарь платежей:</h4>
<table class ="my-table">
    <tr>
    	<th>
            месяц/год:
        </th>
        <th><?echo $c['months']['month1'];?></th>
        <th><?echo $c['months']['month2'];?></th>
        <th><?echo $c['months']['month3'];?></th>
        <th><?echo $c['months']['month4'];?></th>
        <th><?echo $c['months']['month5'];?></th>
        <th><?echo $c['months']['month6'];?></th>
        <th><?echo $c['months']['month7'];?></th>
        <th><?echo $c['months']['month8'];?></th>
        <th><?echo $c['months']['month9'];?></th>
        <th><?echo $c['months']['month10'];?></th>
        <th><?echo $c['months']['month11'];?></th>
        <th><?echo $c['months']['month12'];?></th>
    </tr>
    <tr>
        <td><?echo $c['hCTotalNumberOfOverdueInstalments']['description']; ?></td>
        <td><?echo $c['hCTotalNumberOfOverdueInstalments']['month1'][0]['value']; ?></td>
        <td><?echo $c['hCTotalNumberOfOverdueInstalments']['month2'][0]['value']; ?></td>
        <td><?echo $c['hCTotalNumberOfOverdueInstalments']['month3'][0]['value']; ?></td>
        <td><?echo $c['hCTotalNumberOfOverdueInstalments']['month4'][0]['value']; ?></td>
        <td><?echo $c['hCTotalNumberOfOverdueInstalments']['month5'][0]['value']; ?></td>
        <td><?echo $c['hCTotalNumberOfOverdueInstalments']['month6'][0]['value']; ?></td>
        <td><?echo $c['hCTotalNumberOfOverdueInstalments']['month7'][0]['value']; ?></td>
        <td><?echo $c['hCTotalNumberOfOverdueInstalments']['month8'][0]['value']; ?></td>
        <td><?echo $c['hCTotalNumberOfOverdueInstalments']['month9'][0]['value']; ?></td>
        <td><?echo $c['hCTotalNumberOfOverdueInstalments']['month10'][0]['value']; ?></td>
        <td><?echo $c['hCTotalNumberOfOverdueInstalments']['month11'][0]['value']; ?></td>
        <td><?echo $c['hCTotalNumberOfOverdueInstalments']['month12'][0]['value']; ?></td>
    </tr>
    <tr >
        <td><?echo $c['hCTotalOverdueAmount']['description']; ?></td>
        <td><?echo $c['hCTotalOverdueAmount']['month1'][0]['value']; ?></td>
        <td><?echo $c['hCTotalOverdueAmount']['month2'][0]['value']; ?></td>
        <td><?echo $c['hCTotalOverdueAmount']['month3'][0]['value']; ?></td>
        <td><?echo $c['hCTotalOverdueAmount']['month4'][0]['value']; ?></td>
        <td><?echo $c['hCTotalOverdueAmount']['month5'][0]['value']; ?></td>
        <td><?echo $c['hCTotalOverdueAmount']['month6'][0]['value']; ?></td>
        <td><?echo $c['hCTotalOverdueAmount']['month7'][0]['value']; ?></td>
        <td><?echo $c['hCTotalOverdueAmount']['month8'][0]['value']; ?></td>
        <td><?echo $c['hCTotalOverdueAmount']['month9'][0]['value']; ?></td>
        <td><?echo $c['hCTotalOverdueAmount']['month10'][0]['value']; ?></td>
        <td><?echo $c['hCTotalOverdueAmount']['month11'][0]['value']; ?></td>
        <td><?echo $c['hCTotalOverdueAmount']['month12'][0]['value']; ?></td>
    </tr>
</table>
<table class ="my-table">
    <tr>
    	<th>
            месяц/год:
        </th>
        <th><?echo $c['months24']['month1'];?></th>
        <th><?echo $c['months24']['month2'];?></th>
        <th><?echo $c['months24']['month3'];?></th>
        <th><?echo $c['months24']['month4'];?></th>
        <th><?echo $c['months24']['month5'];?></th>
        <th><?echo $c['months24']['month6'];?></th>
        <th><?echo $c['months24']['month7'];?></th>
        <th><?echo $c['months24']['month8'];?></th>
        <th><?echo $c['months24']['month9'];?></th>
        <th><?echo $c['months24']['month10'];?></th>
        <th><?echo $c['months24']['month11'];?></th>
        <th><?echo $c['months24']['month12'];?></th>
    </tr>
    <tr>
        <td><?echo $c['hCTotalNumberOfOverdueInstalments24']['description']; ?></td>
        <td><?echo $c['hCTotalNumberOfOverdueInstalments24']['month1'][0]['value']; ?></td>
        <td><?echo $c['hCTotalNumberOfOverdueInstalments24']['month2'][0]['value']; ?></td>
        <td><?echo $c['hCTotalNumberOfOverdueInstalments24']['month3'][0]['value']; ?></td>
        <td><?echo $c['hCTotalNumberOfOverdueInstalments24']['month4'][0]['value']; ?></td>
        <td><?echo $c['hCTotalNumberOfOverdueInstalments24']['month5'][0]['value']; ?></td>
        <td><?echo $c['hCTotalNumberOfOverdueInstalments24']['month6'][0]['value']; ?></td>
        <td><?echo $c['hCTotalNumberOfOverdueInstalments24']['month7'][0]['value']; ?></td>
        <td><?echo $c['hCTotalNumberOfOverdueInstalments24']['month8'][0]['value']; ?></td>
        <td><?echo $c['hCTotalNumberOfOverdueInstalments24']['month9'][0]['value']; ?></td>
        <td><?echo $c['hCTotalNumberOfOverdueInstalments24']['month10'][0]['value']; ?></td>
        <td><?echo $c['hCTotalNumberOfOverdueInstalments24']['month11'][0]['value']; ?></td>
        <td><?echo $c['hCTotalNumberOfOverdueInstalments24']['month12'][0]['value']; ?></td>
    </tr>
    <tr >
        <td><?echo $c['hCTotalOverdueAmount24']['description']; ?></td>
        <td><?echo $c['hCTotalOverdueAmount24']['month1'][0]['value']; ?></td>
        <td><?echo $c['hCTotalOverdueAmount24']['month2'][0]['value']; ?></td>
        <td><?echo $c['hCTotalOverdueAmount24']['month3'][0]['value']; ?></td>
        <td><?echo $c['hCTotalOverdueAmount24']['month4'][0]['value']; ?></td>
        <td><?echo $c['hCTotalOverdueAmount24']['month5'][0]['value']; ?></td>
        <td><?echo $c['hCTotalOverdueAmount24']['month6'][0]['value']; ?></td>
        <td><?echo $c['hCTotalOverdueAmount24']['month7'][0]['value']; ?></td>
        <td><?echo $c['hCTotalOverdueAmount24']['month8'][0]['value']; ?></td>
        <td><?echo $c['hCTotalOverdueAmount24']['month9'][0]['value']; ?></td>
        <td><?echo $c['hCTotalOverdueAmount24']['month10'][0]['value']; ?></td>
        <td><?echo $c['hCTotalOverdueAmount24']['month11'][0]['value']; ?></td>
        <td><?echo $c['hCTotalOverdueAmount24']['month12'][0]['value']; ?></td>
    </tr>
</table>
<?    
    }
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
