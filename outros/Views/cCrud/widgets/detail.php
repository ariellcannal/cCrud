<?php if ($this->get_var('custom_head') != false)
        require APPPATH . 'Views/' . ltrim($this->get_var('custom_head'), '/'); ?>
	
<?php require $_SERVER['DOCUMENT_ROOT'].'/application/views/cCrud_default/_blocos/replace_title.php';?>
<div class="panel">
	<div class="panel-heading">
		<?php echo $this->renderTableName($mode,array('tag'=>'h3','class'=>'panel-title pull-left padding-0 padding-bottom-10'),false,$title); ?>
		<div class="btn-group pull-right margin-15">
	    	<?php
		    echo $this->render_button('return','list','','btn btn-warning');
		    require $_SERVER['DOCUMENT_ROOT'].'/application/views/cCrud_default/_blocos/buttons_links.php';
		    echo $this->render_button('save_edit','save',($this->get_var('after_task')!="")?$this->get_var('after_task'):'edit','btn btn-success','','create,edit');
		    ?>
	    </div>
	    <div class="clearfix"></div>
	</div>
	<div class="panel-body">
		<?php echo $mode == 'view' ? $this->render_fields_list($mode,array('tag'=>'table','class'=>'table')) : $this->render_fields_list($mode,'div','div','label','div','div',array('tag'=>'ul','data-plugin'=>'nav-tabs','role'=>'tablist'),array('tag'=>'li','role'=>'presentation'),array('tag'=>'a','data-toggle'=>'tab','role'=>'tab')); ?>
	</div>
</div>