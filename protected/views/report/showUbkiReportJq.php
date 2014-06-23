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

<h2>Украинское Бюро Кредитных Историй</h2>
<h3>Отчет сохранен в базе <?echo $date; ?></h3>
<? 
if(!Yii::app()->user->isGuest){
    echo '<h3><a href='.Yii::app()->createUrl("report/showAnalyzeResult").'?inn='.$inn.'&type=1>Анализ (Кредит без залога) </a><br>'; 
    echo '<a href='.Yii::app()->createUrl("report/showAnalyzeResult").'?inn='.$inn.'&type=2>Анализ (Кредит залоговый) </a></h3><br>'; 
}
?> 

<h3>1.Идентификация Субъекта Кредитной Истории (СКИ)</h3>
<? echo isset($report->photo) ? ('<img src="'.$report->photo.'"></img>'): ( ''); ?>
<table class="table_edit">
    <tr>
    	<td>Ф.И.О. субъекта (рус):</td>
    	<td><?php echo $report->ruLName.' '.$report->ruFName.' '.$report->ruMName; ?></td>
    	<td>Ф.И.О. субъекта (укр):</td>
    	<td><?php echo $report->uaLName.' '.$report->uaFName.' '.$report->uaMName; ?></td>
    <tr>
    	<td>ИНН субьекта:</td>
    	<td><?php echo $report->okpo; ?></td>
    	<td>Семейное положение:</td>
    	<td><?php echo $report->familySt=='Y'? 'женат/замужем':''; ?></td>
    <tr>
    	<td>Дата рождения:</td>
    	<td><?php echo $report->db; ?></td>
    	<td>Серия и номер паспорта:</td>
    	<td><?php echo $report->dser.' '.$report->dnum; ?></td>
    <tr>
    	<td>Пол субьекта:</td>
    	<td><?php echo $report->sex=='M'? 'мужской':'женский'; ?></td>
    	<td>Дата выдачи паспорта:</td>
    	<td><?php echo $report->dds; ?></td>
    <tr>
    	<td>Дата обновления данных:</td>
    	<td><?php echo $report->clDate; ?></td>
    	<td>ЕГРПОУ места работы:</td>
    	<td><?php echo $report->wokpo; ?></td>
    <tr>
    	<td>Место работы:</td>
    	<td><?php echo $report->clWName; ?></td>
    	<td></td>
    	<td></td>
    <tr>
    	<td>Адрес регистрации:</td>
    	<td><?php echo $report->address1; ?></td>
    	<td></td>
    	<td></td>
    <tr>
    	<td>Адрес почтовый:</td>
    	<td><?php echo $report->address2; ?></td>
    	<td></td>
    	<td></td>
    <tr>
    	<td>Адрес проживания:</td>
    	<td><?php echo $report->address3; ?></td>
    	<td></td>
    	<td></td>
