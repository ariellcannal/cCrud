<?php if ($this->get_var('custom_head') != false)
        require APPPATH . 'Views/' . ltrim($this->get_var('custom_head'), '/'); ?>
<?php if($this->is_inner)
	include 'nested/xcrud_list_view.php';
else{
?>
<div class="page-header">
	<?php echo $this->render_table_name('list',array('tag'=>'h1','class'=>'page-title')); ?>
	<div class="page-header-actions">
			<?php echo $this->render_totalizers(false)?>
			<?php echo $this->render_custom_filter('direita')?>
	</div>
</div>
		<div class="page-content">
			<div class="panel">
				<div class="panel-heading">
					<?php if ($this->is_create or $this->is_csv or $this->is_search or $this->is_print){?>
				        <div class="xcrud-top-actions">
							<div class="pull-left">
								<?php echo $this->add_button('btn btn-success','icon wb-plus'); ?>
								<?php echo $this->render_mass_actions();?>
				        	</div>
							<div class="pull-right">
					        	<?php echo $this->render_search(); ?>
					            <div class="btn-group">
					                <?php
									echo $this->print_button ( 'btn btn-default btn-outline', 'icon wb-print' );
									echo $this->csv_button ( 'btn btn-default btn-outline', 'icon wb-file' );
									echo $this->refresh_button ( 'btn btn-default btn-outline', 'icon wb-sync' );
									?>
					            </div>
							</div>
							<div class="clearfix"></div>
						</div>
					<?php } ?>
				</div>
				<div class="pull-left col-md-10">
					<?php echo $this->render_mass_edit_form();?>
					<?php echo $this->render_alphabetical_filter();?>
				</div>
				<div class="pull-right col-md-2">
					<?php echo $this->render_columns_select();?>
				</div>
				<div class="xcrud-list-container table-responsive">
			        <table class="xcrud-list table table-striped table-hover table-condensed table-responsive" data-selectable="selectable" data-row-selectable="true">
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
		</div>
<?php 
}?>