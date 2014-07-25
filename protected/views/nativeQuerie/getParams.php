<h1>Задайте параметры отбора</h1>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'analyze-form',
	'enableClientValidation'=>false,
	'clientOptions'=>array(
		'validateOnSubmit'=>false,
	),
)); ?>
    <br>
        <div class="row">
            <b>Начало интервала: </b>
            <br>
                <?php
                $this->widget('zii.widgets.jui.CJuiDatePicker',array(
                    'name' => 'start_date',
                    'model' => $model,
                    'attribute' => 'start_date',
                    // additional javascript options for the date picker plugin
                    'options'=>array(
                        'showAnim'=>'fold',//'slide','fold','slideDown','fadeIn','blind','bounce','clip','drop'
//                        'showButtonPanel'=>true,
                //        'buttonImage'=>"../images/calendar.png",
                //        'buttonImageOnly'=>true,
                        'showOn'=>"both",
                    ),
                    'language'=>'ru',
                    'htmlOptions'=>array(
                        'style'=>'height:20px;',
                    ),
                ));
                ?>
		
		<?php echo $form->error($model,'start_date'); ?>
	</div>
    <div class="row">
            <b>Конец интервала: </b>
            <br>
                <?php
                $this->widget('zii.widgets.jui.CJuiDatePicker',array(
                    'name' => 'stop_date',
                    'model' => $model,
                    'attribute' => 'stop_date',
                    // additional javascript options for the date picker plugin
                    'options'=>array(
                        'showAnim'=>'fold',//'slide','fold','slideDown','fadeIn','blind','bounce','clip','drop'
//                        'showButtonPanel'=>true,
                //        'buttonImage'=>"../images/calendar.png",
                //        'buttonImageOnly'=>true,
                        'showOn'=>"both",
                    ),
                    'language'=>'ru',
                    'htmlOptions'=>array(
                        'style'=>'height:20px;',
                    ),
                ));
                ?>
		<?php echo $form->error($model,'stop_date'); ?>
	</div>
 
    <div class="row buttons">
        <?php echo CHtml::submitButton('Продолжить',array('name'=>'my_button')); ?>
    </div>
<?php $this->endWidget(); ?>
</div><!-- form -->
