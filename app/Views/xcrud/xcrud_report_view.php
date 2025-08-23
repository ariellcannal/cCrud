<?php if($this->get_var('custom_head')!= false)
	require (CCRUD_PATH . '/' . $this->config->themes_path . $this->get_var('custom_head'));?>
	
<?php require $_SERVER['DOCUMENT_ROOT'].'/application/views/xcrud_default/_blocos/replace_title.php';?>
<div class="page-header">
	<?php echo $this->render_table_name($mode,array('tag'=>'h1','class'=>'page-title'),false,$title); ?>
	<p class="page-description">Preencha os parâmetros abaixo:</p>
    <div class="page-header-actions xcrud-top-actions">
    	<div class="btn-group">
    	<?php echo $this->render_button('make_report','make_report','','btn btn-success','');?>
	    </div>
    </div>
</div>
<div class="page-content padding-30 container-fluid">
	<div class="col-md-3 hidden-sm hidden-xs"></div>
	<div class="col-md-6 col-sm-12 col-xs-12">
		<div class="panel">
			<div class="panel-body">
				<div class="xcrud-view">
					<?php
					/* DEFAULTS */
					$container = 'table';
					$row = 'tr';
					$label = 'td';
					$field = 'td';
					$tabs_block = 'div';
					$tabs_head = 'ul';
					$tabs_row = 'li';
					$tabs_link = 'a';
					$tabs_content = 'div';
					$tabs_pane = 'div';
					if($mode == 'view'){
						$container = array('tag'=>'table','class'=>'table');
					}
					else{
						$container = 'div';
						$row = 'div';
						$label = array('tag'=>'label','class'=>'col-md-4');
						$field = array('tag'=>'div','class'=>'col-md-8');
					}
					$tabs_head = array('tag'=>'ul','data-plugin'=>'nav-tabs','role'=>'tablist');
					$tabs_row = array('tag'=>'li','role'=>'presentation');
					$tabs_link = array('tag'=>'a','data-toggle'=>'tab','role'=>'tab');
					echo $this->render_fields_list($mode,$container,$row,$label,$field,$tabs_block,$tabs_head,$tabs_row,$tabs_link,$tabs_content,$tabs_pane);
					?>
				</div>
				<div class="xcrud-nav">
				    <?php echo $this->render_benchmark(); ?>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-3 hidden-sm hidden-xs"></div>
</div>