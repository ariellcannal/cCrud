<?php
if ($this->get_var ( 'custom_head' ) != false)
	require (CCRUD_PATH . '/' . \cCrud\Config\cCrudConfig::$themes_path . $this->get_var ( 'custom_head' ));
?>
<div class="panel margin-15">
	<div class="panel-heading">
		<?php echo $this->render_table_name('list',array('tag'=>'h3','class'=>'panel-title padding-0')); ?>
	</div>
	<?php if ($this->is_mass_remove || count($this->mass_edit)) {?>
	<div class="panel-body">
		<?php echo $this->render_mass_edit_actions();?>
		<?php echo $this->render_mass_edit_form();?>
	</div>
	<?php }?>
	<div class="xcrud-list-container table-responsive">
		<table class="table table-hover table-striped" data-selectable="selectable" data-row-selectable="true">
			<thead>
                <?php echo $this->render_grid_head('tr', 'th', array('asc' =>'<i class="icon wb-triangle-up"></i>', 'desc' => '<i class="icon wb-triangle-down"></i>')); ?>
            </thead>
			<tbody>
                <?php echo $this->render_grid_body('tr', 'td'); ?>
            </tbody>
			<tfoot>
                <?php echo $this->render_grid_footer('tr', 'td'); ?>
            </tfoot>
		</table>
	</div>
	<div class="panel-footer">
    	<div class="pull-right"><?php echo $this->render_pagination(7,1); ?></div>
		<div class="pull-right"><?php echo $this->render_limitlist(25,true); ?></div>
		<div class="clearfix"></div>
	</div>
</div>