<?php
if ($this->get_var('custom_head') != false)
        require APPPATH . 'Views/' . ltrim($this->get_var('custom_head'), '/');
?>
<script>
$(document).ready(function(){
    $('#inventario '+$('#inventario').data('wizard').options.buttonsAppendTo).html('');
    $('#inventario').data('wizard').setup(); 
});
</script>
<div class="panel panel-bordered <? echo $this->get_var ( 'wizard-active' )?>" role="tabpanel" aria-expanded="true">
	<div class="panel-toolbar" role="toolbar">
		<div class="btn-group btn-group-flat pull-right" role="group">
              <?php echo $this->add_button('btn btn-success waves-effect waves-light','icon wb-plus'); ?>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="table-responsive">
		<table class="table table-hover table-striped" data-selectable="selectable">
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
	
	</div>
</div>