</table>
<!--<h4>1.1. История идентификации СКИ</h4>-->
<table id="list"></table> 
<div id="pager"></div> 
<? //echo var_dump($report->auth_hist[0]['clDate']); ?>
<script type="text/javascript">
$(function() {
    var grid=$("#list");
    grid.jqGrid( {
//        url: "<?php echo Yii::app()->createUrl('reportMBKI/getAuthHistory'); ?>",
//                array('auth_hist'=>$report->auth_hist))?>//",
        datatype : 'local',
//        data: JSON::encode(  <?echo $report->auth_hist; ?>),
        width : '1000',
        shrinktofit:false,
//        autowidth:true,
        height : 'auto',
        mtype : 'GET',
        colNames : ['Дата','Ф.И.О. субьекта','Семейное положение','Дата рождения','Пол субьекта','ОКПО места работы'],
        colModel : [
            {name:'clDate', width:40},
            {name:'full_name', width:150},
            {name:'family', width:60},
            {name:'db',width:40},
            {name:'sex', width:40},
            {name:'WOKPO', width:60},
        ],
        caption : '1.1. История идентификации СКИ',
        rowNum : 300000,
        pgbuttons: false,     // disable page control like next, back button
        pgtext: null,  
//        pager: '#pager',
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
    var data = <? echo CJavaScript::encode($report->auth_hist); ?>;
//    alert(data[0])
//    console.log(data[0]);
    for(var i = 0;i < data.length; i++){
        if (data[i]['sex']=='M')
            data[i]['sex']='мужской';
        data[i]['sex']=(data[i]['sex']=='M'? 'мужской':(data[i]['sex']=='F'? 'женский':''));
        data[i]['full_name']=data[i]['ruLName']+' '+data[i]['ruFName']+' '+data[i]['ruMName']+' (рус)<br>'+
                data[i]['uaLName']+' '+data[i]['uaFName']+' '+data[i]['uaMName']+' (укр)<br>'+data[i]['fioEn']+' (eng)';
        data[i]['family']=data[i]['familySt']=='Y'? 'женат/замужем': data[i]['familySt']=='N'? 'не женат/не замужем':'';
        $('#list').jqGrid('addRowData',i+1,data[i]);
    }

});
</script>
<br>
<table id="doc_list"></table> 
<div id="doc_pager"></div>
<? //echo var_dump($report->auth_hist[0]['clDate']); ?>
<script type="text/javascript">
$(function() {
    var grid=$("#doc_list");
    grid.jqGrid( {
        datatype : 'local',
        width : '1000',
        shrinktofit:false,
        height : 'auto',
        mtype : 'GET',
        colNames : ['Дата','Серия и номер паспорта','Кем выдан','Дата выдачи'],
        colModel : [
            {name:'dtm', width:40},
            {name:'full_doc', width:50},
            {name:'dwho', width:150},
            {name:'dds',width:40},
        ],
        caption : '1.2. История документов СКИ',
        rowNum : 300000,
        pgbuttons: false,     // disable page control like next, back button
        pgtext: null,  
    	loadError: function(xhr, status, error) {alert('status: '+status+" error: "+error)}
    }).navGrid('#doc_pager',{search:false, view:false, del:false, add:false, edit:false, refresh:false},
    {}, // default settings for edit
    {}, // default settings for add
    {}, // delete
    {}, // search options
    {}
    ); //, cloneToTop:true});
    var d_data = <? echo CJavaScript::encode($report->doc_hist); ?>;
//    alert(data[0])
//    console.log(d_data);
    for(var i = 0;i < d_data.length; i++){
        d_data[i]['full_doc']=d_data[i]['dser']+' '+d_data[i]['dnum'];
        $('#doc_list').jqGrid('addRowData',i+1,d_data[i]);
    }

});
</script>
<br>
<table id="contact_list"></table> 
<div id="contact_pager"></div>
<? //echo var_dump($report->auth_hist[0]['clDate']); ?>
<script type="text/javascript">
$(function() {
    var grid=$("#contact_list");
    grid.jqGrid( {
        datatype : 'local',
        width : '1000',
        shrinktofit:false,
        height : 'auto',
        mtype : 'GET',
        colNames : ['Тип адреса','История изм.'],
        colModel : [
            {name:'type', width:100},
            {name:'date', width:600},
        ],
        caption : '1.3. История контактов СКИ',
        rowNum : 300000,
        pgbuttons: false,     // disable page control like next, back button
        pgtext: null,  
    	loadError: function(xhr, status, error) {alert('status: '+status+" error: "+error)}
    }).navGrid('#contact_pager',{search:false, view:false, del:false, add:false, edit:false, refresh:false},
    {}, // default settings for edit
    {}, // default settings for add
    {}, // delete
    {}, // search options
    {}
    ); //, cloneToTop:true});
    var c_data = <? echo CJavaScript::encode($report->contact_hist); ?>;
//    alert(data[0])
//    console.log(c_data);
    for(var i = 0;i < c_data.length; i++){
        switch(c_data[i]['type']) {
            case '1':
                c_data[i]['type'] = 'Адрес регистрации';
                break;
            case '2':
                c_data[i]['type'] = 'Адрес почтовый';
                break;
            case '3':
                c_data[i]['type'] = 'Адрес проживания';
                break;
        }
        c_data[i]['date'] = c_data[i]['date']+' '+c_data[i]['address'];
        $('#contact_list').jqGrid('addRowData',i+1,c_data[i]);
    }

});
</script>
<br>
<table id="contact2_list"></table> 
<div id="contact2_pager"></div>
<script type="text/javascript">
$(function() {
    var grid=$("#contact2_list");
    grid.jqGrid( {
        datatype : 'local',
        width : '1000',
        shrinktofit:false,
        height : 'auto',
        mtype : 'GET',
        colNames : ['Тип контакта', 'Дата информации','Контакт'],
        colModel : [
            {name:'type', width:100},
            {name:'versionDate', width:100},
            {name:'number', width:100}
        ],
        caption : '2. Контакты (new)',
        rowNum : 300000,
        pgbuttons: false,     // disable page control like next, back button
        pgtext: null,  
    	loadError: function(xhr, status, error) {alert('status: '+status+" error: "+error)}
    }).navGrid('#contact_pager',{search:false, view:false, del:false, add:false, edit:false, refresh:false},
    {}, // default settings for edit
    {}, // default settings for add
    {}, // delete
    {}, // search options
    {}
    ); //, cloneToTop:true});
    var c2_data = <? echo CJavaScript::encode($report->contacts); ?>;
//    alert(data[0])
//    console.log(c_data);
    for(var i = 0;i < c2_data.length; i++){
        switch(c2_data[i]['type']) {
            case '1':
                c2_data[i]['type'] = 'Домашний тел.';
                break;
            case '2':
                c2_data[i]['type'] = 'Рабочий тел.';
                break;
            case '3':
                c2_data[i]['type'] = 'Мобильный тел.';
                break;
        }
        $('#contact2_list').jqGrid('addRowData',i+1,c2_data[i]);
    }

});
</script>
<br>
<h3>3. Финансовые обязательства СКИ</h3>
<h3>Кредиты</h3>
<?
//if(count($report->credits>0))
foreach ($report->credits as $key => $v) {
?>    
<table class="cel" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tbody>
        <tr height="20">
            <td style="font-weight:bold; font-family:Tahoma,sans-serif;" colspan="2" bgcolor="#616161">
                &nbsp;<font color="#FFFFFF" size="2">
                <b><? echo ($key+1)." - ".$v['reference']; ?></b>
                </font>
            </td>
        </tr>
    </tbody>
</table>

<h4>Информация по договору</h4>
<table class="table_edit">
    <tr>
    	<td>Дата выдачи кредита:</td>
    	<td><?php echo $v['startDate']; ?></td>
    </tr>
    <tr>
    	<td>Тип кредита:</td>
    	<td><?php echo $v['creditTypeName']; ?></td>
    </tr>
    <tr>
    	<td>Сумма кредита (лимит):</td>
    	<td><?php echo $v['amount']; ?></td>
    </tr>
    <tr>
    	<td>Текущая задолженность:</td>
    	<td><?php echo $v['amtCurr']; ?></td>
    </tr>
    <tr>
    	<td>Текущая просроченная задолженность:</td>
    	<td><?php echo $v['amtExp']; ?></td>
    </tr>
    <tr>
    	<td>Валюта кредита:</td>
    	<td><?php echo $v['currencyCode']; ?></td>
    </tr>
    <tr>
    	<td>Источник:</td>
    	<td><?php echo $v['donor']; ?></td>
    </tr>
    <tr>
    	<td>Срок действия кредита до:</td>
    	<td><?php echo $v['stopDate']; ?></td>
    </tr>
    <tr>
    	<td>Вид обеспечения:</td>
    	<td></td>
    </tr>
    <tr>
    	<td>Стоимость обеспечения в базовой валюте:</td>
    	<td></td>
    </tr>
    <tr>
    	<td>Роль субъекта:</td>
    	<td>Заемщик</td>
    </tr>
    <tr>
    	<td>Кредит закрыт?</td>
    	<td><?php echo ($v['flClose']=='Y'? 'закрыт':'открыт'); ?></td>
    </tr>
    <tr>
    	<td>Дата обновления:</td>
    	<td><?php echo $v['dateCalc']; ?></td>
    </tr>
</table>
<br>
<table <?echo "id=payment".$key."_list"; ?>></table> 
<div id="payment_pager"></div>
<script type="text/javascript">
$(function() {
    var grid=$("#payment"+<?echo $key;?>+"_list");
    grid.jqGrid( {
        datatype : 'local',
        width : '1000',
        shrinktofit:false,
        height : 'auto',
        mtype : 'GET',
        sortname : 'year',
        sortorder : 'desc',
//        loadonce: true,
        colNames : ['Период','Исполнение платежа','Сумма задолж.','Сумма обяз. платежа','Срок просрочки','Сумма просрочки','Кредитный транш'],
        colModel : [
            {name:'year', width:60, index:'year', sorttype: 'text', sortable: true},
            {name:'flPay', width:100},
            {name:'amtCurr', width:100},
            {name:'crSetAmount', width:100},
            {name:'daysExp', width:100},
            {name:'amtExp', width:100},
            {name:'flUse', width:100}
        ],
        caption : 'Платежи',
        rowNum : 300000,
        pgbuttons: false,     // disable page control like next, back button
        pgtext: null,  
    	loadError: function(xhr, status, error) {alert('status: '+status+" error: "+error)}
    }).navGrid('#payment_pager',{search:false, view:false, del:false, add:false, edit:false, refresh:false},
    {}, // default settings for edit
    {}, // default settings for add
    {}, // delete
    {}, // search options
    {}
    ); //, cloneToTop:true});
    var data = <? echo CJavaScript::encode($v['payments']); ?>;
//    alert(data[0])
//    console.log(data);
    for(var i = 0;i < data.length; i++){
        data[i]['year']=data[i]['year']+' '+data[i]['month'];
        data[i]['flPay']=(data[i]['flPay']=='Y'?'Да':'Нет');
        data[i]['flUse']=(data[i]['flUse']=='Y'?'Да':'Нет');
        if(data[i]['daysExp'] == '0') 
            data[i]['daysExp']='Нет';
        else if((0<+data[i]['daysExp']) && (+data[i]['daysExp']<8)) 
                data[i]['daysExp'] = 'менее 7-ми дней';
        else if((7<+data[i]['daysExp']) && (+data[i]['daysExp']<30))
            data[i]['daysExp'] = 'от 7 до 29 дней';
        else if((29<+data[i]['daysExp']) && (+data[i]['daysExp']<60))
            data[i]['daysExp'] = 'от 30 до 59 дней';
        else if(59<+data[i]['daysExp']<90)
            data[i]['daysExp'] = 'от 60 до 89 дней';
        else
            data[i]['daysExp'] = 'свыше 90 дней';
        $('#payment'+<?echo $key;?>+'_list').jqGrid('addRowData',i+1,data[i]);
    }
    grid.jqGrid("sortGrid", "year", true); 
});
</script>

<br>
<table class="table_edit">
    <tr>
    	<td>Просрочек < 7 дней: <?echo $v['nBreak1']?></td>
        <td>Просрочек от 7 до 29 дней: <?echo $v['nBreak2']?></td>	
        <td>Просрочек от 30 до 59 дней: <?echo $v['nBreak3']?></td>	
        <td>Просрочек от 60 до 89 дней: <?echo $v['nBreak4']?></td>
        <td>Просрочек свыше 90 дней: <?echo $v['nBreak5']?></td>
    </tr>
</table>
<br>

<?    
}
?>
<table class="cel" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tbody>
        <tr height="20">
            <td style="font-weight:bold; font-family:Tahoma,sans-serif;" colspan="2" bgcolor="#616161">
                &nbsp;<font color="#FFFFFF" size="2">
                <b>ИТОГО:</b>
                </font>
            </td>
        </tr>
    </tbody>
</table>
<table class="table_edit">
    <tr>
    	<td>Общая сумма взятых обязательств:</td><td> </td>
    </tr>
    <tr>
        <td>Общая сумма задолженности:</td><td> </td>	
    </tr>
    <tr>
        <td>Общая сумма просроченной задолженности:</td>	
    </tr>
    <tr>
        <td>Общая сумма обяз. платежа:</td>
    </tr>
</table>
<h3>4. Кредитный рейтинг УБКИ</h3>

<table style="border:1px solid #DAD8D7;" cellpadding="0" cellspacing="0" height="187" width="640">
    <tbody><tr><td width="30%">    
    <table style="border-right:1px solid #DAD8D7;" bgcolor="#EFEFEF" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">    
        <tbody><tr height="30%">	     
                <td align="center"><font style="font-size:12px;FONT-FAMILY:Arial Black;"><?echo $report->rating['scorerlname'].' '.$report->rating['scorerfname'].' '.$report->rating['scorermname'].' ('.$report->rating['scoreinn'].')'?></font>
                    <br><font style="font-size:16px;FONT-FAMILY:Arial Black;">Кредитный&nbsp;рейтинг&nbsp;УБКИ:</font></td>
            </tr>    
            <tr height="30%">       
                <td align="center"><font style="font-size:60px;FONT-FAMILY:Arial,sans-serif;" ><?echo $report->rating['score']?></font></td>	  
            </tr>     
            <tr height="30%">	     
                <td colspan="3" align="center"><font style="font-size:10px;FONT-FAMILY:Arial;"><b>Дата расчета :</b>&nbsp;<?echo $report->rating['scoredate']?></font></td>	  
            </tr>	  
        </tbody>
    </table> 
            </td> 
        <td width="30%"> 	  
            <table bgcolor="white" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">     
                <tbody><tr>	     
                        <td align="center"><font style="font-size:15px;FONT-FAMILY:Arial Black,sans-serif;color:#72B773;">
                            <?if((int)$report->rating['score']<251){?>
                                <font style="font-size:18px;" color="#FF0000">очень низкий</font>
                            <?}elseif (((int)$report->rating['score']>250) and ((int)$report->rating['score']<351)) { ?>
                                <font style="font-size:18px;" color="#FC9900">низкий</font>
                            <?}elseif (((int)$report->rating['score']>350) and ((int)$report->rating['score']<451)) { ?>
                                <font style="font-size:18px;" color="#CDCD00">средний</font>
                            <?}elseif (((int)$report->rating['score']>450) and ((int)$report->rating['score']<551)) { ?>
                                <font style="font-size:18px;" color="#99CC00">выше среднего</font>
                            <?}else{?>
                                <font style="font-size:18px;" color="#339966">высокий</font>
                            <?}?>
                    </font></td></tr>     
                    <tr>       
                        <td align="center"><img src="<?php echo Yii::app()->request->baseUrl.'/images/CreditBa.png';?>" alt="" border="0" height="126" width="248"></td></tr>
                    <tr>	     
                        <td>&nbsp;</td>	  </tr>	  
                </tbody>
            </table> </td>
            <td width="30%"> 	  
                <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">    
                    <tbody><tr>	     <td class="lsmal" align="center">Пояснения: </td>	  </tr>    
                        <tr>	     
                            <td class="lsmal" align="left"><font color="FF0000">0...250</font> - наличие просрочки свыше 90 дней;<br>		                                                        - большое количество открытых необеспеченных кредитов; </td>	  </tr>	  <tr>	     
                            <td class="lsmal" align="left"><font color="FC9900">251 ... 350</font> - периодические просрочки более 30 дней;                                                                  - большое количество кредитов;</td>	  </tr>	  <tr>	     
                            <td class="lsmal" align="left"><font color="CDCD00">351 ... 450</font> - средняя дисциплина платежей;                                                                  - возможны просрочки до 30 дней;</td>	  </tr>	  <tr>	     
                            <td class="lsmal" align="left"><font color="99CC00">451 ... 550</font> - своевременные оплаты по кредиту;                                                                  - наличие закрытых кредитов;</td>	  </tr>	  <tr>	     
                            <td class="lsmal" align="left"><font color="339966">551 ... 700</font>  - отличная кредитная история  </td>	  </tr>	  
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>	  
<h3>8. Реестр запросов</h3>
<table>
    <tr>
        <th>Запросов за 1 час</th>
        <th>Запросов за 1 день</th>
        <th>Запросов за 1 неделю</th> 	
        <th>Запросов за 1 месяц</th> 	
        <th>Запросов за 1 квартал</th> 	
        <th>Запросов за 1 год</th>
        <th>Запросов свыше года</th>
    </tr>
    <tr>
        <td><?echo $report->query_register['hr']?></td>
        <td><?echo $report->query_register['da']?></td>
        <td><?echo $report->query_register['wk']?></td>
        <td><?echo $report->query_register['mn']?></td>
        <td><?echo $report->query_register['qw']?></td>
        <td><?echo $report->query_register['ye']?></td>
        <td><?echo $report->query_register['yu']?></td>
    </tr>
<table id="query_hist"></table> 
<div id="query_pager"></div>
<script type="text/javascript">
$(function() {
    var grid=$("#query_hist");
    grid.jqGrid( {
        datatype : 'local',
        width : '1000',
        shrinktofit:false,
        height : 'auto',
        mtype : 'GET',
        colNames : ['Номер запроса','Дата запроса','Тип запроса'],
        colModel : [
            {name:'reqID', width:100},
            {name:'reqDateTime', width:100},
            {name:'reqType', width:100}
        ],
        caption : 'История запросов',
        rowNum : 300000,
        pgbuttons: false,     // disable page control like next, back button
        pgtext: null,  
    	loadError: function(xhr, status, error) {alert('status: '+status+" error: "+error)}
    }).navGrid('#query_pager',{search:false, view:false, del:false, add:false, edit:false, refresh:false},
    {}, // default settings for edit
    {}, // default settings for add
    {}, // delete
    {}, // search options
    {}
    ); //, cloneToTop:true});
    var data = <? echo CJavaScript::encode($report->query_hist); ?>;
//    alert(data[0])
//    console.log(data);
    for(var i = 0;i < data.length; i++){
        $('#query_hist').jqGrid('addRowData',i+1,data[i]);
    }
});

</script>
    