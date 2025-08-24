<div class="panel-heading padding-0">
	<?php echo $this->render_table_name('list',array('tag'=>'h3','class'=>'panel-title padding-0 padding-bottom-10')); ?>
</div>
<div class="panel-body container-fluid padding-0">
	<?php if ($this->is_create or $this->is_csv or $this->is_print){?>
	<div class="xcrud-top-actions">
		<div class="pull-left">
    		<?php echo $this->add_button('btn btn-success','icon wb-plus'); ?>
    	</div>
    	<div class="pull-right">
        	<?php echo $this->render_search(); ?>
            <div class="btn-group">
                <?php
            		echo $this->print_button ( 'btn btn-default btn-outline', 'icon wb-print' );
            		echo $this->csv_button ( 'btn btn-default btn-outline', 'icon wb-file' );
		          ?>
            </div>
		</div>
		<div class="clearfix"></div>
	</div>
	<?php } ?>
	<div class="xcrud-list-container">
		<?php echo $this->render_alphabetical_filter();?><br/>
		<?php echo $this->render_custom_filter('direita')?>
        <table
			class="xcrud-list table table-striped table-hover table-condensed">
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