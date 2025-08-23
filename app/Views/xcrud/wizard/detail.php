<?php if($this->get_var('custom_head')!= false)
	require (CCRUD_PATH . '/' . $this->config->themes_path . $this->get_var('custom_head'));?>
	
<?php require $_SERVER['DOCUMENT_ROOT'].'/application/views/xcrud_default/_blocos/replace_title.php';?>
<div class="panel panel-bordered wizard-pane active" role="tabpanel" aria-expanded="true">
	<div class="panel-toolbar" role="toolbar">
		<div class="btn-group btn-group-flat pull-right" role="group">
            <?php
		    echo $this->render_button('return','list','','btn btn-warning waves-effect waves-light');
		    echo $this->render_button('save_edit','save',($this->get_var('after_task')!="")?$this->get_var('after_task'):'edit','btn btn-success waves-effect waves-light','','create,edit');
		    ?>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="panel-body">
		<?php echo $mode == 'view' ? $this->render_fields_list($mode,array('tag'=>'table','class'=>'table')) : $this->render_fields_list($mode,'div','div','label','div','div',array('tag'=>'ul','data-plugin'=>'nav-tabs','role'=>'tablist'),array('tag'=>'li','role'=>'presentation'),array('tag'=>'a','data-toggle'=>'tab','role'=>'tab')); ?>
	</div>
</div>