<?php
$cs = Yii::app()->clientScript;
 
$cs->registerCssFile(Yii::app()->request->baseUrl.'/jqgrid/themes/ui.jqgrid.css');
$cs->registerCssFile(Yii::app()->request->baseUrl.'/jqgrid/themes/redmond/jquery-ui-custom.css');
 
$cs->registerScriptFile(Yii::app()->request->baseUrl.'/jqgrid/js/jquery.js');
$cs->registerScriptFile(Yii::app()->request->baseUrl.'/jqgrid/js/i18n/grid.locale-ru.js');
$cs->registerScriptFile(Yii::app()->request->baseUrl.'/jqgrid/js/jquery.jqGrid.min.js');
$cs->registerScriptFile(Yii::app()->request->baseUrl.'/jqgrid/js/jquery-ui-custom.min.js');
$cs->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.form.js');
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
<?php
    echo '<h2>Список запросов к системе за период с '.$_GET['start_date'].' по '.$_GET['stop_date'].'</h2>';
?>
<table id="list"></table> 
<div id="pager"></div> 

<script type="text/javascript">
    
$(function() {
    var grid=$("#list");
//    var pager_selector = "#pager";
    grid.jqGrid( {
        url: "<?php  echo Yii::app()->createUrl('nativeQuerie/getQueries',array('start_date'=>$_GET['start_date'], 'stop_date'=>$_GET['stop_date']))?>",
        datatype : 'json',
        width : '800',
        height : '400',
        mtype : 'GET',
        colNames : ['ID','Пользователь','ИНН','Описание','Дата'],
        colModel : [
            {name:'id',index:'id', width:20, hidden:true},
            {name:'user',index:'user', width:100},
            {name:'inn',index:'inn', width:60},
            {name:'description',index:'description', width:200},
            {name:'date',index:'date', width:150},
        ],
        caption : 'Запросы',
//        rowNum : 300000,
        pgbuttons: true,     // disable page control like next, back button
//        pgtext: null,  
        rowNum:10,
   	rowList:[10,20,30],
        viewrecords: true,
        pager: '#pager',
        subGrid: true,
        loadonce: true,
        subGridRowExpanded: function (subgridDivId, rowId) {
//            grid.setCell (row,col,val,{background:'#ff0000'});
            var inn = $('#list').getCell(rowId, 'inn');
//            alert('inn='+inn)
            var subgridTableId = subgridDivId + "_t";
            var pager_id = "p_"+subgridTableId;
            var subgrid_pager_selector = pager_id;
            $(".subgrid-cell").css('background','#ddd');
            $(".subgrid-data").css('background','#ddd');
            $("#" + subgridDivId).html("<table id='"+subgridTableId+"' class='scroll'></table><div id='"+ pager_id +"' class='scroll'></div>");
            $("#" + subgridTableId).jqGrid({
                url: "<? echo Yii::app()->createUrl('nativeQuerie/getReportsByInn?inn=') ?>"+inn,
                datatype : 'json',
                height : 'auto',
                width : '700',
//                loadonce:true,
                colNames: ['ID','Stamp','Бюро','Данные от','Получен'],
                colModel: [
                    {name:'id',index:'id', width:20, hidden:true},
                    {name:'stamp',index:'stamp', width:20, hidden:true},
                    {name: 'bureau',index:'bureau', width:80},
                    {name: 'issue', width: 70 },
                    {name: 'created', width: 60 },
                ],
            pager: pager_id,
            caption: 'ИНН '+inn,
            pgbuttons: false,     // disable page control like next, back button
            pgtext: null,  
            viewrecords: false //,
            });
            jQuery("#"+subgridTableId).jqGrid("navGrid","#"+pager_id,{edit:false,add:false,del:false,search:false, refresh:false});
            sg_pager_ButtonAdd = function(options) {
                jQuery("#"+subgridTableId).jqGrid('navButtonAdd',subgrid_pager_selector,options);
            };
            sg_pager_ButtonAdd ({
            caption: '',
            title: 'Просмотреть отчет',
            buttonicon: 'ui-icon-pencil',
            onClickButton: function()
            {
                var n_row = jQuery("#"+subgridTableId).getGridParam('selrow');
                if(n_row) {
                    var stamp = jQuery("#"+subgridTableId).getCell(n_row, 'stamp').replace('#','$');
//                    alert(stamp)
                    var bureau = jQuery("#"+subgridTableId).getCell(n_row, 'bureau');
                    var bureau_id = bureau==='МБКИ'? 3:2;
                    if(stamp>'')
                        window.location.href = "<?echo Yii::app()->createUrl('report/showReportByStamp') ?>"+'?stamp='+stamp;
                    else
                        window.location.href = "<?echo Yii::app()->createUrl('report/showLastReportByBureau') ?>"+'?bureau_id='+bureau_id+'&inn='+inn;
                } else
                    alert('Выберите строку');
            }
            });
            
        },
//        gridComplete: function () {
//            var block_id = $('#list').getCell(rowId, 'id');
//            if(grid.getGridParam("reccount")==1)
//                grid.expandSubGridRow(1); //grid.getDataIDs()); 
//        },
    	loadError: function(xhr, status, error) {alert('status: '+status+" error: "+error)}
    }).navGrid('#pager',{search:false, view:false, del:false, add:false, edit:false, refresh:false},
    {}, // default settings for edit
    {}, // default settings for add
    {}, // delete
    {
//        closeOnEscape: true, multipleSearch: true, 
//       sopt:['cn','eq','ne','bw','bn'],
//         closeAfterSearch: true 
     }, // search options
    {}
    ); //, cloneToTop:true});


//    pager_ButtonAdd = function(options) {
//        grid.jqGrid('navButtonAdd',pager_selector,options);
//    };
//
//    pager_ButtonAdd ({
//    caption: '',
//    title: 'Добавить блок',
//    buttonicon: 'ui-icon-plus',
//    onClickButton: function()
//    {
//        window.location.href = "<?echo Yii::app()->createUrl('direction/addBlock',array('direction_id'=>$direction_id)) ?>";
//    }
//    
//    });
//
//    pager_ButtonAdd ({
//    caption: '',
//    title: 'Изменить веса блоков',
//    buttonicon: 'ui-icon-pencil',
//    onClickButton: function()
//    {
//        window.location.href = "<?echo Yii::app()->createUrl('direction/editBlockWeights',array('direction_id'=>$direction_id)) ?>";
//    }
//    });
//
//    grid.jqGrid('navSeparatorAdd','#pager');
//
//    pager_ButtonAdd ({
//    caption: '',
//    title: 'Добавить показатель',
//    buttonicon: 'ui-icon-plus',
//    onClickButton: function()
//    {
//        var n_row = grid.getGridParam('selrow');
//        if(n_row) {
//            var block_id = grid.getCell(n_row, 'id');
//            window.location.href = '<? echo Yii::app()->createUrl("block/addFactor") ?>'+'?block_id='+block_id;
//        } else
//            alert('Выберите блок показателей');
//    }
//    });
//
//    pager_ButtonAdd ({
//    caption: '',
//    title: 'Изменить веса показателей',
//    buttonicon: 'ui-icon-pencil',
//    onClickButton: function()
//    {
//        var n_row = grid.getGridParam('selrow');
//        if(n_row) {
//            var block_id = grid.getCell(n_row, 'id');
//            var categorized = grid.getCell(n_row, 'categorization');
//            if (categorized == '')
//                window.location.href = "<? echo Yii::app()->createUrl('factor/editFactorWeights')?>"+
//                '?block_id='+block_id+'&category_id=0';
//            else
//                window.location.href = '<? echo Yii::app()->createUrl("direction/getCategory") ?>'+'?block_id='+block_id;
//        }
//        else
//            alert('Выберите блок показателей');
//    }
//    });

});
</script>
