<?php require $_SERVER['DOCUMENT_ROOT'].'/application/views/xcrud_default/_blocos/replace_title.php';?>
<div class="panel-heading">
	<?php echo $this->render_table_name($mode,array('tag'=>'h3','class'=>'panel-title padding-0 padding-bottom-10'),false,$title); ?>
    <div class="page-header-actions">
    	<div class="xcrud-top-actions btn-group">
    	<?php
	    echo $this->render_button('return','list','','btn btn-warning');
	    require $_SERVER['DOCUMENT_ROOT'].'/application/views/xcrud_default/_blocos/buttons_links.php';
	    echo $this->render_button('save_edit','save',($this->get_var('after_task')!="")?$this->get_var('after_task'):'edit','btn btn-success','','create,edit');
	    ?>
	    </div>
    </div>
</div>

		<div class="panel-body container-fluid">
			<div class="xcrud-view">
				<?php echo $mode == 'view' ? $this->render_fields_list($mode,array('tag'=>'table','class'=>'table')) : $this->render_fields_list($mode,'div','div','label','div','div',array('tag'=>'ul','data-plugin'=>'nav-tabs','role'=>'tablist'),array('tag'=>'li','role'=>'presentation'),array('tag'=>'a','data-toggle'=>'tab','role'=>'tab')); ?>
			</div>
			<div class="xcrud-nav">
			    <?php echo $this->render_benchmark(); ?>
			</div>
		</div>
