<?php
$cs = Yii::app()->clientScript;
 
$cs->registerCssFile(Yii::app()->request->baseUrl.'/jqgrid/themes/ui.jqgrid.css');
$cs->registerCssFile(Yii::app()->request->baseUrl.'/jqgrid/themes/redmond/jquery-ui-custom.css');
 
$cs->registerScriptFile(Yii::app()->request->baseUrl.'/jqgrid/js/jquery.js');
$cs->registerScriptFile(Yii::app()->request->baseUrl.'/jqgrid/js/i18n/grid.locale-ru.js');
$cs->registerScriptFile(Yii::app()->request->baseUrl.'/jqgrid/js/jquery.jqGrid.min.js');
$cs->registerScriptFile(Yii::app()->request->baseUrl.'/jqgrid/js/jquery-ui-custom.min.js');
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
</style>

<script type="text/javascript">
   $.jgrid.no_legacy_api = true;
   $.jgrid.useJSON = true;
</script>

<table id="list"></table> 

<script type="text/javascript">
$(function() {
    var grid=$("#list");
    grid.jqGrid( {
        url: "<?php  echo Yii::app()->createUrl('crfield/getDictionaries')?>",
        datatype : 'json',
        width : '650',
        height : 'auto',
        mtype : 'GET',
        colNames : ['ID','Название'],
        colModel : [
            {name:'dictionary_id',index:'dictionary_id', hidden:true},
            {name:'name',index:'name', width:400},
        ],
        caption : 'Справочники',
        rowNum : 300000,
        pgbuttons: false,     // disable page control like next, back button
        pgtext: null,  
        subGrid: true,
        pager: '#pager',
        subGridRowExpanded: function (subgridDivId, rowId) {
            var dictionary_id = $('#list').getCell(rowId, 'dictionary_id');
            var dictionary_name = $('#list').getCell(rowId, 'name');
            var url = '';
            if (dictionary_id == 0)
                url = "<? echo Yii::app()->createUrl('bureau/getBureaus')?>"
            else
                url = "<? echo Yii::app()->createUrl('crField/getFields')?>"
            var subgridTableId = subgridDivId + "_t";
            var pager_id = "p_"+subgridTableId;
//            var subgrid_pager_selector = pager_id;
            $(".subgrid-cell").css('background','#ddd');
            $(".subgrid-data").css('background','#ddd');
            $("#" + subgridDivId).html("<table id='"+subgridTableId+"' class='scroll'></table><div id='"+pager_id+"' class='scroll'></div>");
            $("#" + subgridTableId).jqGrid({
                caption : '',
                url: url,
                editurl: "<? echo Yii::app()->createUrl('crField/createField')?>",
                datatype : 'json',
                height : 'auto',
                width : '600',
                mtype : 'GET',
//                postData:{'direction_id': direction_id},
//                loadonce:true,
                colNames: ['ID','Название'],
                colModel: [
                    {name:'id',index:'id', width:20, hidden:true},
                    {name: 'name',index:'name', width:200, 'editable':true},
//                    {name: 'division',index:'division', width:200},
                ],
            pager: pager_id,
//            sortname: 'division',
//            sortorder: 'asc',
            rowNum:300000,
            rownumbers: true,
            pgbuttons: false,     // disable page control like next, back button
            pgtext: null,  
            viewrecords: false //,
            });
            jQuery("#"+subgridTableId).jqGrid("navGrid","#"+pager_id,{edit:false,add:true,del:true,search:false, refresh:false},
            {}, // default settings for edit
            {   closeAfterAdd:true,
                width:'auto',
                recreateForm:true
            }, // default settings for add
            {               
                errorTextFormat : function(response){
                    return '. '+response.responseText;
                }, 
                onclickSubmit:function(params,postdata){
                    var sel_ = $("#" + subgridTableId).getGridParam('selrow');
                    if(sel_) {
                        var field_id = $("#" + subgridTableId).getCell(sel_, 'id');
                        params.url='<?echo Yii::app()->createUrl('crField/delete?field_id=');?>'+field_id;
                    }
                },
                afterSubmit:function(responce,postdata){
                    if (responce.status='deleted')
                        return[true]
                    else 
                        return[false,,responce.message];
                } //,
//            serializeDelData:function(postdata){
//                    var sel_ = $("#" + subgridTableId).getGridParam('selrow');
//                    if(sel_) {
//                        postdata.iddb=$("#" + subgridTableId).getCell(sel_, 'fixation_id');
//                    }
//                    return postdata;
//            }
            }, // delete
            {}, // search options
            {});

//            sub_pager_ButtonAdd = function(options) {
//                grid.jqGrid('navButtonAdd',subgrid_pager_selector,options);
//            };
//            sub_pager_ButtonAdd ({
//            caption: '',
//            title: '����������� KPI',
//            buttonicon: 'ui-icon-document',
//            onClickButton: function(){
//                var n_row = jQuery("#"+subgridTableId).getGridParam('selrow');
//                if(n_row) {
//                    var region_id = jQuery("#"+subgridTableId).getCell(n_row, 'id');
//                    window.location.href = "<?echo Yii::app()->createUrl('performance/showKpiJq').'?period_id='.$_GET['period_id'].'&direction_id=' ?>"+direction_id+
//                        "<?echo $_GET['level_id']==2 ? '&division_id=':'&worker_id='?>"+region_id+"<?echo $_GET['level_id']==5 ? '&division_id=998':''?>";
//                }else
//                    alert('�������� '+"<?echo $_GET['level_id']==2 ? '��������': ($_GET['level_id']==4 ? '����������':'��������')?>");
//            }
//            });
        },
//        gridComplete: function () {
//            if(grid.getGridParam("reccount")==1)
//                grid.expandSubGridRow(grid.getDataIDs()); 
//            if(grid.getGridParam("reccount")==0){
//                alert("��� ������� ��� �� ����������");
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
