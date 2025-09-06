<?php if ($cCrud->get_var('custom_head') != false)
        require APPPATH . 'Views/' . ltrim($cCrud->get_var('custom_head'), '/'); ?>
	
<?php require $_SERVER['DOCUMENT_ROOT'].'/application/views/cCrud_default/_blocos/replace_title.php';?>
<?php if($cCrud->is_inner)
        include 'nested/cCrud_detail_view.php';
else{
?>
<div class="page-header">
        <?php echo $cCrud->renderTableName($mode,array('tag'=>'h1','class'=>'page-title'),false,$title); ?>
    <div class="page-header-actions cCrud-top-actions">
        <div class="btn-group">
        <?php
        echo $cCrud->render_button('return','list','','btn btn-warning');
            require $_SERVER['DOCUMENT_ROOT'].'/application/views/cCrud_default/_blocos/buttons_links.php';
            foreach($cCrud->buttons as $k=>$btn){
                //echo $cCrud->render_button($k,$k,'edit',$btn['class'],$btn['icon']);
            }
            echo $cCrud->render_button('save_edit','save',($cCrud->get_var('after_task')!="")?$cCrud->get_var('after_task'):'edit','btn btn-success','','create,edit');
            ?>
            </div>
    </div>
</div>
<div class="page-content padding-30 container-fluid">
<div class="panel">
		<div class="panel-body">
			<div class="cCrud-view">
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
					$label = 'label';
					$field = 'div';
				}
				$tabs_head = array('tag'=>'ul','data-plugin'=>'nav-tabs','role'=>'tablist');
				$tabs_row = array('tag'=>'li','role'=>'presentation');
				$tabs_link = array('tag'=>'a','data-toggle'=>'tab','role'=>'tab');
                                echo $cCrud->render_fields_list($mode,$container,$row,$label,$field,$tabs_block,$tabs_head,$tabs_row,$tabs_link,$tabs_content,$tabs_pane);
                                ?>
                        </div>
                        <div class="cCrud-nav">
                            <?php echo $cCrud->render_benchmark(); ?>
                        </div>
                </div>
        </div>
</div>
<?php }?>
